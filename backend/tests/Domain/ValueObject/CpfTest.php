<?php

declare(strict_types=1);

namespace App\Tests\Domain\ValueObject;

use App\Domain\ValueObject\Cpf;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CpfTest extends TestCase
{
    public function testAcceptsValidCpfAndNormalizesIt(): void
    {
        $cpf = new Cpf('529.982.247-25');

        self::assertSame('52998224725', $cpf->value());
        self::assertSame('529.982.247-25', $cpf->formatted());
    }

    #[DataProvider('invalidCpfProvider')]
    public function testRejectsInvalidCpf(string $input): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Cpf($input);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalidCpfProvider(): iterable
    {
        yield 'wrong length' => ['1234567890'];
        yield 'wrong check digit' => ['52998224726'];
        yield 'repeated digits' => ['111.111.111-11'];
    }
}
