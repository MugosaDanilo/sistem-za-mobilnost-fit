<?php

namespace Tests\Feature;

use App\Models\Fakultet;
use App\Models\Predmet;
use App\Models\Prepis;
use App\Models\PrepisAgreement;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PrepisTest extends TestCase
{
    use RefreshDatabase;

    private function loginAdmin(): User
    {
        $user = User::factory()->create(['type' => 0]);
        $this->actingAs($user);

        return $user;
    }

    #[Test]
    public function moze_da_kreira_prepis_sa_agreementima()
    {
        $this->loginAdmin();

        $student = Student::factory()->create();
        $fakultet = Fakultet::factory()->create();
        $fitPredmet = Predmet::factory()->create();
        $straniPredmet = Predmet::factory()->create();

        $payload = [
            'student_id' => $student->id,
            'fakultet_id' => $fakultet->id,
            'datum' => now()->toDateString(),
            'agreements' => [
                [
                    'fit_predmet_id' => $fitPredmet->id,
                    'strani_predmet_id' => $straniPredmet->id,
                ],
            ],
        ];

        $response = $this->post(route('prepis.store'), $payload);
        $response->assertRedirect(route('prepis.index'));

        $this->assertDatabaseHas('prepisi', [
            'student_id' => $student->id,
            'fakultet_id' => $fakultet->id,
            'status' => 'u procesu',
        ]);

        $this->assertDatabaseHas('prepis_agreements', [
            'fit_predmet_id' => $fitPredmet->id,
            'strani_predmet_id' => $straniPredmet->id,
            'status' => 'u procesu',
        ]);
    }

    #[Test]
    public function store_validira_obavezna_polja()
    {
        $this->loginAdmin();

        $response = $this->from(route('prepis.create'))
            ->post(route('prepis.store'), []);

        $response->assertRedirect(route('prepis.create'));

        $response->assertSessionHasErrors([
            'student_id',
            'fakultet_id',
            'datum',
            'agreements',
        ]);
    }

    #[Test]
    public function update_mijenja_podatke_i_ponovno_kreira_agreemente()
    {
        $this->loginAdmin();

        $student = Student::factory()->create();
        $fakultet = Fakultet::factory()->create();

        $stariFit = Predmet::factory()->create();
        $stariStrani = Predmet::factory()->create();

        $prepis = Prepis::factory()->create([
            'student_id' => $student->id,
            'fakultet_id' => $fakultet->id,
            'datum' => now()->subDay()->toDateString(),
            'status' => 'u procesu',
        ]);

        $stariAgreement = PrepisAgreement::factory()->create([
            'prepis_id' => $prepis->id,
            'fit_predmet_id' => $stariFit->id,
            'strani_predmet_id' => $stariStrani->id,
            'status' => 'u procesu',
        ]);

        $noviFit = Predmet::factory()->create();
        $noviStrani = Predmet::factory()->create();

        $payload = [
            'student_id' => $student->id,
            'fakultet_id' => $fakultet->id,
            'datum' => now()->toDateString(),
            'agreements' => [
                [
                    'fit_predmet_id' => $noviFit->id,
                    'strani_predmet_id' => $noviStrani->id,
                ],
            ],
        ];

        $response = $this->put(route('prepis.update', $prepis->id), $payload);

        $response->assertRedirect(route('prepis.index'));

        $this->assertDatabaseMissing('prepis_agreements', [
            'id' => $stariAgreement->id,
        ]);

        $this->assertDatabaseHas('prepis_agreements', [
            'prepis_id' => $prepis->id,
            'fit_predmet_id' => $noviFit->id,
            'strani_predmet_id' => $noviStrani->id,
        ]);
    }

    #[Test]
    public function destroy_brise_prepis_i_sve_agreemente()
    {
        $this->loginAdmin();

        $student = Student::factory()->create();
        $fakultet = Fakultet::factory()->create();
        $fit = Predmet::factory()->create();
        $strani = Predmet::factory()->create();

        $prepis = Prepis::factory()->create([
            'student_id' => $student->id,
            'fakultet_id' => $fakultet->id,
            'datum' => now()->toDateString(),
            'status' => 'u procesu',
        ]);

        $agreement = PrepisAgreement::factory()->create([
            'prepis_id' => $prepis->id,
            'fit_predmet_id' => $fit->id,
            'strani_predmet_id' => $strani->id,
            'status' => 'u procesu',
        ]);

        $response = $this->delete(route('prepis.destroy', $prepis->id));

        $response->assertRedirect(route('prepis.index'));

        $this->assertDatabaseMissing('prepisi', ['id' => $prepis->id]);
        $this->assertDatabaseMissing('prepis_agreements', ['id' => $agreement->id]);
    }

    #[Test]
    public function show_prikazuje_jedan_prepis()
    {
        $this->loginAdmin();

        $prepis = Prepis::factory()->create();

        $response = $this->get(route('prepis.show', $prepis->id));

        $response->assertStatus(200);
        $response->assertViewIs('prepis.show');
        $response->assertViewHas('prepis');
    }
}
