<?php
<<<<<<< HEAD

namespace App\Models;

=======
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
>>>>>>> master
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
<<<<<<< HEAD
    protected $table = 'studenti';
=======
    use HasFactory;
>>>>>>> master

    protected $fillable = [
        'ime',
        'prezime',
<<<<<<< HEAD
        'br_indexa',
        'datum_rodjenja',
        'telefon',
        'email',
        'godina_studija',
        'jmbg',
        'nivo_studija_id'
    ];

    public function nivoStudija()
    {
        return $this->belongsTo(NivoStudija::class, 'nivo_studija_id');
    }

    public function mobilnosti()
    {
        return $this->hasMany(Mobilnost::class, 'student_id');
=======
        'broj_indeksa',
        'email',
        'telefon',
        'datum_rodjenja',
        'godina_studija',
        'napomena',
    ];

    protected $casts = [
        'datum_rodjenja' => 'date',
    ];

    public function getPunoImeAttribute(): string
    {
        return $this->ime.' '.$this->prezime;
>>>>>>> master
    }
}
