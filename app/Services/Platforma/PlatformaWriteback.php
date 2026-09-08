<?php

namespace App\Services\Platforma;

use App\Models\MappingRequest;
use App\Models\Mobilnost;
use App\Models\Student;
use Illuminate\Database\Eloquent\Model;
use Throwable;

/**
 * Šalje priznate ispite u karton studenta na platformi (student_pfs).
 * Sinhrono, rezultat se čuva na samom zapisu (platforma_poslato_at / platforma_greska).
 */
class PlatformaWriteback
{
    private const OCJENE = [10 => 'A', 9 => 'B', 8 => 'C', 7 => 'D', 6 => 'E'];

    public function __construct(private readonly PlatformaClient $client)
    {
    }

    public function enabled(): bool
    {
        return $this->client->enabled() && (bool) config('platforma.writeback');
    }

    /**
     * Mobilnost: ocjene iz learning agreement-a za matične predmete.
     */
    public function posaljiMobilnost(Mobilnost $mobilnost): bool
    {
        $mobilnost->loadMissing(['student', 'fakultet', 'learningAgreements.fitPredmet']);

        $predmeti = [];
        foreach ($mobilnost->learningAgreements as $la) {
            $ocjena = $this->ocjena($la->ocjena);
            if ($ocjena === null || !$la->fitPredmet) {
                continue;
            }
            // Više stranih predmeta može biti vezano za isti matični; uzima se poslednja ocjena.
            $predmeti[$la->fitPredmet->id] = ['predmet' => $la->fitPredmet, 'ocjena' => $ocjena, 'bodovi' => $la->bodovi];
        }

        return $this->posalji($mobilnost, $mobilnost->student, 'mobilnost', $predmeti, [
            'strani_fakultet' => $mobilnost->fakultet?->naziv,
            'referenca' => 'mobilnost:' . $mobilnost->id,
        ]);
    }

    /**
     * Prepis: uparivanja koje su profesori prihvatili, ocjena sa stranog predmeta.
     */
    public function posaljiPrepis(MappingRequest $zahtjev): bool
    {
        $zahtjev->loadMissing(['student.predmeti', 'fakultet', 'subjects.fitPredmet']);

        $predmeti = [];
        foreach ($zahtjev->subjects as $s) {
            if ($s->is_rejected || !$s->fitPredmet) {
                continue;
            }
            $strani = $zahtjev->student->predmeti->firstWhere('id', $s->strani_predmet_id);
            $ocjena = $this->ocjena($strani?->pivot?->grade);
            if ($ocjena === null) {
                continue;
            }
            $predmeti[$s->fitPredmet->id] = ['predmet' => $s->fitPredmet, 'ocjena' => $ocjena, 'bodovi' => null];
        }

        return $this->posalji($zahtjev, $zahtjev->student, 'prepis', $predmeti, [
            'strani_fakultet' => $zahtjev->fakultet?->naziv,
            'referenca' => 'prepis:' . $zahtjev->id,
        ]);
    }

    /**
     * @param array<int, array{predmet:\App\Models\Predmet, ocjena:string, bodovi?:float|null}> $predmeti
     */
    private function posalji(Model $zapis, ?Student $student, string $izvor, array $predmeti, array $meta): bool
    {
        $greska = null;

        if (!$student || !$student->platforma_student_id || !$student->platforma_upis_id) {
            $greska = 'Student nije povezan sa platformom (nema platforma_student_id / upis).';
        } elseif (empty($predmeti)) {
            $greska = 'Nema predmeta sa ocjenom za slanje.';
        } else {
            $nepovezani = collect($predmeti)->filter(fn ($p) => !$p['predmet']->platforma_pfs_id)->map(fn ($p) => $p['predmet']->naziv);
            if ($nepovezani->isNotEmpty()) {
                $greska = 'Predmeti nisu povezani sa platformom (pokreni sinhronizaciju predmeta): ' . $nepovezani->implode(', ');
            }
        }

        if ($greska) {
            $this->zabiljezi($zapis, null, $greska);

            return false;
        }

        $payload = collect($predmeti)->map(fn ($p) => array_filter([
            'predmet_fakultet_semestar_id' => (int) $p['predmet']->platforma_pfs_id,
            'ocjena' => $p['ocjena'],
            'bodovi' => isset($p['bodovi']) && $p['bodovi'] !== '' ? (float) $p['bodovi'] : null,
        ], fn ($v) => $v !== null))->values()->all();

        try {
            $odgovor = $this->client->priznajIspite(
                (int) $student->platforma_student_id,
                (int) $student->platforma_upis_id,
                $izvor,
                $payload,
                array_filter($meta)
            );
            $this->zabiljezi($zapis, $odgovor, null);

            return true;
        } catch (Throwable $e) {
            $this->zabiljezi($zapis, null, $e->getMessage());

            return false;
        }
    }

    private function zabiljezi(Model $zapis, ?array $odgovor, ?string $greska): void
    {
        $zapis->forceFill([
            'platforma_poslato_at' => $odgovor ? now() : $zapis->platforma_poslato_at,
            'platforma_odgovor' => $odgovor ? json_encode($odgovor, JSON_UNESCAPED_UNICODE) : $zapis->platforma_odgovor,
            'platforma_greska' => $greska,
        ])->save();
    }

    /**
     * Normalizuje ocjenu na slovo A–E (platforma čuva slova u krajnja_ocjena).
     */
    private function ocjena(mixed $v): ?string
    {
        if ($v === null || $v === '') {
            return null;
        }
        $v = trim((string) $v);

        if (is_numeric($v)) {
            return self::OCJENE[(int) round((float) $v)] ?? null;
        }

        $slovo = strtoupper(substr($v, 0, 1));

        return preg_match('/^[A-E]$/', $slovo) ? $slovo : null;
    }
}
