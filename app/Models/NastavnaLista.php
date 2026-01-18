<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NastavnaLista extends Model
{
    use HasFactory;

    protected $table = 'nastavne_liste';

    protected $fillable = [
        'predmet_id',
        'fakultet_id',
        'link',
    ];

    public function predmet()
    {
        return $this->belongsTo(Predmet::class);
    }

    public function fakultet()
    {
        return $this->belongsTo(Fakultet::class);
    }
}
