<?php

declare(strict_types=1);

namespace App\Application\Transaction;

use App\Domain\Transaction\Transaction;
use App\Domain\User\User;

interface UserTransactionNotifier
{
    public function notify(User $receiver, Transaction $transaction): void;
}
