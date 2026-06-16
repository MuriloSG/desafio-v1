<?php

declare(strict_types=1);

namespace App\Application\Features\Citizen\Exception;

use App\Application\Exception\AppException;
use App\Domain\ValueObject\Cpf;

/** CPF já cadastrado: centraliza a mensagem e o status 409. */
class CpfAlreadyExistsException extends AppException
{
    public function __construct(Cpf $cpf)
    {
        parent::__construct(sprintf('Já existe um cidadão cadastrado com o CPF %s.', $cpf->formatted()), 409);
    }
}
