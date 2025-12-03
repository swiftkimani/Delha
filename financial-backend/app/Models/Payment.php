<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'member_id', 'sheet_id', 'date',
        'savings', 'project', 'welfare', 'fine', 'others'
    ];
    
    protected $casts = [
        'date' => 'date',
        'savings' => 'decimal:2',
        'project' => 'decimal:2',
        'welfare' => 'decimal:2',
        'fine' => 'decimal:2',
        'others' => 'decimal:2',
    ];
    
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }
    
    public function sheet(): BelongsTo
    {
        return $this->belongsTo(Sheet::class);
    }
    
    protected static function booted(): void
    {
        static::saving(function ($payment) {
            // Auto-exclude sheets with "Sep 9 - Sep 30 (2025)" in name
            if (str_contains($payment->sheet->name, 'Sep 9 - Sep 30 (2025)')) {
                $payment->sheet->update(['is_excluded' => true]);
            }
        });
    }
}