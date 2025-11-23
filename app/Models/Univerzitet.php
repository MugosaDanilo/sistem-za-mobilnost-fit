<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Univerzitet extends Model
{
    protected $table = 'univerziteti';
    protected $fillable = ['naziv', 'drzava', 'grad', 'email'];

    public function fakulteti()
    {
        return $this->hasMany(Fakultet::class);
    }
}
