<?php

declare(strict_types=1);

namespace App\Application\Transaction;

readonly class PerformTransactionDTO
{
    public function __construct(public float $value, public int $payer, public int|float $payee)
    {
    }
}
