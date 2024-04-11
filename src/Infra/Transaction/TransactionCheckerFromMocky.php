<?php

declare(strict_types=1);

namespace App\Infra\Transaction;

use App\Application\Transaction\TransactionChecker;
use App\Domain\Transaction\Transaction;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @codeCoverageIgnore
 */
readonly class TransactionCheckerFromMocky implements TransactionChecker
{
    public function __construct(private HttpClientInterface $httpClient, private ParameterBagInterface $parameterBag)
    {
    }

    public function authorize(Transaction $transaction): bool
    {
        /** @var string $transactionValidationUrl */
        $transactionValidationUrl = $this->parameterBag->get('transfer_validation.url');
        $response = $this->httpClient->request('GET', $transactionValidationUrl);
        /**
         * @var array{Autorizado: bool} $decodedBody
         * @TODO: Verificar item correto caso use a API real
         */
        $decodedBody = json_decode($response->getContent(), true);

        return $decodedBody['Autorizado'] === true;
    }
}
