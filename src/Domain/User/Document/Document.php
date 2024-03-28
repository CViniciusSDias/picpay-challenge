<?php

declare(strict_types=1);

namespace App\Domain\User\Document;

abstract class Document
{
    public function __construct(public readonly string $number)
    {
        $this->validate();
    }

    /**
     * @throws \DomainException If the document is invalid
     */
    abstract public function validate(): void;
}
