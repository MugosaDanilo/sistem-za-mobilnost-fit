<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // dodaj ovo


class Fakultet extends Model
{
    use HasFactory;
    protected $table = 'fakulteti';
    protected $fillable = ['naziv', 'email', 'telefon', 'web', 'uputstvo_za_ocjene', 'univerzitet_id'];

    public function univerzitet()
    {
        return $this->belongsTo(Univerzitet::class);
    }

    public function predmeti()
    {
        return $this->hasMany(Predmet::class);
    }
}
