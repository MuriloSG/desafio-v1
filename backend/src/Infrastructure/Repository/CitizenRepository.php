<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Application\Features\Citizen\Exception\CpfAlreadyExistsException;
use App\Application\Features\Citizen\Repository\CitizenRepositoryInterface;
use App\Domain\Entity\Citizen;
use App\Domain\ValueObject\Cpf;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Repositório Doctrine do cidadão: persistência e pesquisa por nome/CPF.
 */
class CitizenRepository implements CitizenRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function create(Citizen $citizen): void
    {
        try {
            $this->entityManager->persist($citizen);
            $this->entityManager->flush();
        } catch (UniqueConstraintViolationException) {
            throw new CpfAlreadyExistsException($citizen->getCpf());
        }
    }

    public function searchByNameOrCpf(string $term): array
    {
        $term = trim($term);

        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from(Citizen::class, 'c')
            ->orderBy('c.name', 'ASC');

        // Sem termo: lista todos os cidadãos.
        if ($term === '') {
            return $queryBuilder->getQuery()->getResult();
        }

        $queryBuilder->where('LOWER(c.name) LIKE LOWER(:name)')
            ->setParameter('name', '%'.$term.'%');

        $digits = preg_replace('/\D/', '', $term) ?? '';
        if ($digits !== '') {
            $queryBuilder->orWhere('c.cpf LIKE :cpf')
                ->setParameter('cpf', $digits.'%');
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function existsByCpf(Cpf $cpf): bool
    {
        return $this->entityManager->getRepository(Citizen::class)
            ->findOneBy(['cpf' => $cpf->value()]) !== null;
    }
}
