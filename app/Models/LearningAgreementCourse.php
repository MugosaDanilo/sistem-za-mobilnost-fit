<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningAgreementCourse extends Model
{
    protected $fillable = [
        'learning_agreement_id',
        'predmet_fit',
        'semestar',
        'ects',
        'strani_predmet',
        'ocjena'
    ];

    public function agreement(): BelongsTo
    {
        return $this->belongsTo(LearningAgreement::class, 'learning_agreement_id');
    }
}
