<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FinancialTransaction;
use App\Models\Building;
use Illuminate\Support\Facades\Auth;

class FinancialController extends Controller
{
    public function getTransactions()
    {
        $transactions = FinancialTransaction::whereHas('building', function($query) {
            $query->where('company_id', Auth::user()->company_id);
        })->with(['building'])
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json(['data' => $transactions]);
    }

    public function createTransaction(Request $request)
    {
        $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'transaction_type' => 'required|in:gelir,gider',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'transaction_date' => 'required|date',
            'payment_method' => 'sometimes|string|max:50',
            'reference_number' => 'sometimes|string|max:100',
        ]);

        // Check if building belongs to company
        $building = Building::where('company_id', Auth::user()->company_id)
            ->where('id', $request->building_id)
            ->firstOrFail();

        $transaction = FinancialTransaction::create([
            'building_id' => $request->building_id,
            'transaction_type' => $request->transaction_type,
            'amount' => $request->amount,
            'description' => $request->description,
            'category' => $request->category,
            'transaction_date' => $request->transaction_date,
            'payment_method' => $request->payment_method,
            'reference_number' => $request->reference_number,
            'status' => 'completed',
        ]);

        return response()->json(['data' => $transaction], 201);
    }

    public function getStats()
    {
        $companyId = Auth::user()->company_id;

        $stats = [
            'total_income' => FinancialTransaction::whereHas('building', function($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })->where('transaction_type', 'gelir')->sum('amount'),
            'total_expense' => FinancialTransaction::whereHas('building', function($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })->where('transaction_type', 'gider')->sum('amount'),
            'net_income' => FinancialTransaction::whereHas('building', function($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })->where('transaction_type', 'gelir')->sum('amount') -
            FinancialTransaction::whereHas('building', function($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })->where('transaction_type', 'gider')->sum('amount'),
            'monthly_income' => FinancialTransaction::whereHas('building', function($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })->where('transaction_type', 'gelir')
            ->whereMonth('transaction_date', now()->month)
            ->sum('amount'),
            'monthly_expense' => FinancialTransaction::whereHas('building', function($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })->where('transaction_type', 'gider')
            ->whereMonth('transaction_date', now()->month)
            ->sum('amount'),
        ];

        return response()->json(['data' => $stats]);
    }
}
