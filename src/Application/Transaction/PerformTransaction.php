<?php

declare(strict_types=1);

namespace App\Application\Transaction;

use App\Application\Persistence\TransactionalSession;
use App\Domain\Transaction\Transaction;
use App\Domain\Transaction\TransactionRepository;
use App\Domain\User\UserRepository;

class PerformTransaction
{
    public function __construct(
        private UserRepository $userRepository,
        private TransactionChecker $validateTransfer,
        private TransactionRepository $transactionRepository,
        private UserTransactionNotifier $userTransactionNotifier,
        private TransactionalSession $transactionalSession,
    ) {
    }

    public function execute(PerformTransactionDTO $data): Transaction
    {
        $transaction = $this->transactionalSession->executeAtomically(function () use ($data): Transaction {
            $users = $this->userRepository->findUsersByIds([$data->payer, $data->payee]);
            $sender = $users[$data->payer] ?? null;
            $receiver = $users[$data->payee] ?? null;

            if ($sender === null || $receiver === null) {
                throw new \DomainException('ID(s) de usuário(s) inválido(s)');
            }

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

            return $transaction;
        });
        $this->userTransactionNotifier->notify($transaction);

        return $transaction;
    }
}
