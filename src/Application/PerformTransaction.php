<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\User\UserRepository;

class PerformTransaction
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function execute(PerformTransactionDTO $data): void
    {
        $users = $this->userRepository->findUsersByIds([$data->payer, $data->payee]);
        $sender = $users[$data->payer];
        $receiver = $users[$data->payee];

        $sender->transferTo($receiver, $data->value * 100);
    }
}
