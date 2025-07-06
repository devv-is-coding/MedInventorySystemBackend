<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransactionType extends Model
{
    use HasFactory;

     protected $fillable = [
        'id',
        'code',
        'label',
    ];

    public $incrementing = false;
    protected $keyType = 'integer';

    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class, 'txn_type_id');
    }

}
