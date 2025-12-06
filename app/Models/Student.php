<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'studenti';

    protected $fillable = [
        'ime',
        'prezime',
        'br_indexa',
        'datum_rodjenja',
        'telefon',
        'email',
        'godina_studija',
        'jmbg',
        'nivo_studija_id'
    ];

    protected $casts = [
        'datum_rodjenja' => 'date',
    ];

    public function getPunoImeAttribute()
    {
        return $this->ime . ' ' . $this->prezime;
    }

    public function nivoStudija()
    {
        return $this->belongsTo(NivoStudija::class, 'nivo_studija_id');
    }

    public function mobilnosti()
    {
        return $this->hasMany(Mobilnost::class, 'student_id');
    }

    public function predmeti()
    {
        return $this->belongsToMany(Predmet::class, 'student_predmet', 'student_id', 'predmet_id')
                    ->withPivot('ocjena', 'polozen')
                    ->withTimestamps();
    }
}
