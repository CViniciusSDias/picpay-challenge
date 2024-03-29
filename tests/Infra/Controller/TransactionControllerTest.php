<?php

namespace App\Tests\Infra\Controller;

use App\Application\Persistence\TransactionalSession;
use App\Application\Transaction\TransactionChecker;
use App\Domain\Transaction\Transaction;
use App\Domain\User\CommonUser;
use App\Domain\User\Document\CNPJ;
use App\Domain\User\Document\CPF;
use App\Domain\User\MerchantUser;
use App\Domain\User\User;
use App\Domain\User\UserRepository;
use App\Infra\Controller\TransactionController;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Constraint\Constraint;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

#[CoversClass(TransactionController::class)]
class TransactionControllerTest extends WebTestCase
{
    private static KernelBrowser $client;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        static::$client = static::createClient();

        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $metaData = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->updateSchema($metaData);
    }

    #[Test]
    public function performing_a_transaction_must_change_the_users_balances(): void
    {
        // Arrange
        $commonUser = new CommonUser('Common user', new CPF('12345678910'), 'common@example.org', '12345678');
        $commonUser->deposit(300_00);
        $merchantUser = new MerchantUser('Merchant user', new CNPJ('12345678000190'), 'merchant@example.org', '123456');

        /** @var TransactionalSession $transaction */
        $transaction = static::getContainer()->get(TransactionalSession::class);
        $transaction->executeAtomically(function () use ($merchantUser, $commonUser) {
            /** @var UserRepository $userRepository */
            $userRepository = static::getContainer()->get(EntityManagerInterface::class)->getRepository(User::class);
            $userRepository->save($commonUser);
            $userRepository->save($merchantUser);
        });

        static::getContainer()->set(TransactionChecker::class, new class implements TransactionChecker
        {
            public function authorize(Transaction $transaction): bool
            {
                return true;
            }
        });

        // Act
        static::$client->jsonRequest('POST', '/transaction', [
            "value" => 100_00,
            "payer" => $commonUser->id,
            "payee" => $merchantUser->id
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $userRepository = static::getContainer()->get(EntityManagerInterface::class)->getRepository(User::class);
        $users = $userRepository->findAll();
        self::assertSame(200_00, $users[0]->getBalance());
        self::assertSame(100_00, $users[1]->getBalance());
    }
}
