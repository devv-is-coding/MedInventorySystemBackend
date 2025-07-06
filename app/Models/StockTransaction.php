<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'medicine_id',
        'txn_type_id',
        'txn_date',
        'quantity',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'txn_date' => 'date',
    ];

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function transactionType()
    {
        return $this->belongsTo(TransactionType::class, 'txn_type_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
