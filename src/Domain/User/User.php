<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\Transaction\Transaction;
use App\Domain\User\Document\Document;
use Symfony\Component\Uid\Ulid;

abstract class User
{
    private Ulid $surrogateId;
    protected int $balance;

    public function __construct(
        public readonly string $fullName,
        public readonly Document $document,
        public readonly string $email,
        public readonly string $password,
    ) {
        $this->balance = 0;
    }

    abstract public function transferTo(User $user, int $valueInCents): Transaction;

    public function deposit(int $amount): void
    {
        if ($amount < 0) {
            throw new \DomainException('Valor deve ser positivo');
        }

        $this->balance += $amount;
    }

    public function balance(): int
    {
        return $this->balance;
    }
}
