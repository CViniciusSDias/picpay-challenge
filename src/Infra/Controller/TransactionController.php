<?php

declare(strict_types=1);

namespace App\Infra\Controller;

use App\Application\Transaction\PerformTransaction;
use App\Application\Transaction\PerformTransactionDTO;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class TransactionController extends AbstractController
{
    public function __construct(private PerformTransaction $useCase, private LoggerInterface $logger)
    {
    }

    #[Route('/transaction', name: 'app_perform_transaction', methods: ['POST'], format: 'json')]
    public function performTransaction(#[MapRequestPayload] PerformTransactionDTO $data): JsonResponse
    {
        try {
            $transaction = $this->useCase->execute($data);
            return $this->json([
                'status' => 'ok',
                'data' => $transaction
            ], Response::HTTP_CREATED);
        } catch (\DomainException $exception) {
            return $this->json([
                'status' => 'error',
                'message' => $exception->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $throwable) {
            $this->logger->error($throwable->getMessage(), $throwable->getTrace());

            return $this->json([
                'status' => 'error',
                'message' => 'Erro desconhecido',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
