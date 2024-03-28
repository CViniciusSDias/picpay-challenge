<?php

declare(strict_types=1);

namespace App\Domain\User;

interface UserRepository
{

    /**
     * @param int[] $ids
     * @return array<int, User>
     */
    public function findUsersByIds(array $ids): array;
}
