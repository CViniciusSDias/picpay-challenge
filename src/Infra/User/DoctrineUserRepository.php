<?php

declare(strict_types=1);

namespace App\Infra\User;

use App\Domain\User\User;
use App\Domain\User\UserRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Uid\Ulid;

/**
 * @template-extends ServiceEntityRepository<User>
 */
class DoctrineUserRepository extends ServiceEntityRepository implements UserRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findUsersByIds(array $ids): array
    {
        $ulids = array_map(fn (string $id) => new Ulid($id), $ids);
        $queryBuilder = $this->createQueryBuilder('user')
            ->select('user');

        foreach ($ulids as $i => $ulid) {
            $paramIndex = $i + 1;
            $queryBuilder->orWhere('user.id = ?' . $paramIndex);
            $queryBuilder->setParameter($paramIndex, $ulid, UlidType::NAME);
        }

        /** @var User[] $users */
        $users = $queryBuilder
            ->getQuery()
            ->setLockMode(LockMode::PESSIMISTIC_WRITE)
            ->getResult();

        $return = [];
        foreach ($users as $user) {
            $return[(string) $user->id] = $user;
        }

        return $return;
    }

    public function save(User $user): void
    {
        if (!$this->getEntityManager()->contains($user)) {
            $this->getEntityManager()->persist($user);
        }
    }
}
