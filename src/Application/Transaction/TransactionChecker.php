<?php

declare(strict_types=1);

namespace App\Application\Transaction;

interface TransactionChecker
{
    public function validate(): bool;
}
