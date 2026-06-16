<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use InvalidArgumentException;

/**
 * CPF validado: normaliza a máscara e confere os dígitos verificadores
 * no construtor. CPF inválido lança exceção e o objeto não é criado.
 */
readonly class Cpf
{
    private const LENGTH = 11;

    private string $value;

    public function __construct(string $value)
    {
        $digits = preg_replace('/\D/', '', $value) ?? '';

        if (!$this->isValid($digits)) {
            throw new InvalidArgumentException(sprintf('CPF inválido: %s.', $value));
        }

        $this->value = $digits;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function formatted(): string
    {
        return sprintf('%s.%s.%s-%s', 
            substr($this->value, 0, 3), 
            substr($this->value, 3, 3), 
            substr($this->value, 6, 3), 
            substr($this->value, 9, 2),
        );
    }

    private function isValid(string $digits): bool
    {
        if (strlen($digits) !== self::LENGTH) {
            return false;
        }

        if (preg_match('/^(\d)\1{10}$/', $digits)) {
            return false;
        }

        return $this->checkDigit($digits, 9) === (int) $digits[9] && $this->checkDigit($digits, 10) === (int) $digits[10];
    }

    /**
     * Calcula um dígito verificador (módulo 11): soma os dígitos com pesos
     * decrescentes e converte o resto da divisão por 11 em dígito.
     */
    private function checkDigit(string $digits, int $position): int
    {
        $sum = 0;
        for ($i = 0; $i < $position; ++$i) {
            $sum += (int) $digits[$i] * ($position + 1 - $i);
        }
        $remainder = $sum % 11;
        return $remainder < 2 ? 0 : 11 - $remainder;
    }
}
