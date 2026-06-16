<?php

declare(strict_types=1);

namespace App\Tests\Application\Features\Citizen\Service;

use App\Application\Features\Citizen\Repository\CitizenRepositoryInterface;
use App\Application\Features\Citizen\Service\ListCitizensService;
use App\Domain\Entity\Citizen;
use App\Domain\ValueObject\Cpf;
use PHPUnit\Framework\TestCase;

class ListCitizensServiceTest extends TestCase
{
    public function testReturnsCitizensFromRepository(): void
    {
        $citizens = [new Citizen('Maria da Silva', new Cpf('52998224725'))];

        $repository = $this->createStub(CitizenRepositoryInterface::class);
        $repository->method('searchByNameOrCpf')->willReturn($citizens);

        $result = (new ListCitizensService($repository))->execute('maria');

        self::assertSame($citizens, $result);
    }

    public function testSearchesWithEmptyTermWhenNoneIsGiven(): void
    {
        $repository = $this->createMock(CitizenRepositoryInterface::class);
        $repository->expects(self::once())
            ->method('searchByNameOrCpf')
            ->with('')
            ->willReturn([]);

        $result = (new ListCitizensService($repository))->execute();

        self::assertSame([], $result);
    }

    public function testDelegatesSearchTermToRepository(): void
    {
        $repository = $this->createMock(CitizenRepositoryInterface::class);
        $repository->expects(self::once())
            ->method('searchByNameOrCpf')
            ->with('  maria  ')
            ->willReturn([]);

        (new ListCitizensService($repository))->execute('  maria  ');
    }
}
