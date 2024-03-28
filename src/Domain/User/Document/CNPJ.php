<?php

declare(strict_types=1);

namespace App\Domain\User\Document;

class CNPJ extends Document
{
    public function validate(): void
    {
        $isValid = preg_match('/^\d{14}$/', $this->number);

        if (!$isValid) {
            throw new \DomainException('CNPJ inválido: ' . $this->number);
        }
    }
}
