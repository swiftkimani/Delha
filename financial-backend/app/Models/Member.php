<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    protected $primaryKey = 'member_id';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = ['member_id', 'name', 'status'];
    
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'member_id', 'member_id');
    }
}