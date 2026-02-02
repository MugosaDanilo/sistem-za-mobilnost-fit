<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobilityCategory extends Model
{
    protected $guarded = [];

    public function documents()
    {
        return $this->hasMany(MobilnostDokument::class, 'category_id');
    }
}
