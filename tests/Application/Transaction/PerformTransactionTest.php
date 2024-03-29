<?php

declare(strict_types=1);

namespace App\Tests\Application\Transaction;

use App\Application\Persistence\TransactionalSession;
use App\Application\Transaction\PerformTransaction;
use App\Application\Transaction\PerformTransactionDTO;
use App\Application\Transaction\TransactionChecker;
use App\Application\Transaction\UserTransactionNotifier;
use App\Domain\Transaction\Transaction;
use App\Domain\Transaction\TransactionRepository;
use App\Domain\User\CommonUser;
use App\Domain\User\Document\CPF;
use App\Domain\User\UserRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(PerformTransaction::class), CoversClass(PerformTransactionDTO::class)]
class PerformTransactionTest extends TestCase
{
    private static TransactionalSession $fakeTransactionalSession;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$fakeTransactionalSession = new class implements TransactionalSession {
            public function executeAtomically(callable $operation): mixed
            {
                return $operation();
            }
        };
    }

    #[Test]
    public function if_the_external_service_denies_the_transaction_an_exception_should_be_thrown(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Transação não autorizada pelo serviço externo. Operação cancelada.');

        $user1 = new CommonUser('Test Name', new CPF('12345678910'), 'email@example.org', '123456');
        $user1->deposit(1);

        $userRepository = $this->createStub(UserRepository::class);
        $userRepository->method('findUsersByIds')
            ->willReturn([$user1, $user1]);

        $validateTransfer = $this->createStub(TransactionChecker::class);
        $validateTransfer->method('authorize')
            ->willReturn(false);

        $transactionRepository = $this->createMock(TransactionRepository::class);
        $transactionRepository->expects($this->never())
            ->method('add');

        $userTransactionNotifier = $this->createMock(UserTransactionNotifier::class);
        $userTransactionNotifier->expects($this->never())
            ->method('notify');

        $sut = new PerformTransaction($userRepository, $validateTransfer, $transactionRepository, $userTransactionNotifier, self::$fakeTransactionalSession);

        $sut->execute(new PerformTransactionDTO(1, '0', '1'));
    }

    #[Test]
    public function performing_a_transaction_should_store_the_data_and_notify_the_user(): void
    {
        $user1 = new CommonUser('Test Name', new CPF('12345678910'), 'email@example.org', '123456');
        $user1->deposit(1);

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->method('findUsersByIds')
            ->willReturn([$user1, $user1]);
        $userRepository->expects($this->exactly(2))
            ->method('save')
            ->with($user1);

        $validateTransfer = $this->createStub(TransactionChecker::class);
        $validateTransfer->method('authorize')
            ->willReturn(true);

        $transactionRepository = $this->createMock(TransactionRepository::class);
        $transactionRepository->expects($this->once())
            ->method('add');

        $userTransactionNotifier = $this->createMock(UserTransactionNotifier::class);
        $userTransactionNotifier->expects($this->once())
            ->method('notify');

        $sut = new PerformTransaction($userRepository, $validateTransfer, $transactionRepository, $userTransactionNotifier, self::$fakeTransactionalSession);
        $sut->execute(new PerformTransactionDTO(1, '0', '1'));
    }

    #[Test]
    public function performing_a_transaction_with_float_value_should_convert_it_to_cents(): void
    {
        $user1 = new CommonUser('Test Name', new CPF('12345678910'), 'email@example.org', '123456');
        $user1->deposit(1_00);

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->method('findUsersByIds')
            ->willReturn([$user1, $user1]);
        $userRepository->expects($this->exactly(2))
            ->method('save')
            ->with($user1);

        $validateTransfer = $this->createStub(TransactionChecker::class);
        $validateTransfer->method('authorize')
            ->willReturn(true);

        $transactionRepository = $this->createMock(TransactionRepository::class);
        $transactionRepository->expects($this->once())
            ->method('add')
            ->willReturnCallback(fn (Transaction $transaction) => self::assertSame(1_00, $transaction->valueInCents));

        $userTransactionNotifier = $this->createMock(UserTransactionNotifier::class);
        $userTransactionNotifier->expects($this->once())
            ->method('notify');

        $sut = new PerformTransaction($userRepository, $validateTransfer, $transactionRepository, $userTransactionNotifier, self::$fakeTransactionalSession);

        $sut->execute(new PerformTransactionDTO(1.00, '0', '1'));
    }
}
