<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningAgreement extends Model
{
    protected $table = 'learning_agreements';
    
    protected $fillable = [
        'mobilnost_id',
        'fit_predmet_id',
        'strani_predmet_id',
        'napomena',
        'ocjena'
    ];

    public function mobilnost()
    {
        return $this->belongsTo(Mobilnost::class);
    }

    public function fitPredmet()
    {
        return $this->belongsTo(Predmet::class, 'fit_predmet_id');
    }

    public function straniPredmet()
    {
        return $this->belongsTo(Predmet::class, 'strani_predmet_id');
    }
}
