<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $hotelIds = $request->user()->getHotelIds();

        $query = Expense::whereIn('hotel_id', $hotelIds)->with('hotel:id,short_name');

        if ($request->filled('hotel_id'))  $query->where('hotel_id', $request->hotel_id);
        if ($request->filled('type'))      $query->where('expense_type', $request->type);
        if ($request->filled('date_from')) $query->whereDate('occurred_at', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('occurred_at', '<=', $request->date_to);

        $expenses = $query->latest('occurred_at')->paginate(20);

        return response()->json([
            'data'  => $expenses,
            'total' => $query->sum('amount'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'hotel_id'     => 'required|exists:hotels,id',
            'description'  => 'required|string',
            'amount'       => 'required|numeric|min:0',
            'expense_type' => 'required|string',
            'occurred_at'  => 'nullable|date',
            'notes'        => 'nullable|string',
        ]);

        $this->checkAccess($request, $data['hotel_id']);

        $data['occurred_at'] = $data['occurred_at'] ?? now();

        return response()->json(Expense::create($data), 201);
    }

    public function update(Request $request, Expense $expense)
    {
        $this->checkAccess($request, $expense->hotel_id);

        $data = $request->validate([
            'description'  => 'sometimes|string',
            'amount'       => 'sometimes|numeric|min:0',
            'expense_type' => 'sometimes|string',
            'occurred_at'  => 'nullable|date',
            'notes'        => 'nullable|string',
        ]);

        $expense->update($data);

        return response()->json($expense);
    }

    public function destroy(Request $request, Expense $expense)
    {
        $this->checkAccess($request, $expense->hotel_id);
        $expense->delete();

        return response()->json(['message' => 'Charge supprimée.']);
    }

    private function checkAccess(Request $request, int $hotelId): void
    {
        $user = $request->user();
        if ($user->role !== 'admin' && ! $user->getHotelIds()->contains($hotelId)) {
            abort(403);
        }
    }
}
