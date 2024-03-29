<?php

declare(strict_types=1);

namespace App\Application\Persistence;

interface TransactionalSession
{

    /**
     * @template T
     * @param callable(): T $operation
     * @return T
     */
    public function executeAtomically(callable $operation): mixed;
}
