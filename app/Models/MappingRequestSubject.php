<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MappingRequestSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'mapping_request_id',
        'strani_predmet_id',
        'fit_predmet_id',
    ];

    public function mappingRequest()
    {
        return $this->belongsTo(MappingRequest::class);
    }

    public function straniPredmet()
    {
        return $this->belongsTo(Predmet::class, 'strani_predmet_id');
    }

    public function fitPredmet()
    {
        return $this->belongsTo(Predmet::class, 'fit_predmet_id');
    }
}
