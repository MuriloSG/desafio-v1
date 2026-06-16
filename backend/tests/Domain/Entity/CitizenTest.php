<?php

declare(strict_types=1);

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\Citizen;
use App\Domain\ValueObject\Cpf;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CitizenTest extends TestCase
{
    public function testCreatesCitizenWithValidData(): void
    {
        $cpf = new Cpf('529.982.247-25');
        $citizen = new Citizen('Maria da Silva', $cpf);

        self::assertSame('Maria da Silva', $citizen->getName());
        self::assertSame($cpf->value(), $citizen->getCpf()->value());
    }

    public function testNormalizesRedundantWhitespaceInName(): void
    {
        $citizen = new Citizen('  Maria   da  Silva  ', new Cpf('529.982.247-25'));

        self::assertSame('Maria da Silva', $citizen->getName());
    }

    public function testRejectsBlankName(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Citizen('   ', new Cpf('529.982.247-25'));
    }

    public function testRejectsNameLongerThanColumnLimit(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Citizen(str_repeat('a', 256), new Cpf('529.982.247-25'));
    }
}
