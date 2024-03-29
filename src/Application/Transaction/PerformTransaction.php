<?php

declare(strict_types=1);

namespace App\Application\Transaction;

use App\Domain\User\UserRepository;

class PerformTransaction
{
    public function __construct(private UserRepository $userRepository, private TransactionChecker $validateTransfer)
    {
    }

    public function execute(PerformTransactionDTO $data): void
    {
        $users = $this->userRepository->findUsersByIds([$data->payer, $data->payee]);
        $sender = $users[$data->payer];
        $receiver = $users[$data->payee];

        if (!$this->validateTransfer->validate()) {
            // TODO: exception
            return;
        }

        $transferAmountInCents = is_int($data->value)
            ? $data->value
            : $data->value * 100;
        $sender->transferTo($receiver, $transferAmountInCents);
    }
}
