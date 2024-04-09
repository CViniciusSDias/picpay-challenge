<?php

declare(strict_types=1);

namespace App\Infra\Transaction;

use App\Application\Transaction\UserTransactionNotifier;
use App\Domain\Transaction\Transaction;
use App\Domain\User\User;
use Psr\Log\LoggerInterface;

/**
 * @codeCoverageIgnore
 */
class FakeUserTransactionNotifier implements UserTransactionNotifier
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function notify(User $receiver, Transaction $transaction): void
    {
        // TODO: Enviar notificação de forma assíncrona
        $this->logger->info('Notificação sobre transação', compact('receiver', 'transaction'));
    }
}
