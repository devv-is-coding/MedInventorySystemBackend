<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MonthlySummary extends Model
{
     use HasFactory;

    protected $fillable = [
        'medicine_id',
        'year',
        'month',
        'opening_stock',
        'total_return',
        'total_donation',
        'total_new_added',
        'total_dispensed',
        'closing_stock',
    ];

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}
