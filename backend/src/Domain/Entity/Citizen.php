<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\Cpf;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Uid\Uuid;

/**
 * Cidadão cadastrado: id (UUID), nome, CPF (único) e data de criação.
 */
#[ORM\Entity]
#[ORM\Table(name: 'citizen')]
class Citizen
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(length: 11, unique: true)]
    private string $cpf;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    private const NAME_MAX_LENGTH = 255;

    public function __construct(string $name, Cpf $cpf)
    {
        $this->id = Uuid::v7();
        $this->name = $this->normalizeName($name);
        $this->cpf = $cpf->value();
        $this->createdAt = new DateTimeImmutable();
    }

    /**
     * Valida e normaliza o nome: obrigatório, sem espaços redundantes e
     * dentro do limite da coluna. Nome inválido lança exceção.
     */
    private function normalizeName(string $name): string
    {
        $name = trim(preg_replace('/\s+/', ' ', $name) ?? '');

        if ($name === '') {
            throw new InvalidArgumentException('O nome do cidadão é obrigatório.');
        }

        if (mb_strlen($name) > self::NAME_MAX_LENGTH) {
            throw new InvalidArgumentException(sprintf('O nome do cidadão deve ter no máximo %d caracteres.', self::NAME_MAX_LENGTH));
        }

        return $name;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCpf(): Cpf
    {
        return new Cpf($this->cpf);
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
