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
    public function profesori()
    {
        return $this->belongsToMany(User::class, 'profesor_predmet', 'predmet_id', 'profesor_id');
    }
}
