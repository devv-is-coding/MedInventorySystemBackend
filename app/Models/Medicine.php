<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medicine extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'unit',
        'dosage_form',
        'description',
    ];

    protected $appends = ['current_stock'];

    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function monthlySummaries()
    {
        return $this->hasMany(MonthlySummary::class);
    }

        public function getCurrentStockAttribute()
{
    $transactions = $this->stockTransactions;
    $currentStock = 0;

    $addTypes = [2, 3, 4]; // RDD = Return, Donation, Direct Add

    foreach ($transactions as $transaction) {
        if (in_array($transaction->txn_type_id, $addTypes)) {
            $currentStock += $transaction->quantity;
        } else {
            $currentStock -= $transaction->quantity;
        }
    }

    return max(0, $currentStock);
}

    }
