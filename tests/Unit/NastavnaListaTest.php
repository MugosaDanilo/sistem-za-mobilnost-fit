<?php

namespace Tests\Unit;

use App\Models\Fakultet;
use App\Models\Predmet;
use App\Models\NastavnaLista;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NastavnaListaTest extends TestCase
{
    use RefreshDatabase;

    private function napraviFakultetIPredmet()
    {
        $fakultet = Fakultet::factory()->create();
        $predmet = Predmet::factory()->create([
            'fakultet_id' => $fakultet->id,
        ]);

        return [$fakultet, $predmet];
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function moze_se_kreirati_nastavna_lista()
    {
        [$fakultet, $predmet] = $this->napraviFakultetIPredmet();

        $nastavnaLista = NastavnaLista::factory()->create([
            'fakultet_id' => $fakultet->id,
            'predmet_id' => $predmet->id,
        ]);

        $this->assertDatabaseHas('nastavne_liste', [
            'id' => $nastavnaLista->id,
            'fakultet_id' => $fakultet->id,
            'predmet_id' => $predmet->id,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function moze_se_dohvatiti_nastavna_lista_po_predmetu()
    {
        [$fakultet, $predmet] = $this->napraviFakultetIPredmet();

        $nastavnaLista = NastavnaLista::factory()->create([
            'fakultet_id' => $fakultet->id,
            'predmet_id' => $predmet->id,
        ]);

        $lista = NastavnaLista::where('predmet_id', $predmet->id)->first();
        $this->assertEquals($nastavnaLista->id, $lista->id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function moze_se_provjeriti_da_predmet_ima_nastavnu_listu()
    {
        [$fakultet, $predmet] = $this->napraviFakultetIPredmet();

        $nastavnaLista = NastavnaLista::factory()->create([
            'fakultet_id' => $fakultet->id,
            'predmet_id' => $predmet->id,
        ]);

        $this->assertTrue($predmet->nastavnaLista()->exists());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function moze_se_azurirati_nastavna_lista()
    {
        [$fakultet, $predmet] = $this->napraviFakultetIPredmet();

        $nastavnaLista = NastavnaLista::factory()->create([
            'fakultet_id' => $fakultet->id,
            'predmet_id' => $predmet->id,
        ]);

       $nastavnaLista->update(['link' => 'https://test-link.com']);
$this->assertDatabaseHas('nastavne_liste', [
    'id' => $nastavnaLista->id,
    'link' => 'https://test-link.com',
]);

    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function moze_se_obrisati_nastavna_lista()
    {
        [$fakultet, $predmet] = $this->napraviFakultetIPredmet();

        $nastavnaLista = NastavnaLista::factory()->create([
            'fakultet_id' => $fakultet->id,
            'predmet_id' => $predmet->id,
        ]);

        $nastavnaLista->delete();
        $this->assertDatabaseMissing('nastavne_liste', [
            'id' => $nastavnaLista->id,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function moze_se_provjeriti_koji_predmeti_imate_nastavnu_listu()
    {
        [$fakultet, $predmet] = $this->napraviFakultetIPredmet();

        $nastavnaLista = NastavnaLista::factory()->create([
            'fakultet_id' => $fakultet->id,
            'predmet_id' => $predmet->id,
        ]);

        $predmetiSaNL = Predmet::has('nastavnaLista')->get();
        $this->assertTrue($predmetiSaNL->contains($predmet));
    }
}
