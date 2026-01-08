<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Predmet extends Model
{
    protected $table = 'predmeti';

    protected $fillable = [
        'sifra_predmeta',
        'naziv',
        'semestar',
        'ects',
        'fakultet_id',
        'profesor_id',
        'nivo_studija_id',
        'naziv_engleski',
    ];

    public function fakultet()
    {
        return $this->belongsTo(Fakultet::class);
    }
    public function profesori()
    {
        return $this->belongsToMany(User::class, 'profesor_predmet', 'predmet_id', 'profesor_id');
    }

    public function nivoStudija()
    {
        return $this->belongsTo(NivoStudija::class);
    }

    public function studenti()
    {
        return $this->belongsToMany(Student::class, 'student_predmet', 'predmet_id', 'student_id')->withPivot('grade');
    }
}
