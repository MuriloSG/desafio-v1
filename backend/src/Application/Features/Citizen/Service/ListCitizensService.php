<?php

declare(strict_types=1);

namespace App\Application\Features\Citizen\Service;

use App\Application\Features\Citizen\Repository\CitizenRepositoryInterface;
use App\Domain\Entity\Citizen;

/**
 * Lista cidadãos, com filtro opcional por nome ou CPF.
 */
class ListCitizensService
{
    public function __construct(private readonly CitizenRepositoryInterface $repository)
    {
    }

    /**
     * Lista os cidadãos cadastrados; com termo de busca, filtra por nome ou CPF.
     *
     * @return Citizen[]
     */
    public function execute(?string $search = null): array
    {
        return $this->repository->searchByNameOrCpf($search ?? '');
    }
}
