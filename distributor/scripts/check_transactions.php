<?php

use App\Models\Transaction;
use App\Models\TransactionDetail;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$latestTransaction = Transaction::latest()->first();

if ($latestTransaction) {
    echo "Latest Transaction ID: " . $latestTransaction->id . PHP_EOL;
    echo "Type: " . $latestTransaction->type . PHP_EOL;
    echo "Date: " . $latestTransaction->date . PHP_EOL;
    echo "Total Price: " . $latestTransaction->total_price . PHP_EOL;

    $details = TransactionDetail::where('transaction_id', $latestTransaction->id)->get();

    echo "Transaction Details:" . PHP_EOL;
    foreach ($details as $detail) {
        echo "- Item ID: " . $detail->item_id . ", Quantity: " . $detail->quantity . ", Price: " . $detail->price . ", Total: " . $detail->total_price . PHP_EOL;
    }
} else {
    echo "No transactions found in the database." . PHP_EOL;
}
