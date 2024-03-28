<?php

declare(strict_types=1);

namespace App\Tests\Domain\User\Document;

use App\Domain\User\Document\CNPJ;
use App\Domain\User\Document\CPF;
use App\Domain\User\Document\Document;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Document::class), CoversClass(CPF::class), CoversClass(CNPJ::class)]
class DocumentTest extends TestCase
{
    #[Test]
    public function creating_an_invalid_cpf_should_throw_an_exception(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('CPF inválido');

        new CPF('invalid');
    }

    #[Test]
    public function creating_an_invalid_cnpj_should_throw_an_exception(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('CNPJ inválido');

        new CNPJ('invalid');
    }
}
