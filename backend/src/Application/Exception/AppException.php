<?php

declare(strict_types=1);

namespace App\Application\Exception;

use RuntimeException;

/**
 * Exceção da aplicação: carrega a mensagem e o status HTTP da resposta.
 */
class AppException extends RuntimeException
{
    public function __construct(string $message, private readonly int $statusCode = 400)
    {
        parent::__construct($message);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
