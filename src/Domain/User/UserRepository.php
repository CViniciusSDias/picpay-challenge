<?php

declare(strict_types=1);

namespace App\Domain\User;

interface UserRepository
{

    /**
     * @param string[] $ids List of ULIDs as strings
     * @return array<string, User> User list with the ULID (as string) being the key
     */
    public function findUsersByIds(array $ids): array;

    public function save(User $user): void;
}
