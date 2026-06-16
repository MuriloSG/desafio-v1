<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Repository;

use App\Application\Features\Citizen\Exception\CpfAlreadyExistsException;
use App\Domain\Entity\Citizen;
use App\Domain\ValueObject\Cpf;
use App\Infrastructure\Repository\CitizenRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class CitizenRepositoryTest extends TestCase
{
    /** O create persiste a entidade e confirma a transação. */
    public function testPersistsAndFlushesCitizen(): void
    {
        $citizen = new Citizen('Maria da Silva', new Cpf('52998224725'));

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())->method('persist')->with($citizen);
        $entityManager->expects($this->once())->method('flush');

        (new CitizenRepository($entityManager))->create($citizen);
    }

    /** A violação de unicidade no flush deve virar CpfAlreadyExistsException (409). */
    public function testTranslatesUniqueViolationIntoConflict(): void
    {
        $entityManager = $this->createStub(EntityManagerInterface::class);
        $entityManager->method('flush')
            ->willThrowException($this->createStub(UniqueConstraintViolationException::class));

        $repository = new CitizenRepository($entityManager);

        $this->expectException(CpfAlreadyExistsException::class);
        $this->expectExceptionMessage('Já existe um cidadão cadastrado com o CPF 529.982.247-25.');

        $repository->create(new Citizen('Maria da Silva', new Cpf('52998224725')));
    }
}
