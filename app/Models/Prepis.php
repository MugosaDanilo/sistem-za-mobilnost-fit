<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prepis extends Model
{
    protected $table = 'prepisi';

    protected $fillable = [
        'datum',
        'status',
        'napomena',
        'fakultet_id',
        'student_id',
    ];

    protected $casts = [
        'datum' => 'date',
    ];

    public function fakultet(): BelongsTo
    {
        return $this->belongsTo(Fakultet::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function agreements(): HasMany
    {
        return $this->hasMany(PrepisAgreement::class);
    }

    public function getDerivedStatusAttribute(): string
    {
        $agreements = $this->agreements;

        if ($agreements->isEmpty()) {
            return 'u procesu'; 
        }

        if ($agreements->contains('status', 'odbijen')) {
            return 'odbijen';
        }

        if ($agreements->contains('status', 'u procesu')) {
            return 'u procesu';
        }

        return 'odobren';
    }
}
