<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Validate the request
            $validatedData = $request->validate([
                'account_name' => 'required|string|max:255',
                'account_type' => 'required|in:Personal,Business',
                'currency' => 'required|string|size:3', // ISO currency codes (USD, EUR, GBP)
                'balance' => 'nullable|numeric|min:0'
            ]);

            // Create the account
            $account = Account::create([
                'user_id' => Auth::id(),
                'account_name' => $validatedData['account_name'],
                'account_type' => $validatedData['account_type'],
                'currency' => $validatedData['currency'],
                'balance' => $validatedData['balance'] ?? 0
            ]);

            return response()->json(['message' => 'Account created successfully', 'account' => $account], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function show($account_number)
    {
        try {
        $account = Account::where('account_number', $account_number)
        ->where('user_id', Auth::id())
        ->firstOrFail();
        return response()->json($account);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Database error'], 500);
        } catch (Exception $e) {
            return response()->json(['error' => 'Account not found'], 404);
        }
    }

    public function update(Request $request, $account_number)
    {
        try {
            $account = Account::where('account_number', $account_number)
            ->where('user_id', Auth::id())
            ->firstOrFail();

            // Validate input
            $validatedData = $request->validate([
                'account_name' => 'sometimes|string|max:255',
                'account_type' => 'sometimes|in:Personal,Business',
                'currency' => 'sometimes|string|size:3',
                'balance' => 'sometimes|numeric|min:0'
            ]);

            $account->update($validatedData);

            return response()->json(['message' => 'Account updated successfully', 'account' => $account]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Account not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function destroy($account_number)
    {
        $account = Account::where('account_number', $account_number)
        ->where('user_id', Auth::id())
        ->firstOrFail();
        $account->delete();
        return response()->json(['message' => 'Account deactivated']);
    }
}
