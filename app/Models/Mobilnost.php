<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mobilnost extends Model
{
    protected $table = 'mobilnosti';
    protected $fillable = ['datum_pocetka', 'datum_kraja', 'student_id', 'fakultet_id', 'is_locked'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function fakultet()
    {
        return $this->belongsTo(Fakultet::class);
    }

    public function learningAgreements()
    {
        return $this->hasMany(LearningAgreement::class);
    }
    
}
