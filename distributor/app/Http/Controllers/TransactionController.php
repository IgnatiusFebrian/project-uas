<?php

 namespace App\Http\Controllers;

 use App\Models\Transaction;
 use App\Models\TransactionDetail;
 use App\Models\Item;
 use Illuminate\Http\Request;
 use Illuminate\Support\Facades\DB;

 class TransactionController extends Controller
 {
    public function index()
    {
        // Fetch incoming goods and outgoing goods with related item and user
        $incomingGoods = \App\Models\IncomingGoods::with('item', 'user')->get()->map(function ($incoming) {
            $incoming->type = 'Masuk';
            return $incoming;
        });

        $outgoingGoods = \App\Models\OutgoingGoods::with('item', 'user')->get()->map(function ($outgoing) {
            $outgoing->type = 'Keluar';
            return $outgoing;
        });

        // Merge and sort by date descending
        $combined = $incomingGoods->merge($outgoingGoods)->sortByDesc('date')->values();

        return view('transactions.index', ['combined' => $combined]);
    }

    public function report()
    {
        // Aggregate transactions by type and date
        $transactionSummary = Transaction::selectRaw('type, date, COUNT(*) as count, SUM(total_price) as total')
            ->groupBy('type', 'date')
            ->orderBy('date', 'desc')
            ->get();
       // Get current stock summary for all items
        $stockSummary = \App\Models\Item::select('name', 'stock')->get();

        $incomingTransactions = \App\Models\IncomingGoods::with('item', 'user')->get();

        // Fetch all transactions for listing
        $transactions = Transaction::with('details')->orderBy('date', 'desc')->get();

       return view('transactions.report', compact('transactionSummary', 'stockSummary', 'incomingTransactions', 'transactions'));
    }

    public function create()
    {
        $items = Item::all();
        return view('transactions.create', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:masuk,keluar',
           'date' => 'required|date',
            'items' => 'required|array',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.item_id' => 'nullable|exists:items,id',
            'items.*.new_item_name' => 'nullable|string',
        ]);

        if (!auth()->user()->is_admin) {
            // Non-admin users must use existing prices
            foreach ($request->items as $item) {
                if (!empty($item['item_id'])) {
                    $dbItem = Item::findOrFail($item['item_id']);
                    $item['price'] = $dbItem->price;
                } else {
                    return back()->withErrors('Only administrators can create new items.');
                }
            }
        }

        DB::beginTransaction();

        try {
            $totalPrice = 0;
            foreach ($request->items as $item) {
                $totalPrice += $item['quantity'] * $item['price'];
            }

            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                'type' => $request->type,
               'date' => $request->date,
                'total_price' => $totalPrice,
            ]);

            foreach ($request->items as $item) {
                if (!empty($item['new_item_name'])) {
                    $itemModel = Item::create([
                        'name' => $item['new_item_name'],
                        'stock' => 0,
                        'price' => $item['price'],
                    ]);
                   $itemId = $itemModel->id;
                } else {
                    $itemId = $item['item_id'];
                    $itemModel = Item::find($itemId);
                }

                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'item_id' => $itemId,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total_price' => $item['quantity'] * $item['price'],
                ]);

                // Update stock
                if ($request->type === 'masuk') {
                    $itemModel->stock += $item['quantity'];

                    // Also create incoming goods record
                    \App\Models\IncomingGoods::create([
                        'item_id' => $itemId,
                        'quantity' => $item['quantity'],
                        'date' => $request->date,
                        'notes' => 'Created from transaction ID ' . $transaction->id,
                    ]);
                } else {
                    $itemModel->stock -= $item['quantity'];
                }
                $itemModel->save();
            }
            DB::commit();

           return redirect()->route('transactions.index')->with('success', 'Transaction created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Failed to create transaction: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Transaction $transaction)
    {
        $transaction->load('details.item', 'user');
        return view('transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction)
    {
        $items = Item::all();
        $transaction->load('details');
        return view('transactions.edit', compact('transaction', 'items'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        // For simplicity, disallow update for now or implement similar to store with stock adjustments
        return back()->withErrors('Update not implemented yet.');
    }

    public function destroy(Transaction $transaction)
    {
        // For simplicity, disallow delete for now or implement stock rollback
       return back()->withErrors('Delete not implemented yet.');
    }
 }
