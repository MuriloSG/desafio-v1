<?php

declare(strict_types=1);

namespace App\Application\Features\Citizen\Repository;

use App\Domain\Entity\Citizen;
use App\Domain\ValueObject\Cpf;

/**
 * Persistência e consultas de cidadão.
 */
interface CitizenRepositoryInterface
{
    public function create(Citizen $citizen): void;

    /**
     * @return Citizen[]
     */
    public function searchByNameOrCpf(string $term): array;

    public function existsByCpf(Cpf $cpf): bool;
}
