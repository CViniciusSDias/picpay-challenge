<?php

declare(strict_types=1);

namespace App\Domain\Transaction;

use App\Domain\User\CommonUser;
use App\Domain\User\User;

readonly class Transaction
{
    public function __construct(
        public CommonUser $sender,
        public User $receiver,
        public int $valueInCents
    ) {
    }
}
