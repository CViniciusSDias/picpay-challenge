<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\User\Document\CNPJ;

class MerchantUser extends User
{
    public function __construct(string $fullName, CNPJ $document, string $email, string $password)
    {
        parent::__construct($fullName, $document, $email, $password);
    }

    public function transferTo(User $user, int $amount): void
    {
        throw new \DomainException('Lojistas não podem enviar dinheiro. Apenas receber.');
    }
}
