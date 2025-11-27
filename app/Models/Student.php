<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'ime',
        'prezime',
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
    }
}
