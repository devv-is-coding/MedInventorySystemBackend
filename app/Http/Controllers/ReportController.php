<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function dailyReport(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
        ]);

        $transactions = StockTransaction::with(['medicine', 'transactionType'])
            ->whereDate('txn_date', $data['date'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Daily report retrieved successfully',
            'data' => $transactions
        ]);
    }

    public function monthlyReport(Request $request)
    {
        $data = $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $medicines = Medicine::all();
        $reports = [];

        foreach ($medicines as $medicine) {
            $transactions = StockTransaction::where('medicine_id', $medicine->id)
                ->whereYear('txn_date', $data['year'])
                ->whereMonth('txn_date', $data['month'])
                ->get();

            $openingStock = 0;
            $totalReturn = 0;
            $totalDonation = 0;
            $totalNewAdded = 0;
            $totalDispensed = 0;

            foreach ($transactions as $transaction) {
                switch ($transaction->txn_type_id) {
                    case 1: // FORWARD
                        $openingStock += $transaction->quantity;
                        break;
                    case 2: // RETURN
                        $totalReturn += $transaction->quantity;
                        break;
                    case 3: // DONATION
                        $totalDonation += $transaction->quantity;
                        break;
                    case 4: // NEW_ADDED
                        $totalNewAdded += $transaction->quantity;
                        break;
                    case 5: // DISPENSE
                        $totalDispensed += $transaction->quantity;
                        break;
                }
            }

            $closingStock = $openingStock + $totalReturn + $totalDonation + $totalNewAdded - $totalDispensed;

            $reports[] = [
                'medicine_id' => $medicine->id,
                'medicine' => $medicine,
                'year' => $data['year'],
                'month' => $data['month'],
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
            'data' => $reports
        ]);
    }

    public function performMonthClose(Request $request)
    {
        $data = $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12',
        ]);

        // Get monthly report data
        $monthlyReportRequest = new Request(['year' => $data['year'], 'month' => $data['month']]);
        $monthlyReports = $this->monthlyReport($monthlyReportRequest)->getData()->data;

        // Calculate next month
        $nextMonth = $data['month'] == 12 ? 1 : $data['month'] + 1;
        $nextYear = $data['month'] == 12 ? $data['year'] + 1 : $data['year'];
        $nextMonthDate = Carbon::create($nextYear, $nextMonth, 1)->format('Y-m-d');

        $forwardCount = 0;

        foreach ($monthlyReports as $report) {
            if ($report->closing_stock > 0) {
                StockTransaction::create([
                    'medicine_id' => $report->medicine_id,
                    'txn_type_id' => 1, // FORWARD
                    'txn_date' => $nextMonthDate,
                    'quantity' => $report->closing_stock,
                    'remarks' => "Forward from {$data['year']}-" . str_pad($data['month'], 2, '0', STR_PAD_LEFT),
                    'created_by' => $request->user()->id,
                ]);
                $forwardCount++;
            }

            // Save monthly summary
            MonthlySummary::updateOrCreate(
                [
                    'medicine_id' => $report->medicine_id,
                    'year' => $data['year'],
                    'month' => $data['month'],
                ],
                [
                    'opening_stock' => $report->opening_stock,
                    'total_return' => $report->total_return,
                    'total_donation' => $report->total_donation,
                    'total_new_added' => $report->total_new_added,
                    'total_dispensed' => $report->total_dispensed,
                    'closing_stock' => $report->closing_stock,
                ]
            );
        }

        return response()->json([
            'status' => true,
            'message' => 'Month closed successfully',
            'forward_transactions_created' => $forwardCount
        ]);
    }
}
