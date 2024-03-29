<?php

declare(strict_types=1);

namespace App\Application\Transaction;

use App\Domain\Transaction\TransactionRepository;
use App\Domain\User\UserRepository;

class PerformTransaction
{
    public function __construct(
        private UserRepository $userRepository,
        private TransactionChecker $validateTransfer,
        private TransactionRepository $transactionRepository,
        private UserTransactionNotifier $userTransactionNotifier,
    ) {
    }

    public function execute(PerformTransactionDTO $data): void
    {
        $users = $this->userRepository->findUsersByIds([$data->payer, $data->payee]);
        $sender = $users[$data->payer];
        $receiver = $users[$data->payee];

        $transferAmountInCents = is_int($data->value)
            ? $data->value
            : intval($data->value * 100);
        $transaction = $sender->transferTo($receiver, $transferAmountInCents);

        if (!$this->validateTransfer->authorize($transaction)) {
            throw new \DomainException('Transação não autorizada pelo serviço externo. Operação cancelada.');
        }

        $this->userRepository->save($sender);
        $this->userRepository->save($receiver);
        $this->transactionRepository->add($transaction);
        $this->userTransactionNotifier->notify($transaction);
    }
}
