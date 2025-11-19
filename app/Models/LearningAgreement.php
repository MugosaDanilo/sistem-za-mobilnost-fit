<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearningAgreement extends Model
{
    protected $fillable = ['ime', 'prezime', 'naziv_fakulteta'];

    public function courses(): HasMany
    {
        return $this->hasMany(LearningAgreementCourse::class);
    }
}
