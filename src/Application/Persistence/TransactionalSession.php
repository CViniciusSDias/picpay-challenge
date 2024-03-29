<?php

declare(strict_types=1);

namespace App\Application\Persistence;

interface TransactionalSession
{
    public function executeAtomically(callable $operation): void;
}
