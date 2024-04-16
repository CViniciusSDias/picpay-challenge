<?php

declare(strict_types=1);

namespace App\Infra\Controller;

use App\Domain\User\CommonUser;
use App\Domain\User\Document\CNPJ;
use App\Domain\User\Document\CPF;
use App\Domain\User\MerchantUser;
use App\Domain\User\User;
use App\Domain\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @internal Esse controller só serve para analisar os dados. Não faz parte real da aplicação / do desafio.
 * @codeCoverageIgnore
 */
class UserController extends AbstractController
{
    public function __construct(private UserRepository $userRepository, private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/users', name: 'app_get_user', methods: ['GET'], format: 'json')]
    public function allUsers(): JsonResponse
    {
        return $this->json($this->userRepository->findAll());
    }

    #[Route('/users', name: 'app_create_user', methods: ['POST'], format: 'json')]
    public function createUser(#[MapRequestPayload] UserCreateDTO $request): JsonResponse
    {
        $user = match ($request->type) {
            'common' => new CommonUser($request->fullName, new CPF($request->documentNumber), $request->email, $request->password),
            'merchant' => new MerchantUser($request->fullName, new CNPJ($request->documentNumber), $request->email, $request->password),
        };

        $user->deposit($request->initialBalance);
        $this->userRepository->save($user);
        $this->entityManager->flush();
        return $this->json($user);
    }
}

readonly class UserCreateDTO
{
    public function __construct(
        public string $type,
        public string $fullName,
        public string $documentNumber,
        public string $email,
        public string $password,
        public int $initialBalance
    )
    {
    }
}
