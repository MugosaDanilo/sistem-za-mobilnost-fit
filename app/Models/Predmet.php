<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Predmet extends Model
{
    protected $table = 'predmeti';

    protected $fillable = ['naziv', 'ects', 'semestar', 'fakultet_id'];

    public function fakultet()
    {
        return $this->belongsTo(Fakultet::class);
    }
}
