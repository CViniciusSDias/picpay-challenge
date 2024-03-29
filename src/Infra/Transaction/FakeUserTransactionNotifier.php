<?php

declare(strict_types=1);

namespace App\Infra\Transaction;

use App\Application\Transaction\UserTransactionNotifier;
use App\Domain\Transaction\Transaction;
use Psr\Log\LoggerInterface;

class FakeUserTransactionNotifier implements UserTransactionNotifier
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function notify(Transaction $transaction): void
    {
        $this->logger->info('Notificação sobre transação', ['transaction' => $transaction]);
        return;
    }
}
