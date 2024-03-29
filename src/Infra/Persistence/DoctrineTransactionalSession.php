<?php

declare(strict_types=1);

namespace App\Infra\Persistence;
use App\Application\Persistence\TransactionalSession;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineTransactionalSession implements TransactionalSession
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function executeAtomically(callable $operation): void
    {
        $this->entityManager->wrapInTransaction($operation);
    }
}
