<?php

namespace App\Tests\Domain\User;

use App\Domain\User\CommonUser;
use App\Domain\User\Document\CNPJ;
use App\Domain\User\Document\CPF;
use App\Domain\User\Document\Document;
use App\Domain\User\MerchantUser;
use App\Domain\User\User;
use App\Domain\Transaction\Transaction;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(User::class), CoversClass(CommonUser::class), CoversClass(MerchantUser::class), CoversClass(Transaction::class)]
#[UsesClass(Document::class), UsesClass(CPF::class), UsesClass(CNPJ::class)]
class UserTest extends TestCase
{
    #[Test]
    public function common_user_can_perform_a_transfer(): void
    {
        // Arrange
        $user1 = new CommonUser('Full name', new CPF('12345678910'), 'common@example.org', 'plain password');
        $user1->deposit(5_00);
        $user2 = new MerchantUser('Full name', new CNPJ('12345678000190'), 'merchant@example.org', 'plain password');

        // Act
        $transaction = $user1->transferTo($user2, 1_00);

        // Assert
        self::assertSame(1_00, $user2->getBalance());
        self::assertSame(4_00, $user1->getBalance());
        self::assertInstanceOf(Transaction::class, $transaction);
        self::assertSame(1_00, $transaction->valueInCents);
        self::assertSame($user1, $transaction->sender);
        self::assertSame($user2, $transaction->receiver);
    }

    #[Test]
    public function transfer_of_negative_value_should_throw_an_exception(): void
    {
        // Assert
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Valor deve ser positivo');

        // Arrange
        $user1 = new CommonUser('Full name', new CPF('12345678910'), 'common@example.org', 'plain password');
        $user2 = new MerchantUser('Full name', new CNPJ('12345678000190'), 'merchant@example.org', 'plain password');

        // Act
        $user1->transferTo($user2, -1_00);
    }

    #[Test]
    public function transfer_without_necessary_balance_should_throw_an_exception(): void
    {
        // Assert
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Saldo insuficiente');

        // Arrange
        $user1 = new CommonUser('Full name', new CPF('12345678910'), 'common@example.org', 'plain password');
        $user2 = new MerchantUser('Full name', new CNPJ('12345678000190'), 'merchant@example.org', 'plain password');

        // Act
        $user1->transferTo($user2, 1_00);
    }

    #[Test]
    public function trying_to_transfer_from_a_merchant_should_throw_an_exception(): void
    {
        // Assert
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Lojistas não podem enviar dinheiro. Apenas receber.');

        // Arrange
        $user1 = new CommonUser('Full name', new CPF('12345678910'), 'common@example.org', 'plain password');
        $user2 = new MerchantUser('Full name', new CNPJ('12345678000190'), 'merchant@example.org', 'plain password');

        // Act
        $user2->transferTo($user1, 1_00);
    }
}
