<?php

declare(strict_types=1);

namespace App\Domain\User\Document;

class CPF extends Document
{
    public function validate(): void
    {
        $isValid = preg_match('/^\d{11}$/', $this->number);

        if (!$isValid) {
            throw new \DomainException('CPF inválido: ' . $this->number);
        }
    }
}
