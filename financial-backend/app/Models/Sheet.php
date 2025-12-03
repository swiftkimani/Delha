<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sheet extends Model
{
    protected $fillable = ['name', 'upload_date', 'is_excluded'];
    
    protected $casts = [
        'upload_date' => 'date',
        'is_excluded' => 'boolean',
    ];
    
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}