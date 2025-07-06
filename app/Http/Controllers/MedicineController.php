<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medicine;

class MedicineController extends Controller
{
    public function index(Request $request)
    {
         $query = Medicine::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('unit', 'like', "%{$search}%")
                  ->orWhere('dosage_form', 'like', "%{$search}%");
            });
        }

        $medicines = $query->orderBy('name')->get();

        return response()->json([
            'status' => true,
            'message' => 'Medicines retrieved successfully',
            'data' => $medicines
        ]);
    }

    public function store(Request $request)
    {
         $data = $request->validate([
            'name' => 'required|string|max:200',
            'unit' => 'nullable|string|max:50',
            'dosage_form' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        $medicine = Medicine::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Medicine created successfully',
            'data' => $medicine
        ], 201);
    }
    public function show(Medicine $medicine)
    {
         return response()->json([
            'status' => true,
            'message' => 'Medicine retrieved successfully',
            'data' => $medicine
        ]);
    }
    public function update(Request $request, Medicine $medicine)
    {
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'unit' => 'nullable|string|max:50',
            'dosage_form' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        $medicine->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Medicine updated successfully',
            'data' => $medicine
        ]);
    }

    public function destroy(Medicine $medicine)
    {
        $medicine->delete();
        
        return response()->json([
            'status' => true,
            'message' => 'Medicine deleted successfully'
        ]);
    }
}
