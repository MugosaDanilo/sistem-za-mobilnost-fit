<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fakultet extends Model
{
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

 public function mobilnosti()
{
    return $this->hasMany(\App\Models\Mobilnost::class, 'fakultet_id');
}


}
