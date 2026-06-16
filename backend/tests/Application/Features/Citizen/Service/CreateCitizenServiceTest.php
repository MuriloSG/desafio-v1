<?php

declare(strict_types=1);

namespace App\Tests\Application\Features\Citizen\Service;

use App\Application\Exception\AppException;
use App\Application\Features\Citizen\Dto\CreateCitizenDto;
use App\Application\Features\Citizen\Repository\CitizenRepositoryInterface;
use App\Application\Features\Citizen\Service\CreateCitizenService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CreateCitizenServiceTest extends TestCase
{
    public function testCreatesCitizenWhenDataIsValid(): void
    {
        $repository = $this->createMock(CitizenRepositoryInterface::class);
        $repository->method('existsByCpf')->willReturn(false);
        $repository->expects(self::once())->method('create');

        $citizen = (new CreateCitizenService($repository))->execute(new CreateCitizenDto('Maria da Silva', '529.982.247-25'));

        self::assertSame('Maria da Silva', $citizen->getName());
        self::assertSame('52998224725', $citizen->getCpf()->value());
    }

    public function testRejectsBlankName(): void
    {
        $repository = $this->createStub(CitizenRepositoryInterface::class);

        $this->expectException(InvalidArgumentException::class);

        (new CreateCitizenService($repository))->execute(new CreateCitizenDto('   ', '52998224725'));
    }

    public function testRejectsDuplicatedCpf(): void
    {
        $repository = $this->createMock(CitizenRepositoryInterface::class);
        $repository->method('existsByCpf')->willReturn(true);
        $repository->expects(self::never())->method('create');

        $this->expectException(AppException::class);

        (new CreateCitizenService($repository))->execute(new CreateCitizenDto('Maria da Silva', '52998224725'));
    }
}
