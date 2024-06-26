<?php

declare(strict_types=1);

namespace App\Application\Transaction;

use App\Domain\Transaction\Transaction;

interface TransactionChecker
{
    public function authorize(Transaction $transaction): bool;
}
