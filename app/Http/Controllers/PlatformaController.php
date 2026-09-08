<?php

namespace App\Http\Controllers;

use App\Models\MappingRequest;
use App\Models\Mobilnost;
use App\Models\Student;
use App\Services\Platforma\PlatformaClient;
use App\Services\Platforma\PlatformaException;
use App\Services\Platforma\PlatformaPredmetSync;
use App\Services\Platforma\PlatformaStudentSync;
use App\Services\Platforma\PlatformaWriteback;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Most ka studentskoj platformi: pretraga i povezivanje studenata,
 * sinhronizacija matičnih predmeta, slanje priznatih ispita.
 */
class PlatformaController extends Controller
{
    public function __construct(
        private readonly PlatformaClient $client,
        private readonly PlatformaStudentSync $studentSync,
        private readonly PlatformaPredmetSync $predmetSync,
        private readonly PlatformaWriteback $writeback,
    ) {
    }

    /** GET /admin/platforma/status */
    public function status(): JsonResponse
    {
        if (!$this->client->enabled()) {
            return response()->json(['enabled' => false, 'ok' => false, 'message' => 'Integracija nije konfigurisana.']);
        }

        try {
            $ping = $this->client->ping();

            return response()->json(['enabled' => true, 'ok' => true, 'writeback' => $this->writeback->enabled()] + $ping);
        } catch (PlatformaException $e) {
            return response()->json(['enabled' => true, 'ok' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * GET /admin/platforma/studenti?q=
     * Rezultat sa platforme + mapirana lokalna polja + lokalni id ako je već povezan.
     */
    public function pretraga(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        try {
            $rezultati = $this->client->pretragaStudenata($q);
        } catch (PlatformaException $e) {
            return response()->json(['message' => $e->getMessage()], 502);
        }

        $platformaIds = array_column($rezultati, 'id');
        $lokalni = Student::whereIn('platforma_student_id', $platformaIds)->get()
            ->keyBy(fn ($s) => $s->platforma_student_id . '-' . ($s->platforma_upis_id ?? 'x'));

        // Jedan red po upisu: isti student može biti na osnovnim i na masteru.
        $out = [];
        foreach ($rezultati as $p) {
            $upisi = !empty($p['upisi']) ? $p['upisi'] : [$p['aktivni_upis'] ?? null];
            foreach ($upisi as $upis) {
                $m = $this->studentSync->mapiraj($p, $upis);
                $kljuc = $m['platforma_student_id'] . '-' . ($m['platforma_upis_id'] ?? 'x');
                $out[] = $m + [
                    'kljuc' => $kljuc,
                    'lokalni_id' => $lokalni[$kljuc]->id ?? null,
                    'lokalni_status' => $lokalni[$kljuc]->status ?? null,
                    'upisan' => $p['upisan'] ?? null,
                ];
            }
        }

        return response()->json($out);
    }

    /**
     * POST /admin/platforma/studenti/{platformaId}/povezi  {status?: mobilnost|prepis}
     * Kreira/osvježava lokalnog studenta i vraća ga.
     */
    public function povezi(Request $request, int $platformaId): JsonResponse
    {
        $status = $request->input('status');

        try {
            $upisId = $request->filled('upis_id') ? (int) $request->input('upis_id') : null;
            $student = $this->studentSync->povezi($platformaId, in_array($status, ['mobilnost', 'prepis'], true) ? $status : null, $upisId);
        } catch (PlatformaException $e) {
            return response()->json(['message' => $e->getMessage()], 502);
        }

        return response()->json($student->fresh(['nivoStudija', 'fakulteti']));
    }

    /** POST /admin/platforma/studenti/{id}/osvjezi  (lokalni id) */
    public function osvjezi(int $id): RedirectResponse
    {
        $student = Student::findOrFail($id);

        try {
            $this->studentSync->osvjezi($student);
        } catch (PlatformaException $e) {
            return back()->with('error', 'Osvježavanje sa platforme nije uspjelo: ' . $e->getMessage());
        }

        return back()->with('success', 'Podaci studenta osvježeni sa platforme.');
    }

    /** POST /admin/platforma/sync-predmeti */
    public function syncPredmeti(Request $request): RedirectResponse
    {
        $samo = $request->filled('fakultet_id') ? \App\Models\Fakultet::findOrFail((int) $request->fakultet_id) : null;

        try {
            $r = $this->predmetSync->sinhronizuj($request->filled('akademska_godina_id') ? (int) $request->akademska_godina_id : null, $samo);
        } catch (PlatformaException $e) {
            return back()->with('error', 'Sinhronizacija predmeta nije uspjela: ' . $e->getMessage());
        }

        return back()->with('success', 'Predmeti sinhronizovani sa platformom (' . implode(', ', $r['fakulteti']) . "): kreirano {$r['kreirano']}, ažurirano {$r['azurirano']}.");
    }

    /** POST /admin/platforma/mobilnost/{id}/posalji */
    public function posaljiMobilnost(int $id): RedirectResponse
    {
        $mobilnost = Mobilnost::findOrFail($id);

        if (!$this->writeback->enabled()) {
            return back()->with('error', 'Slanje u platformu je isključeno (PLATFORMA_WRITEBACK_ENABLED).');
        }

        $ok = $this->writeback->posaljiMobilnost($mobilnost);

        return back()->with($ok ? 'success' : 'error', $ok
            ? 'Priznati ispiti su poslati u karton studenta na platformi.'
            : 'Slanje nije uspjelo: ' . $mobilnost->fresh()->platforma_greska);
    }

    /** POST /admin/platforma/prepis/{id}/posalji */
    public function posaljiPrepis(int $id): RedirectResponse
    {
        $zahtjev = MappingRequest::findOrFail($id);

        if (!$this->writeback->enabled()) {
            return back()->with('error', 'Slanje u platformu je isključeno (PLATFORMA_WRITEBACK_ENABLED).');
        }

        if ($zahtjev->status !== 'accepted') {
            return back()->with('error', 'Zahtjev nije prihvaćen, nema šta da se šalje.');
        }

        $ok = $this->writeback->posaljiPrepis($zahtjev);

        return back()->with($ok ? 'success' : 'error', $ok
            ? 'Priznati ispiti su poslati u karton studenta na platformi.'
            : 'Slanje nije uspjelo: ' . $zahtjev->fresh()->platforma_greska);
    }
}
