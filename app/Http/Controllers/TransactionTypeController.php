<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransactionType;

class TransactionTypeController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => true,
            'message' => 'Transaction types retrieved successfully',
            'data' => TransactionType::all()
        ]);
    }
}
