<?php

declare(strict_types=1);

namespace App\Infra\Transaction;

use App\Application\Transaction\TransactionChecker;
use App\Domain\Transaction\Transaction;

/**
 * @codeCoverageIgnore
 */
readonly class FakeTransactionChecker implements TransactionChecker
{
    /**
     * @param int $proportion Success rate of the fake checker in percentage
     */
    public function __construct(private int $proportion = 70)
    {
    }

    public function authorize(Transaction $transaction): bool
	{
		return rand(1, 100) <= $this->proportion;
	}
}
