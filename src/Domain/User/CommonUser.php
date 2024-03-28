<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\User\Document\CPF;

class CommonUser extends User
{
    public function __construct(string $fullName, CPF $document, string $email, string $password)
    {
        parent::__construct($fullName, $document, $email, $password);
    }

    public function transferTo(User $user, int $amount): void
    {
        if ($amount > $this->balance) {
            throw new \DomainException('Saldo insuficiente');
        }

        $user->deposit($amount);
        $this->balance -= $amount;
    }
}
