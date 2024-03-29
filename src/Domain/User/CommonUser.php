<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\Transaction\Transaction;
use App\Domain\User\Document\CPF;

class CommonUser extends User
{
    public function __construct(string $fullName, CPF $document, string $email, string $password)
    {
        parent::__construct($fullName, $document, $email, $password);
    }

    public function transferTo(User $user, int $valueInCents): Transaction
    {
        if ($valueInCents > $this->balance) {
            throw new \DomainException('Saldo insuficiente');
        }

        $user->deposit($valueInCents);
        $this->balance -= $valueInCents;

        return new Transaction($this, $user, $valueInCents);
    }
}
