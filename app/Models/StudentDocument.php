<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentDocument extends Model
{
    protected $fillable = [
        'student_id',
        'naziv',
        'file_path',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}