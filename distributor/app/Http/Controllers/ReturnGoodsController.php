<?php

namespace App\Http\Controllers;

use App\Models\ReturnGoods;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReturnGoodsController extends Controller
{
    public function index()
    {
        $returns = ReturnGoods::with(['item', 'user'])->latest()->get();
        return view('returns.index', compact('returns'));
    }

    public function create()
    {
        $items = Item::whereHas('outgoingGoods')->get();
        return view('returns.create', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'date' => 'required|date',
            'reason' => 'required|string|max:255',
        ]);

        $validated['user_id'] = Auth::id();

        $returnGoods = ReturnGoods::create($validated);

        // Increase item stock on return
        $item = Item::findOrFail($validated['item_id']);
        $item->stock += $validated['quantity'];
        $item->save();

        return redirect()->route('returns.index')->with('success', 'Return recorded successfully.');
    }

    public function edit($id)
    {
        $returnGoods = ReturnGoods::findOrFail($id);
        $items = Item::all();
        return view('returns.edit', compact('returnGoods', 'items'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'date' => 'required|date',
            'reason' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $returnGoods = ReturnGoods::findOrFail($id);
            $item = Item::findOrFail($validated['item_id']);

            // Calculate total outgoing quantity for the item
            $totalOutgoing = \App\Models\OutgoingGoods::where('item_id', $validated['item_id'])->sum('quantity');

            // Calculate total returned quantity for the item excluding current return
            $totalReturned = ReturnGoods::where('item_id', $validated['item_id'])
                ->where('id', '!=', $id)
                ->sum('quantity');

            // Check if new return quantity exceeds available outgoing quantity
            if ($totalReturned + $validated['quantity'] > $totalOutgoing) {
                return back()->withErrors(['quantity' => 'Return quantity exceeds total outgoing quantity.'])->withInput();
            }

            // Adjust stock based on quantity difference
            $quantityDifference = $validated['quantity'] - $returnGoods->quantity;

            // For returns, increase stock if quantityDifference > 0, decrease if < 0
            if ($quantityDifference > 0) {
                $item->stock += $quantityDifference;
            } elseif ($quantityDifference < 0) {
                $item->stock += $quantityDifference; // quantityDifference is negative, so this decreases stock
            }

            $item->save();

            $validated['user_id'] = Auth::id();

            // Adjust profit by updating or creating a negative transaction for the return
            $returnTransaction = \App\Models\Transaction::where('type', 'return')
                ->where('date', $returnGoods->date)
                ->where('user_id', $returnGoods->user_id)
                ->first();

            $returnTransactionDetail = null;
            if ($returnTransaction) {
                $returnTransactionDetail = \App\Models\TransactionDetail::where('transaction_id', $returnTransaction->id)
                    ->where('item_id', $item->id)
                    ->first();
            }

            if ($returnTransaction && $returnTransactionDetail) {
                // Update existing negative transaction and detail
                $returnTransaction->total_price += -($item->price * $quantityDifference);
                $returnTransaction->save();

                $returnTransactionDetail->quantity += -$quantityDifference;
                $returnTransactionDetail->total_price += -($item->price * $quantityDifference);
                $returnTransactionDetail->save();
            } else {
                // Create new negative transaction and detail
                $transaction = new \App\Models\Transaction();
                $transaction->user_id = $validated['user_id'];
                $transaction->type = 'return';
                $transaction->date = $validated['date'];
                $transaction->total_price = -($item->price * $validated['quantity']);
                $transaction->save();

                $transactionDetail = new \App\Models\TransactionDetail();
                $transactionDetail->transaction_id = $transaction->id;
                $transactionDetail->item_id = $item->id;
                $transactionDetail->quantity = -$validated['quantity'];
                $transactionDetail->price = $item->price;
                $transactionDetail->total_price = -($item->price * $validated['quantity']);
                $transactionDetail->save();
            }

            $returnGoods->update($validated);

            DB::commit();
            return redirect()->route('returns.index')
                ->with('success', 'Return updated successfully and profit adjusted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Error occurred: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $returnGoods = ReturnGoods::findOrFail($id);
        $item = $returnGoods->item;

        // Adjust stock
        if ($item) {
            // Decrease stock by return quantity on delete, because return previously increased stock
            $item->stock -= $returnGoods->quantity;
            $item->save();
        }

        $returnGoods->delete();

        return redirect()->route('returns.index')
            ->with('success', 'Return deleted successfully.');
    }
}
