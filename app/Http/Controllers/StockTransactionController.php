<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockTransaction;
use App\Models\Medicine;

class StockTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = StockTransaction::with(['medicine', 'transactionType']);

        if ($request->has('date')) {
            $query->whereDate('txn_date', $request->date);
        }

        if ($request->has('medicine_id')) {
            $query->where('medicine_id', $request->medicine_id);
        }

        $transactions = $query->orderBy('txn_date', 'desc')
                             ->orderBy('created_at', 'desc')
                             ->get();

        return response()->json([
            'status' => true,
            'message' => 'Transactions retrieved successfully',
            'data' => $transactions
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'txn_type_id' => 'required|exists:transaction_types,id',
            'txn_date' => 'required|date',
            'quantity' => 'required|integer|min:1',
            'remarks' => 'nullable|string',
        ]);

        // Check stock for dispense transactions
        if ($data['txn_type_id'] == 5) { // DISPENSE
            $medicine = Medicine::find($data['medicine_id']);
            $currentStock = $medicine->current_stock;
            
            if ($data['quantity'] > $currentStock) {
                return response()->json([
                    'status' => false,
                    'message' => 'Insufficient stock available',
                    'available_stock' => $currentStock
                ], 400);
            }
        }

        $transaction = StockTransaction::create([
            'medicine_id' => $data['medicine_id'],
            'txn_type_id' => $data['txn_type_id'],
            'txn_date' => $data['txn_date'],
            'quantity' => $data['quantity'],
            'remarks' => $data['remarks'],
            'created_by' => $request->user()->id,
        ]);

        $transaction->load(['medicine', 'transactionType']);

        return response()->json([
            'status' => true,
            'message' => 'Transaction created successfully',
            'data' => $transaction
        ], 201);
    }

    public function show(StockTransaction $stockTransaction)
    {
         $stockTransaction->load(['medicine', 'transactionType']);
        
        return response()->json([
            'status' => true,
            'message' => 'Transaction retrieved successfully',
            'data' => $stockTransaction
        ]);
    }
    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(StockTransaction $stockTransaction)
    {
         $stockTransaction->delete();
        
        return response()->json([
            'status' => true,
            'message' => 'Transaction deleted successfully'
        ]);
    }
}
