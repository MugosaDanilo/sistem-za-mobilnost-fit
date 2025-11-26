<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrepisAgreement extends Model
{
    protected $table = 'prepis_agreements';

    protected $fillable = [
        'status',
        'prepis_id',
        'fit_predmet_id',
        'strani_predmet_id',
    ];

    public function prepis(): BelongsTo
    {
        return $this->belongsTo(Prepis::class);
    }

    public function fitPredmet(): BelongsTo
    {
        return $this->belongsTo(Predmet::class, 'fit_predmet_id');
    }

    public function straniPredmet(): BelongsTo
    {
        return $this->belongsTo(Predmet::class, 'strani_predmet_id');
    }
}
