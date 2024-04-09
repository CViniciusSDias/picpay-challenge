<?php

declare(strict_types=1);

namespace App\Domain\Transaction;

use App\Domain\User\CommonUser;
use App\Domain\User\User;
use Symfony\Component\Uid\Ulid;
use DateTimeImmutable;

readonly class Transaction
{
    /**
     * @var Ulid Surrogate ID used for persistence
     */
    public Ulid $id;
    public DateTimeImmutable $createdAt;

    public function __construct(
        public CommonUser $sender,
        public User $receiver,
        public int $valueInCents
    ) {
        $this->createdAt = new DateTimeImmutable();
    }
}
