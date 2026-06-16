<?php

declare(strict_types=1);

namespace App\Application\Features\Citizen\Dto;

use App\Domain\Entity\Citizen;
use DateTimeInterface;

/**
 * Formato de saída do cidadão na API (CPF formatado).
 */
readonly class CitizenResponseDto
{
    public function __construct(
        public string $id,
        public string $name,
        public string $cpf,
        public string $createdAt,
    ) {
    }

    public static function fromEntity(Citizen $citizen): self
    {
        return new self(
            id: (string) $citizen->getId(),
            name: $citizen->getName(),
            cpf: $citizen->getCpf()->formatted(),
            createdAt: $citizen->getCreatedAt()->format(DateTimeInterface::ATOM),
        );
    }

    /**
     * @param Citizen[] $citizens
     * @return self[]
     */
    public static function fromList(array $citizens): array
    {
        $dtos = [];
        foreach ($citizens as $citizen) {
            $dtos[] = self::fromEntity($citizen);
        }

        return $dtos;
    }
}
