<?php

namespace App\Controller\Api;

use App\Dto\Request\UserCreateInput;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload as Map;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/auth', name: 'api.auth.')]
class AuthController extends AbstractApiController
{
    private const SERIALIZATION_GROUPS = ['common.read', 'user.read'];

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly ObjectMapperInterface $objectMapper
    ) {}

    #[Route('/register', name: 'register', methods: ['POST'])]
    public function create(#[Map] UserCreateInput $input): JsonResponse
    {
        $user = $this->objectMapper->map($input, User::class);
        $this->userRepository->save($user, true);

        return $this->jsonCreated($user, self::SERIALIZATION_GROUPS);
    }
}
