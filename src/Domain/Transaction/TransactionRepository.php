<?php

declare(strict_types=1);

namespace App\Domain\Transaction;

interface TransactionRepository
{
    public function add(Transaction $transaction): void;
}
