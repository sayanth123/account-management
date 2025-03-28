<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'account_id' => 'required|exists:accounts,id',
                'type' => 'required|in:Credit,Debit',
                'amount' => 'required|numeric|min:0.01',
                'description' => 'sometimes|string'
            ]);

            return DB::transaction(function () use ($validated) {
                $account = Account::where('id', $validated['account_id'])
                    ->where('user_id', Auth::id())
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($validated['type'] === 'Debit' && $account->balance < $validated['amount']) {
                    return response()->json(['error' => 'Insufficient funds'], 400);
                }

                $account->balance += ($validated['type'] === 'Credit' ? 1 : -1) * $validated['amount'];
                $account->save();

                $transaction = $account->transactions()->create($validated);

                return response()->json([
                    'message' => 'Transaction successful',
                    'transaction' => $transaction,
                ], 201);
            });
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Database error', 'details' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred', 'details' => $e->getMessage()], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $validated = $request->validate([
                'account_id' => 'required|exists:accounts,id',
                'from' => 'sometimes|date',
                'to' => 'sometimes|date|after_or_equal:from'
            ]);

            $transactions = Transaction::whereHas('account', function ($query) use ($validated) {
                $query->where('user_id', Auth::id())
                      ->where('id', $validated['account_id']);
            })
            ->when($validated['from'] ?? null, function ($query, $from) {
                $query->whereDate('created_at', '>=', $from);
            })
            ->when($validated['to'] ?? null, function ($query, $to) {
                $query->whereDate('created_at', '<=', $to);
            })
            ->get();

            return response()->json($transactions);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Database error', 'details' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred', 'details' => $e->getMessage()], 500);
        }
    }
}