<?php

declare(strict_types=1);

namespace App\Infra\Persistence;

use App\Domain\User\Document\CNPJ;
use App\Domain\User\Document\CPF;
use App\Domain\User\Document\Document;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class DocumentType extends Type
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }

    public function getName(): string
    {
        return 'document';
    }

    /**
     * @throws \UnhandledMatchError If value isn't a string or has an unknown length
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): Document
    {
        return match (strlen($value)) {
            11 => new CPF($value),
            14 => new CNPJ($value),
        };
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return $value->number;
    }
}
