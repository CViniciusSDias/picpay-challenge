<?php

declare(strict_types=1);

namespace App\Domain\User;

interface UserRepository
{

    /**
     * @param string[] $ids ULIDs as strings
     * @return array<int, User>
     */
    public function findUsersByIds(array $ids): array;

    public function save(User $user): void;
}
