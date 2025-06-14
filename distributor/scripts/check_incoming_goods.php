<?php

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Item;

// Bootstrap Laravel application
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking database records for incoming goods...\n";

$transactionsCount = Transaction::where('type', 'masuk')->count();
$transactionDetailsCount = TransactionDetail::count();
$itemsCount = Item::count();

echo "Transactions (masuk): $transactionsCount\n";
echo "Transaction Details: $transactionDetailsCount\n";
echo "Items: $itemsCount\n";
