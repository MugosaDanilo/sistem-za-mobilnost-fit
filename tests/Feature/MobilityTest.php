<?php

namespace Tests\Feature;

use App\Models\Fakultet;
use App\Models\Mobilnost;
use App\Models\Predmet;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MobilityTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function save_kreira_mobilnost_i_learning_agreemente()
    {
        // admin korisnik (type = 0) zbog adminAuth middleware-a
        $user = User::factory()->create(['type' => 0]);
        $this->actingAs($user);

        $fitFakultet = Fakultet::factory()->create(['naziv' => 'FIT']);
        $straniFakultet = Fakultet::factory()->create();
        $student = Student::factory()->create(['br_indexa' => 'IB123/2023']);

        $fitPredmet = Predmet::factory()->create([
            'naziv' => 'Programiranje 1',
            'fakultet_id' => $fitFakultet->id,
            'ects' => 6,
            'semestar' => 1,
        ]);

        $payload = [
            'ime' => $student->ime,
            'prezime' => $student->prezime,
            'fakultet_id' => $straniFakultet->id,
            'student_id' => $student->id,
            'broj_indeksa' => $student->br_indexa,
            'datum_pocetka' => now()->toDateString(),
            'datum_kraja' => now()->addMonth()->toDateString(),
            'links' => [
                'Programiranje 1' => ['Programming I'],
            ],
            'courses' => [
                ['Course' => 'Programiranje 1', 'Term' => '1', 'ECTS' => 6],
                ['Course' => 'Programming I', 'Term' => '1', 'ECTS' => 6],
            ],
        ];

        // koristimo admin.mobility.save jer tako piše u web.php
        $response = $this->postJson(route('admin.mobility.save'), $payload);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Learning Agreement saved successfully.']);

        $this->assertDatabaseHas('mobilnosti', [
            'student_id' => $student->id,
            'fakultet_id' => $straniFakultet->id,
        ]);

        $mobilnost = Mobilnost::first();
        $this->assertNotNull($mobilnost);

        $this->assertDatabaseHas('learning_agreements', [
            'mobilnost_id' => $mobilnost->id,
        ]);
    }

    #[Test]
    public function save_validira_obavezna_polja()
    {
        $user = User::factory()->create(['type' => 0]);
        $this->actingAs($user);

        $response = $this->postJson(route('admin.mobility.save'), []);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'ime',
            'prezime',
            'fakultet_id',
            'student_id',
            'broj_indeksa',
            'datum_pocetka',
            'datum_kraja',
            'links',
        ]);
    }

    #[Test]
    public function show_vraca_stranicu_sa_mobilnoscu()
    {
        $user = User::factory()->create(['type' => 0]);
        $this->actingAs($user);

        $mobilnost = Mobilnost::factory()->create();

        $response = $this->get(route('admin.mobility.show', $mobilnost->id));

        $response->assertStatus(200);
        $response->assertViewIs('mobility.show');
        $response->assertViewHas('mobilnost');
    }
}
