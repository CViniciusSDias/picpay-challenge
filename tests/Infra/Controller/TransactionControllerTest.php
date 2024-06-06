<?php

namespace App\Tests\Infra\Controller;

use App\Application\Transaction\TransactionChecker;
use App\Domain\Transaction\Transaction;
use App\Domain\User\CommonUser;
use App\Domain\User\Document\CNPJ;
use App\Domain\User\Document\CPF;
use App\Domain\User\MerchantUser;
use App\Domain\User\User;
use App\Domain\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Ulid;

class TransactionControllerTest extends WebTestCase
{
    #[Test]
    public function performing_a_transaction_must_change_the_users_balances(): void
    {
        // Arrange
        $kernelBrowser = static::createClient();

        $commonUser = new CommonUser('Common user', new CPF('12345678910'), 'common@example.org', '12345678');
        $commonUser->deposit(300_00);

        $merchantUser = new MerchantUser('Merchant user', new CNPJ('12345678000190'), 'merchant@example.org', '123456');

        /** @var EntityManagerInterface $entityManager */
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        /** @var UserRepository&EntityRepository<User> $userRepository */
        $userRepository = $entityManager->getRepository(User::class);

        $transactionRepository = $entityManager->getRepository(Transaction::class);

        $userRepository->save($commonUser);
        $userRepository->save($merchantUser);
        $entityManager->flush();

        $fakeTransactionChecker = new class implements TransactionChecker {
            public function authorize(Transaction $transaction): bool
            {
                return true;
            }
        };
        static::getContainer()->set(TransactionChecker::class, $fakeTransactionChecker);

        // Act
        $kernelBrowser->jsonRequest('POST', '/transaction', [
            "value" => 100_00,
            "payer" => (string) $commonUser->id,
            "payee" => (string) $merchantUser->id
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $users = $userRepository->findAll();
        $transactions = $transactionRepository->findBy([
            'sender' => $commonUser,
            'receiver' => $merchantUser,
            'valueInCents' => 100_00
        ]);
        $responseContent = (string) $kernelBrowser->getResponse()->getContent();
        /** @var array{status: string, data: string} $responseBody */
        $responseBody = json_decode($responseContent, true);
        self::assertSame('ok', $responseBody['status']);
        self::assertArrayHasKey('data', $responseBody);
        self::assertSame(200_00, $users[0]->getBalance());
        self::assertSame(100_00, $users[1]->getBalance());
        self::assertCount(1, $transactions);
    }

    #[Test]
    public function trying_to_access_endpoint_with_missing_data_must_respond_with_error(): void
    {
        // Arrange
        $kernelBrowser = static::createClient();

        // Act
        $kernelBrowser->jsonRequest('POST', '/transaction');

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    #[Test]
    public function trying_to_access_endpoint_without_valid_users_must_respond_with_error(): void
    {
        // Arrange
        $kernelBrowser = static::createClient();

        // Act
        $kernelBrowser->jsonRequest('POST', '/transaction', [
            'value' => 100,
            'payer' => new Ulid(),
            'payee' => new Ulid(),
        ]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $responseBody = (string) $kernelBrowser->getResponse()->getContent();
        self::assertJson($responseBody);
        /** @var array{status: string, message: string} $parsedBody */
        $parsedBody = json_decode($responseBody, true);
        self::assertSame('error', $parsedBody['status']);
        self::assertSame('ID(s) de usuário(s) inválido(s)', $parsedBody['message']);
    }
}
