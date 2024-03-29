<?php

declare(strict_types=1);

namespace App\Application\Transaction;

readonly class PerformTransactionDTO
{
    public function __construct(public int|float $value, public string $payer, public string $payee)
    {
    }
}
