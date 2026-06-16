<?php

declare(strict_types=1);

namespace App\Application\Features\Citizen\Dto;

/**
 * Dados de entrada do cadastro de cidadão.
 */
readonly class CreateCitizenDto
{
    public function __construct(
        public string $name,
        public string $cpf,
    ) {
    }
}
