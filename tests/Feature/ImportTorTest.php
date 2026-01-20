<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Predmet;
use App\Models\Fakultet;
use App\Models\NivoStudija;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class ImportTorTest extends TestCase
{
    use RefreshDatabase; // Use transaction to not pollute DB

    public function test_can_import_grades_from_word_document()
    {
        // 1. Setup Data
        // Create an admin user to authenticate
        $admin = User::factory()->create(['type' => 0]); // 0 = admin

        $fakultet = Fakultet::create(['naziv' => 'FIT']);
        $nivo = NivoStudija::create(['naziv' => 'Bachelor']);

        $subject = Predmet::create([
            'naziv' => 'Engineering Mathematics', // Matches Word doc
            'semestar' => 1,
            'ects' => 6,
            'fakultet_id' => $fakultet->id,
            'nivo_studija_id' => $nivo->id
        ]);

        // 2. Use the real provided file
        $realFile = base_path('ToR primjer.docx');
        if (!file_exists($realFile)) {
            $this->markTestSkipped('Real ToR file not found.');
        }

        $tempFile = sys_get_temp_dir() . '/tor_rec_test.docx';
        copy($realFile, $tempFile);

        // 3. Perform Request
        $file = new UploadedFile($tempFile, 'tor.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', null, true);

        $response = $this->actingAs($admin)
            ->post(route('students.import-tor'), [
                'tor_file' => $file
            ]);

        // 4. Assertions
        if ($response->status() !== 200) {
            $response->dump();
        }
        $response->assertStatus(200);
        $response->assertJsonCount(1);
        // Word doc has "Engineering Mathematics" with Grade "A" and ECTS 6
        $response->assertJsonFragment([
            'id' => $subject->id,
            'grade' => 10, // A mapped to 10
            'original_grade' => 'A'
        ]);

        // Cleanup
        @unlink($tempFile);
    }
}
