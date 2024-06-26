<?php

declare(strict_types=1);

namespace App\Infra\Transaction;

use App\Domain\Transaction\Transaction;
use App\Domain\Transaction\TransactionRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<Transaction>
 */
class DoctrineTransactionRepository extends ServiceEntityRepository implements TransactionRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function add(Transaction $transaction): void
    {
        $this->getEntityManager()->persist($transaction);
    }
}
