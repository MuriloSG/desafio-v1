<?php

declare(strict_types=1);

namespace App\Presentation\EventListener;

use App\Application\Exception\AppException;
use InvalidArgumentException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

/**
 * Converte exceções das rotas /api em respostas JSON com o status adequado.
 */
#[AsEventListener]
class ApiExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        if (!str_starts_with($event->getRequest()->getPathInfo(), '/api')) {
            return;
        }

        $exception = $event->getThrowable();
        [$statusCode, $message] = $this->mapException($exception);

        $response = new JsonResponse([
            'error' => [
                'message' => $message,
                'status' => $statusCode,
            ],
        ], $statusCode);
        $response->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $event->setResponse($response);
    }

    private function mapException(Throwable $exception): array
    {
        return match (true) {
            $exception instanceof AppException => [$exception->getStatusCode(), $exception->getMessage()],
            $exception instanceof InvalidArgumentException => [Response::HTTP_BAD_REQUEST, $exception->getMessage()],
            $exception instanceof HttpExceptionInterface => [$exception->getStatusCode(), $exception->getMessage() ?: 'Erro'],
            default => [Response::HTTP_INTERNAL_SERVER_ERROR, 'Erro interno do servidor.'],
        };
    }
}
