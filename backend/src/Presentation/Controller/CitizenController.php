<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\Exception\AppException;
use App\Application\Features\Citizen\Dto\CitizenResponseDto;
use App\Application\Features\Citizen\Dto\CreateCitizenDto;
use App\Application\Features\Citizen\Service\CreateCitizenService;
use App\Application\Features\Citizen\Service\ListCitizensService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

/**
 * Endpoints REST de cidadãos: traduz requisições em chamadas aos services.
 */
#[Route('/api/v1/citizens')]
#[OA\Tag(name: 'Cidadãos')]
class CitizenController
{
    #[Route('', methods: ['POST'])]
    #[OA\Post(summary: 'Cadastra um cidadão')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['name', 'cpf'],
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Maria da Silva'),
                new OA\Property(property: 'cpf', type: 'string', description: 'Com ou sem máscara', example: '529.982.247-25'),
            ],
        ),
    )]
    #[OA\Response(response: 201, description: 'Cidadão cadastrado', content: new OA\JsonContent(ref: new Model(type: CitizenResponseDto::class)))]
    #[OA\Response(response: 400, description: 'CPF inválido ou nome vazio')]
    #[OA\Response(response: 409, description: 'CPF já cadastrado')]
    public function create(Request $request, CreateCitizenService $service): JsonResponse
    {
        $data = $this->jsonBody($request);

        $citizen = $service->execute(new CreateCitizenDto(
            name: (string) ($data['name'] ?? ''),
            cpf: (string) ($data['cpf'] ?? ''),
        ));

        return new JsonResponse(CitizenResponseDto::fromEntity($citizen), Response::HTTP_CREATED);
    }

    #[Route('', methods: ['GET'])]
    #[OA\Get(summary: 'Lista e pesquisa cidadãos')]
    #[OA\Parameter(name: 'search', in: 'query', required: false, description: 'Filtra por nome (parcial) ou CPF, com ou sem máscara', schema: new OA\Schema(type: 'string'), example: 'maria')]
    #[OA\Response(response: 200, description: 'Lista de cidadãos', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: CitizenResponseDto::class))))]
    public function list(Request $request, ListCitizensService $service): JsonResponse
    {
        $citizens = $service->execute($request->query->get('search'));

        return new JsonResponse(CitizenResponseDto::fromList($citizens));
    }

    private function jsonBody(Request $request): array
    {
        try {
            return $request->toArray();
        } catch (Throwable) {
            throw new AppException('Corpo da requisição inválido: esperado JSON.', 400);
        }
    }
}
