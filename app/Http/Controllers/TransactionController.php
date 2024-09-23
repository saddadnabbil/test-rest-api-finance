<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::paginate(10);
        return response()->json($transactions);
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'type' => 'required|in:income,expense',
            'transaction_date' => 'required|date'
        ]);

        $transaction = Transaction::create($request->all());

        return response()->json([
            'message' => 'Transaction created successfully',
            'transaction' => $transaction
        ], 201);
    }

    public function show($id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json([
                'message' => 'Transaction not found'
            ], 404);
        }

        return response()->json($transaction);
    }

    public function update(Request $request, $id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json([
                'message' => 'Transaction not found'
            ], 404);
        }

        $request->validate([
            'description' => 'sometimes|required|string|max:255',
            'amount' => 'sometimes|required|numeric',
            'type' => 'sometimes|required|in:income,expense',
            'transaction_date' => 'sometimes|required|date'
        ]);

        $transaction->update($request->all());

        return response()->json([
            'message' => 'Transaction updated successfully',
            'transaction' => $transaction
        ]);
    }

    public function destroy($id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json([
                'message' => 'Transaction not found'
            ], 404);
        }

        $transaction->delete();

        return response()->json([
            'message' => 'Transaction deleted successfully'
        ]);
    }

    public function getBalance()
    {
        $income = Transaction::where('type', 'income')->sum('amount');
        $expense = Transaction::where('type', 'expense')->sum('amount');
        $balance = $income - $expense;

        return response()->json(['balance' => $balance], 200);
    }
}
