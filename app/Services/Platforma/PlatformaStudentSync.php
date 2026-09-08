<?php

namespace App\Services\Platforma;

use App\Models\Fakultet;
use App\Models\NivoStudija;
use App\Models\Student;

/**
 * Preslikava studenta sa platforme u lokalnu tabelu studenti.
 * Lokalni zapis je keš/referenca, platforma je izvor istine.
 */
class PlatformaStudentSync
{
    private const GODINE = ['prva' => 1, 'druga' => 2, 'treća' => 3, 'treca' => 3, 'četvrta' => 4, 'cetvrta' => 4, 'peta' => 5, 'šesta' => 6, 'sesta' => 6];

    public function __construct(private readonly PlatformaClient $client)
    {
    }

    /**
     * Kreira ili osvježava lokalnog studenta na osnovu ID-ja sa platforme.
     */
    public function povezi(int $platformaStudentId, ?string $status = null, ?int $upisId = null): Student
    {
        $podaci = $this->client->student($platformaStudentId);

        return $this->upsert($podaci, $status, null, $upisId);
    }

    /**
     * Upis po ID-ju iz liste upisa studenta, ili aktivni upis ako ID nije dat / ne postoji.
     */
    public function izaberiUpis(array $p, ?int $upisId): ?array
    {
        if ($upisId) {
            foreach ($p['upisi'] ?? [] as $u) {
                if ((int) $u['id'] === $upisId) {
                    return $u;
                }
            }
            if (isset($p['aktivni_upis']['id']) && (int) $p['aktivni_upis']['id'] === $upisId) {
                return $p['aktivni_upis'];
            }
        }

        return $p['aktivni_upis'] ?? null;
    }

    /**
     * Osvježava već povezanog studenta.
     */
    public function osvjezi(Student $student): Student
    {
        if (!$student->platforma_student_id) {
            return $student;
        }

        return $this->upsert($this->client->student((int) $student->platforma_student_id), null, $student, $student->platforma_upis_id ? (int) $student->platforma_upis_id : null);
    }

    /**
     * Polja koja se popunjavaju iz platforminog zapisa (koristi i JS za autofill).
     */
    public function mapiraj(array $p, ?array $upis = null): array
    {
        $upis ??= $p['aktivni_upis'] ?? null;

        return [
            'platforma_student_id' => (int) $p['id'],
            'platforma_upis_id' => $upis['id'] ?? null,
            'ime' => $p['ime'] ?? '',
            'prezime' => $p['prezime'] ?? '',
            'jmbg' => $p['jmbg'] ?? null,
            'email' => filter_var($p['email'] ?? '', FILTER_VALIDATE_EMAIL) ? $p['email'] : null,
            'telefon' => $p['telefon'] ?? null,
            'pol' => $this->pol($p['pol'] ?? null),
            'br_indexa' => $upis['broj_indeksa'] ?? null,
            'godina_studija' => $this->godinaStudija($upis['godina_studija'] ?? null),
            'nivo_studija_id' => $this->nivoStudijaId($upis['nivo_studija'] ?? null),
            'nivo_studija_naziv' => $upis['nivo_studija'] ?? null,
            'platforma_fakultet_id' => $upis['fakultet_id'] ?? null,
            'platforma_fakultet' => $upis['fakultet_skraceno'] ?? null,
            'platforma_akademska_godina_id' => $upis['akademska_godina_id'] ?? null,
            'platforma_akademska_godina' => $upis['akademska_godina'] ?? null,
            'platforma_nivo_studija_id' => $upis['nivo_studija_id'] ?? null,
            'platforma_prepis' => (bool) ($upis['prepis'] ?? false),
        ];
    }

    private function upsert(array $p, ?string $status, ?Student $student = null, ?int $upisId = null): Student
    {
        $m = $this->mapiraj($p, $this->izaberiUpis($p, $upisId));

        $student ??= Student::where('platforma_student_id', $m['platforma_student_id'])
                ->where('platforma_upis_id', $m['platforma_upis_id'])->first()
            // ručno unesen student sa istim JMBG-om koji još nije povezan
            ?? ($m['jmbg'] ? Student::where('jmbg', $m['jmbg'])->whereNull('platforma_student_id')->first() : null)
            ?? new Student();

        $student->fill([
            'platforma_student_id' => $m['platforma_student_id'],
            'platforma_upis_id' => $m['platforma_upis_id'],
            'platforma_synced_at' => now(),
            'ime' => $m['ime'],
            'prezime' => $m['prezime'],
            'jmbg' => $this->jmbgZaZapis($student, $m),
            'email' => $this->emailZaZapis($student, $m['email']),
            'telefon' => $m['telefon'],
            'pol' => $m['pol'],
            'br_indexa' => $m['br_indexa'] ?: ($student->br_indexa ?: 'P-' . $m['platforma_student_id']),
            'godina_studija' => $m['godina_studija'] ?? $student->godina_studija ?? 1,
            'nivo_studija_id' => $m['nivo_studija_id'] ?? $student->nivo_studija_id ?? NivoStudija::query()->value('id'),
        ]);

        if ($status && in_array($status, ['mobilnost', 'prepis'], true)) {
            $student->status = $status;
        } elseif (!$student->status) {
            // Student koji je na platformi upisan kao prepis ide u prepis, ostali u mobilnost.
            $student->status = $m['platforma_prepis'] ? 'prepis' : 'mobilnost';
        }

        $student->save();

        $lokalniFakultet = $m['platforma_fakultet_id']
            ? Fakultet::where('platforma_fakultet_id', $m['platforma_fakultet_id'])->first()
            : null;

        if ($lokalniFakultet) {
            $student->fakulteti()->syncWithoutDetaching([$lokalniFakultet->id]);
        }

        return $student;
    }

    /**
     * studenti.jmbg je unique, a isti student može imati više lokalnih zapisa (po upisu).
     * Prvi zapis dobija pravi JMBG, ostali JMBG sa sufiksom upisa.
     */
    private function jmbgZaZapis(Student $student, array $m): string
    {
        $jmbg = $m['jmbg'] ?: ($student->jmbg ?: 'P' . $m['platforma_student_id']);

        $zauzet = Student::where('jmbg', $jmbg)
            ->when($student->exists, fn ($q) => $q->where('id', '!=', $student->id))
            ->exists();

        return $zauzet ? $jmbg . '-' . ($m['platforma_upis_id'] ?? 'x') : $jmbg;
    }

    /**
     * studenti.email je unique; drugi zapis istog studenta (drugi upis) ostaje bez emaila.
     */
    private function emailZaZapis(Student $student, ?string $email): ?string
    {
        if (!$email) {
            return null;
        }

        $zauzet = Student::where('email', $email)
            ->when($student->exists, fn ($q) => $q->where('id', '!=', $student->id))
            ->exists();

        return $zauzet ? null : $email;
    }

    private function pol(?string $pol): string
    {
        $v = mb_strtolower(trim((string) $pol));

        return str_starts_with($v, 'z') || str_starts_with($v, 'ž') ? 'zensko' : 'musko';
    }

    private function godinaStudija(?string $naziv): ?int
    {
        if ($naziv === null) {
            return null;
        }
        $k = mb_strtolower(trim($naziv));

        return self::GODINE[$k] ?? (is_numeric($k) ? (int) $k : null);
    }

    private function nivoStudijaId(?string $naziv): ?int
    {
        if (!$naziv) {
            return null;
        }

        return NivoStudija::firstOrCreate(['naziv' => trim($naziv)])->id;
    }
}
