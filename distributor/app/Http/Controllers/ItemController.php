<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
public function index(Request $request)
{
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    $outgoingSubquery = DB::table('outgoing_goods')
        ->select('item_id', DB::raw('SUM(quantity) as total_outgoing'))
        ->groupBy('item_id');

    $query = DB::table('items')
        ->leftJoinSub($outgoingSubquery, 'outgoing', function ($join) {
            $join->on('items.id', '=', 'outgoing.item_id');
        });

    if ($startDate) {
        $query->whereDate('items.created_at', '>=', $startDate);
    }
    if ($endDate) {
        $query->whereDate('items.created_at', '<=', $endDate);
    }

        $items = $query->select(
            'items.id',
            'items.name',
            'items.category',
            'items.stock',
            'items.unit',
            'items.minimum_stock',
            'items.price',
            'items.created_at',
            DB::raw('COALESCE(outgoing.total_outgoing, 0) as total_outgoing')
        )
        ->get();

    return view('items.index', compact('items'));
}

    public function create()
    {
        $items = Item::all();
        return view('items.create', compact('items'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->role === 'employee') {
            $request->validate([
                'existing_item' => 'required|exists:items,id',
                'stock' => 'required|integer|min:1',
            ]);

            $item = Item::findOrFail($request->existing_item);
            $item->stock += $request->stock;
            $item->save();

            return redirect()->route('items.index')->with('success', 'Stok berhasil ditambahkan.');
        } elseif ($user->role === 'admin') {
            $request->validate([
                'new_item_name' => 'required|string',
                'category' => 'nullable|string',
                'unit' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'minimum_stock' => 'nullable|integer|min:0',
            ]);

            Item::create([
                'name' => $request->new_item_name,
                'category' => $request->category,
                'stock' => 0,
                'unit' => $request->unit,
                'price' => $request->price,
                'minimum_stock' => $request->minimum_stock ?? 0,
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('items.index')->with('success', 'Barang berhasil ditambahkan.');
        } else {
            // Other roles cannot add stock or create items
            return redirect()->route('items.index')->with('error', 'Unauthorized action. Only employees can add stock.');
        }
    }

    public function edit(Item $item)
    {
        return view('items.edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        $user = auth()->user();

        if ($user->role === 'employee') {
            // Employees can only update stock
            $request->validate([
                'stock' => 'required|integer|min:0',
            ]);

            $item->stock = $request->stock;
            $item->save();

            return redirect()->route('items.index')->with('success', 'Stok berhasil diperbarui.');
        } elseif ($user->role === 'admin') {
            $request->validate([
                'name' => 'required|string',
                'category' => 'nullable|string',
                'stock' => 'required|integer|min:0',
                'unit' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'minimum_stock' => 'nullable|integer|min:0',
            ]);

            $item->update($request->only(['name', 'category', 'stock', 'unit', 'price', 'minimum_stock']));

            return redirect()->route('items.index')->with('success', 'Barang berhasil diperbarui.');
        } else {
            return redirect()->route('items.index')->with('error', 'Unauthorized action.');
        }
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.index')->with('success', 'Barang berhasil dihapus.');
    }
}
