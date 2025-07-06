<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Medicine extends Model
{
    use HasFactory;

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

        foreach ($transactions as $transaction) {
            if ($transaction->txn_type_id == 5) { // DISPENSE
                $currentStock -= $transaction->quantity;
            } else {
                $currentStock += $transaction->quantity;
            }
        }

        return max(0, $currentStock);
    }
}
