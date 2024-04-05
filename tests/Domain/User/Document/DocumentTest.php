<?php

declare(strict_types=1);

namespace App\Tests\Domain\User\Document;

use App\Domain\User\Document\CNPJ;
use App\Domain\User\Document\CPF;
use App\Domain\User\Document\Document;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Document::class), CoversClass(CPF::class), CoversClass(CNPJ::class)]
class DocumentTest extends TestCase
{
    #[Test]
    #[DataProvider('invalidCpfs')]
    public function creating_an_invalid_cpf_should_throw_an_exception(string $number): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('CPF inválido: ' . $number);

        new CPF($number);
    }

    #[Test]
    #[DataProvider('invalidCnpjs')]
    public function creating_an_invalid_cnpj_should_throw_an_exception(string $number): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('CNPJ inválido: ' . $number);

        new CNPJ($number);
    }

    public static function invalidCpfs(): iterable
    {
        return [
            ['invalid'],
            [' 12345678910 '],
            [' 12345678910'],
            ['12345678910 '],
        ];
    }

    public static function invalidCnpjs(): iterable
    {
        return [
            ['invalid'],
            [' 12345678000190 '],
            [' 12345678000190'],
            ['12345678000190 '],
        ];
    }
}
