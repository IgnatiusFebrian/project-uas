<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\IncomingGoods;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class IncomingGoodsController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Fetch all incoming goods records with related item and user data for all users
        $incomingGoods = IncomingGoods::with(['item', 'user'])->latest()->get();

        return view('incoming_goods.index', compact('incomingGoods'));
    }

    public function create()
    {
        // Get all items without filtering by user_id
        $items = Item::all();

        \Log::info('Items passed to incoming_goods.create view:', $items->toArray());
        return view('incoming_goods.create', compact('items'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'employee') {
            // Employees can only add stock to existing items, no new item creation, no price input
            $validated = $request->validate([
                'item_id' => 'required|exists:items,id',
                'quantity' => 'required|integer|min:1',
                'date' => 'required|date',
                'notes' => 'nullable|string'
            ]);

            $item = Item::find($validated['item_id']);
            if (!$item) {
                return back()->withErrors(['item_id' => 'Barang tidak ditemukan.'])->withInput();
            }

            DB::beginTransaction();
            try {
                $validated['user_id'] = $user->id;

                $incomingGoods = IncomingGoods::create([
                    'item_id' => $validated['item_id'],
                    'quantity' => $validated['quantity'],
                    'price' => $item->price, // use existing item price
                    'date' => $validated['date'],
                    'notes' => $validated['notes'] ?? null,
                    'user_id' => $user->id,
                ]);

                // Update item stock
                $item->stock += $validated['quantity'];
                $item->save();

                DB::commit();
                return redirect()->route('incoming_goods.index')
                    ->with('success', 'Barang masuk berhasil ditambahkan');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()
                    ->withInput()
                    ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
            }
        } elseif ($user->role === 'admin') {
            // Admins can create new items or add incoming goods with price input
            $validated = $request->validate([
                'item_id' => 'nullable|exists:items,id',
                'new_item_name' => 'nullable|string',
                'quantity' => 'required|integer|min:1',
                'price' => 'required|numeric|min:0',
                'date' => 'required|date',
                'notes' => 'nullable|string'
            ]);

            DB::beginTransaction();
            try {
                // Determine item_id based on new_item_name or existing item_id
                if (!empty($validated['new_item_name'])) {
                    $item = Item::create([
                        'name' => $validated['new_item_name'],
                        'stock' => 0,
                        'price' => $validated['price'],
                    ]);
                    $validated['item_id'] = $item->id;
                } elseif (!empty($validated['item_id'])) {
                    $item = Item::find($validated['item_id']);
                } else {
                    return back()->withErrors(['item_id' => 'The item id field is required.'])->withInput();
                }

                $validated['user_id'] = $user->id;

                $incomingGoods = IncomingGoods::create($validated);

                // Update item stock
                $item->stock += $validated['quantity'];
                $item->save();

                DB::commit();
                return redirect()->route('incoming_goods.index')
                    ->with('success', 'Barang masuk berhasil ditambahkan');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()
                    ->withInput()
                    ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
            }
        } else {
            return back()->withErrors(['error' => 'Unauthorized action.'])->withInput();
        }
    }

    public function edit($id)
    {
        $incomingGood = IncomingGoods::findOrFail($id);
        $items = Item::all();
        return view('incoming_goods.edit', compact('incomingGood', 'items'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->role === 'employee') {
            $validated = $request->validate([
                'quantity' => 'required|integer|min:1',
            ]);

            DB::beginTransaction();
            try {
                $incomingGood = IncomingGoods::findOrFail($id);
                $item = $incomingGood->item;

                // Adjust stock based on quantity difference
                $quantityDifference = $validated['quantity'] - $incomingGood->quantity;

                $item->stock += $quantityDifference;
                $item->save();

                $incomingGood->update([
                    'quantity' => $validated['quantity'],
                    'user_id' => $user->id,
                ]);

                DB::commit();
                return redirect()->route('incoming_goods.index')
                    ->with('success', 'Jumlah barang masuk berhasil diperbarui');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()
                    ->withInput()
                    ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
            }
        } elseif ($user->role === 'admin') {
            $validated = $request->validate([
                'item_id' => 'nullable|exists:items,id',
                'new_item_name' => 'nullable|string',
                'quantity' => 'required|integer|min:1',
                'price' => 'required|numeric|min:0',
                'date' => 'required|date',
                'notes' => 'nullable|string'
            ]);

            DB::beginTransaction();
            try {
                $incomingGood = IncomingGoods::findOrFail($id);

                // Determine item_id based on new_item_name or existing item_id
                if (!empty($validated['new_item_name'])) {
                    $item = Item::create([
                        'name' => $validated['new_item_name'],
                        'stock' => 0,
                        'price' => $validated['price'],
                    ]);
                    $validated['item_id'] = $item->id;
                } elseif (!empty($validated['item_id'])) {
                    $item = Item::find($validated['item_id']);
                } else {
                    return back()->withErrors(['item_id' => 'The item id field is required.'])->withInput();
                }

                // Adjust stock based on quantity difference
                $quantityDifference = $validated['quantity'] - $incomingGood->quantity;

                $item->stock += $quantityDifference;
                $item->save();

                $validated['user_id'] = $user->id;

                $incomingGood->update($validated);

                DB::commit();
                return redirect()->route('incoming_goods.index')
                    ->with('success', 'Barang masuk berhasil diperbarui');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()
                    ->withInput()
                    ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
            }
        } else {
            return back()->withErrors(['error' => 'Unauthorized action.'])->withInput();
        }
    }

    public function report()
    {
        $incomingTransactions = IncomingGoods::with(['item', 'user'])
            ->latest()
            ->get();
        return view('incoming_goods.report', compact('incomingTransactions'));
    }

    public function destroy($id)
    {
        $incomingGood = IncomingGoods::findOrFail($id);
        $item = $incomingGood->item;

        // Adjust stock
        if ($item) {
            $item->stock -= $incomingGood->quantity;
            $item->save();
        }

        $incomingGood->delete();

        return redirect()->route('incoming_goods.index')
            ->with('success', 'Barang masuk berhasil dihapus');
    }
}
