<?php

declare(strict_types=1);

namespace App\Infra\Persistence;

use App\Application\Persistence\TransactionalSession;
use Doctrine\ORM\EntityManagerInterface;
use Override;

class DoctrineTransactionalSession implements TransactionalSession
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    #[Override]
    public function executeAtomically(callable $operation): mixed
    {
        return $this->entityManager->wrapInTransaction($operation);
    }
}
