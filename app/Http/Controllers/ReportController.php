<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockTransaction;
use App\Models\Medicine;
use App\Models\MonthlySummary;
use Carbon\Carbon;

class ReportController extends Controller
{
   public function dailyReport(Request $request)
{
    $validated = $request->validate([
        'date' => 'required|date',
    ]);

    $transactions = StockTransaction::with(['medicine', 'transactionType'])
        ->whereDate('txn_date', $validated['date'])
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json([
        'status' => true,
        'message' => 'Daily report retrieved successfully',
        'data' => $transactions,
    ]);
}


    public function monthlyReport(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $medicines = Medicine::all();
        $reports = [];

        foreach ($medicines as $medicine) {
            $transactions = StockTransaction::where('medicine_id', $medicine->id)
                ->whereYear('txn_date', $validated['year'])
                ->whereMonth('txn_date', $validated['month'])
                ->get();

            // Type ID constants from migration:
            $FORWARD = 1;
            $NEW_ADDED = 2;
            $DONATION = 3;
            $RETURN = 4;
            $DISPENSE_IDS = [5, 6, 7, 8]; // CHARGES, WARD_ISSUANCE, OTC, DAMAGES

            $openingStock = 0;
            $totalReturn = 0;
            $totalDonation = 0;
            $totalNewAdded = 0;
            $totalDispensed = 0;

            foreach ($transactions as $txn) {
                if ($txn->txn_type_id == $FORWARD) {
                    $openingStock += $txn->quantity;
                } elseif ($txn->txn_type_id == $RETURN) {
                    $totalReturn += $txn->quantity;
                } elseif ($txn->txn_type_id == $DONATION) {
                    $totalDonation += $txn->quantity;
                } elseif ($txn->txn_type_id == $NEW_ADDED) {
                    $totalNewAdded += $txn->quantity;
                } elseif (in_array($txn->txn_type_id, $DISPENSE_IDS)) {
                    $totalDispensed += $txn->quantity;
                }
            }

            $closingStock = $openingStock + $totalReturn + $totalDonation + $totalNewAdded - $totalDispensed;

            $reports[] = [
                'medicine_id' => $medicine->id,
                'medicine' => $medicine,
                'year' => $validated['year'],
                'month' => $validated['month'],
                'opening_stock' => $openingStock,
                'total_return' => $totalReturn,
                'total_donation' => $totalDonation,
                'total_new_added' => $totalNewAdded,
                'total_dispensed' => $totalDispensed,
                'closing_stock' => max(0, $closingStock),
            ];
        }

        return response()->json([
            'status' => true,
            'message' => 'Monthly report retrieved successfully',
            'data' => $reports,
        ]);
    }

    public function performMonthClose(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $monthlyReports = $this->monthlyReport(new Request($validated))->getData(true)['data'];

        // Calculate next month and year
        $current = Carbon::create($validated['year'], $validated['month'], 1);
        $next = $current->copy()->addMonth();
        $nextDate = $next->toDateString();

        $FORWARD = 1;
        $forwardCount = 0;

        foreach ($monthlyReports as $report) {
            if ($report['closing_stock'] > 0) {
                StockTransaction::create([
                    'medicine_id' => $report['medicine_id'],
                    'txn_type_id' => $FORWARD,
                    'txn_date' => $nextDate,
                    'quantity' => $report['closing_stock'],
                    'remarks' => "Forward from {$validated['year']}-" . str_pad($validated['month'], 2, '0', STR_PAD_LEFT),
                    'created_by' => $request->user()->id,
                ]);
                $forwardCount++;
            }

            // Store monthly summary
            MonthlySummary::updateOrCreate(
                [
                    'medicine_id' => $report['medicine_id'],
                    'year' => $validated['year'],
                    'month' => $validated['month'],
                ],
                [
                    'opening_stock' => $report['opening_stock'],
                    'total_return' => $report['total_return'],
                    'total_donation' => $report['total_donation'],
                    'total_new_added' => $report['total_new_added'],
                    'total_dispensed' => $report['total_dispensed'],
                    'closing_stock' => $report['closing_stock'],
                ]
            );
        }

        return response()->json([
            'status' => true,
            'message' => 'Month closed successfully',
            'forward_transactions_created' => $forwardCount,
        ]);
    }
}
