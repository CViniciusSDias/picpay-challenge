<?php

declare(strict_types=1);

namespace App\Application\Transaction;

use App\Domain\Transaction\Transaction;

interface UserTransactionNotifier
{
    public function notify(Transaction $transaction): void;
}
