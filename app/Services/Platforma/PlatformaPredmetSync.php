<?php

namespace App\Services\Platforma;

use App\Models\Fakultet;
use App\Models\NivoStudija;
use App\Models\Predmet;

/**
 * Preslikava matične predmete platforme (predmet_fakultet_semestar) u lokalnu tabelu predmeti.
 * Lokalni FK-ovi (learning_agreements, mapping_request_subjects) ostaju netaknuti;
 * veza ka platformi je predmeti.platforma_pfs_id.
 */
class PlatformaPredmetSync
{
    public function __construct(private readonly PlatformaClient $client)
    {
    }

    /**
     * Sinhronizuje predmete svih lokalnih fakulteta povezanih sa platformom (fakulteti.platforma_fakultet_id),
     * ili samo jednog ako je dat lokalni fakultet. Akademska godina: data ili poslednja na platformi.
     * @return array{kreirano:int, azurirano:int, akademska_godina_id:int, fakulteti:array<string>}
     */
    public function sinhronizuj(?int $akademskaGodinaId = null, ?Fakultet $samo = null): array
    {
        $fakulteti = $samo
            ? collect([$samo])
            : Fakultet::whereNotNull('platforma_fakultet_id')->get();

        if ($fakulteti->isEmpty()) {
            throw new PlatformaException('Nijedan fakultet nije povezan sa platformom. Poveži matični fakultet u formi fakulteta.');
        }

        $kreirano = 0;
        $azurirano = 0;
        $godina = 0;
        $imena = [];

        foreach ($fakulteti as $lokalni) {
            if (!$lokalni->platforma_fakultet_id) {
                throw new PlatformaException("Fakultet '{$lokalni->naziv}' nije povezan sa platformom.");
            }

            $odgovor = $this->client->predmetiFakulteta((int) $lokalni->platforma_fakultet_id, null, $akademskaGodinaId);
            $godina = (int) ($odgovor['akademska_godina_id'] ?? $godina);
            $imena[] = $lokalni->naziv;

            foreach ($odgovor['predmeti'] ?? [] as $row) {
                $predmet = $this->upsert($row, $lokalni);
                $predmet->wasRecentlyCreated ? $kreirano++ : $azurirano++;
            }
        }

        return ['kreirano' => $kreirano, 'azurirano' => $azurirano, 'akademska_godina_id' => $godina, 'fakulteti' => $imena];
    }

    /**
     * Vraća lokalni Predmet za jedan red sa platforme (kreira ako ne postoji).
     */
    public function lokalniPredmet(array $row): Predmet
    {
        return $this->upsert($row, $this->lokalniFakultet((int) $row['fakultet_id']));
    }

    private function upsert(array $row, Fakultet $lokalni): Predmet
    {
        $predmet = Predmet::where('platforma_pfs_id', $row['predmet_fakultet_semestar_id'])->first();

        if (!$predmet) {
            // Isti predmet iz ranije akademske godine (novi pfs id): zadrži jedan lokalni red, prebaci ga na najnoviji pfs.
            $predmet = Predmet::where('fakultet_id', $lokalni->id)
                ->where('platforma_predmet_id', $row['predmet_id'])
                ->orderByDesc('platforma_pfs_id')
                ->first();
        }

        if (!$predmet) {
            // Postojeći ručno uneseni predmet istog naziva na istom fakultetu: poveži ga umjesto dupliranja.
            $predmet = Predmet::where('fakultet_id', $lokalni->id)
                ->whereNull('platforma_pfs_id')
                ->whereRaw('LOWER(naziv) = ?', [mb_strtolower(trim($row['naziv']))])
                ->first() ?? new Predmet();
        }

        $predmet->fill([
            'fakultet_id' => $lokalni->id,
            'naziv' => $row['naziv'],
            'naziv_engleski' => $row['naziv_eng'] ?? $predmet->naziv_engleski,
            'sifra_predmeta' => $row['skracen_naziv'] ?: ($predmet->sifra_predmeta ?: 'P' . $row['predmet_id']),
            'ects' => (int) ($row['ects'] ?? 0),
            'semestar' => (int) ($row['semestar'] ?? 1),
            'nivo_studija_id' => $this->nivoStudijaId($row['nivo_studija_id'] ?? null),
            'platforma_pfs_id' => (int) $row['predmet_fakultet_semestar_id'],
            'platforma_predmet_id' => (int) $row['predmet_id'],
        ]);
        $predmet->save();

        return $predmet;
    }

    private function lokalniFakultet(int $platformaFakultetId): Fakultet
    {
        $f = Fakultet::where('platforma_fakultet_id', $platformaFakultetId)->first();

        if ($f) {
            return $f;
        }

        $pf = collect($this->client->sifarnici()['fakulteti'] ?? [])->firstWhere('id', $platformaFakultetId);
        $naziv = $pf ? "{$pf['naziv']} ({$pf['skracen_naziv']})" : "id={$platformaFakultetId}";

        throw new PlatformaException("Fakultet {$naziv} sa platforme nije povezan ni sa jednim lokalnim fakultetom. Poveži ga u formi fakulteta (polje 'Fakultet na studentskoj platformi').");
    }

    private function nivoStudijaId(?int $platformaNivoId): int
    {
        static $mapa = null;

        if ($mapa === null) {
            $mapa = collect($this->client->sifarnici()['nivoi_studija'] ?? [])->keyBy('id');
        }

        $naziv = $mapa[$platformaNivoId]['naziv'] ?? 'Osnovne';

        return NivoStudija::firstOrCreate(['naziv' => $naziv])->id;
    }
}
