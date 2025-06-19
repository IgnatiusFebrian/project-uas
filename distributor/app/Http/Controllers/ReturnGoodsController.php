<?php

namespace App\Http\Controllers;

use App\Models\ReturnGoods;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

        $item = Item::findOrFail($validated['item_id']);
        $item->stock += $validated['quantity'];
        $item->save();

        $transaction = new \App\Models\Transaction();
        $transaction->user_id = $validated['user_id'];

        $transaction->type = 'keluar';

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

        $returnGoods->transaction_id = $transaction->id;
        $returnGoods->transaction_detail_id = $transactionDetail->id;
        $returnGoods->save();

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
        Log::info('Update method called for ReturnGoods id: ' . $id);
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'date' => 'required|date',
            'reason' => 'required|string|max:255',
        ]);

        Log::info('Validation passed', $validated);

        DB::beginTransaction();
        try {
            $returnGoods = ReturnGoods::findOrFail($id);
            Log::info('Found ReturnGoods', ['returnGoods' => $returnGoods]);

            $item = Item::findOrFail($validated['item_id']);
            Log::info('Found Item', ['item' => $item]);

            $totalOutgoing = \App\Models\OutgoingGoods::where('item_id', $validated['item_id'])->sum('quantity');
            Log::info('Total outgoing quantity', ['totalOutgoing' => $totalOutgoing]);

            $totalReturned = ReturnGoods::where('item_id', $validated['item_id'])
                ->where('id', '!=', $id)
                ->sum('quantity');
            Log::info('Total returned quantity excluding current', ['totalReturned' => $totalReturned]);

            if ($totalReturned + $validated['quantity'] > $totalOutgoing) {
                Log::warning('Return quantity exceeds total outgoing quantity', ['totalReturned' => $totalReturned, 'quantity' => $validated['quantity'], 'totalOutgoing' => $totalOutgoing]);
                return back()->withErrors(['quantity' => 'Return quantity exceeds total outgoing quantity.'])->withInput();
            }

            $quantityDifference = $validated['quantity'] - $returnGoods->quantity;
            Log::info('Quantity difference', ['quantityDifference' => $quantityDifference]);
            if ($quantityDifference > 0) {
                $item->stock += $quantityDifference;
            } elseif ($quantityDifference < 0) {
                $item->stock += $quantityDifference;
            }
            $item->save();
            Log::info('Item stock updated', ['stock' => $item->stock]);

            $validated['user_id'] = Auth::id();

            $returnTransaction = \App\Models\Transaction::find($returnGoods->transaction_id);
            $returnTransactionDetail = \App\Models\TransactionDetail::find($returnGoods->transaction_detail_id);

            if ($returnTransaction && $returnTransactionDetail) {
                $returnTransaction->total_price += -($item->price * $quantityDifference);
                $returnTransaction->save();

                $returnTransactionDetail->quantity += -$quantityDifference;
                $returnTransactionDetail->total_price += -($item->price * $quantityDifference);
                $returnTransactionDetail->save();
            } else {
                $transaction = new \App\Models\Transaction();
                $transaction->user_id = $validated['user_id'];
                $transaction->type = 'keluar';
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

                // Link return goods to transaction and transaction detail
                $returnGoods->transaction_id = $transaction->id;
                $returnGoods->transaction_detail_id = $transactionDetail->id;
                $returnGoods->save();
            }

            $returnGoods->update($validated);
            Log::info('ReturnGoods updated', ['returnGoods' => $returnGoods]);

            DB::commit();
            return redirect()->route('returns.index')
                ->with('success', 'Return updated successfully and profit adjusted.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in update method: ' . $e->getMessage());
            return back()
                ->withInput()
                ->withErrors(['error' => 'Error occurred: ' . $e->getMessage()]);
        }
    }
    public function destroy($id)
    {
        $returnGoods = ReturnGoods::findOrFail($id);
        $item = $returnGoods->item;

        if ($item) {
            $item->stock -= $returnGoods->quantity;
            $item->save();
        }

        $returnTransaction = \App\Models\Transaction::find($returnGoods->transaction_id);
        $returnTransactionDetail = \App\Models\TransactionDetail::find($returnGoods->transaction_detail_id);

        if ($returnTransactionDetail) {
            $returnTransactionDetail->delete();
        }
        if ($returnTransaction) {
            $returnTransaction->delete();
        }

        $returnGoods->delete();

        return redirect()->route('returns.index')
            ->with('success', 'Return deleted successfully.');
    }
}
