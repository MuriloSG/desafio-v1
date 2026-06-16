<?php

declare(strict_types=1);

namespace App\Application\Features\Citizen\Service;

use App\Application\Features\Citizen\Dto\CreateCitizenDto;
use App\Application\Features\Citizen\Exception\CpfAlreadyExistsException;
use App\Application\Features\Citizen\Repository\CitizenRepositoryInterface;
use App\Domain\Entity\Citizen;
use App\Domain\ValueObject\Cpf;

/**
 * Cadastra um cidadão: valida o nome, o CPF e rejeita duplicados.
 */
class CreateCitizenService
{
    public function __construct(private readonly CitizenRepositoryInterface $repository)
    {
    }

    public function execute(CreateCitizenDto $input): Citizen
    {
        $cpf = new Cpf($input->cpf);

        if ($this->repository->existsByCpf($cpf)) {
            throw new CpfAlreadyExistsException($cpf);
        }

        $citizen = new Citizen($input->name, $cpf);
        $this->repository->create($citizen);

        return $citizen;
    }
}
