<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class AbstractApiController extends AbstractController
{
    protected function getDiscordUserId(): string
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        return $request->attributes->get('discord_user_id');
    }

    protected function jsonResponse(mixed $data, int $status = Response::HTTP_OK, array $groups = []): JsonResponse
    {
        return $this->json($data, $status, context: ['groups' => $groups]);
    }

    protected function jsonCreated(mixed $data, array $groups = []): JsonResponse
    {
        return $this->jsonResponse($data, Response::HTTP_CREATED, $groups);
    }

    protected function jsonNoContent(): JsonResponse
    {
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    protected function assertRelation(bool $condition, string $message): void
    {
        if (!$condition) {
            throw new NotFoundHttpException(
                sprintf('["%s"] %s', static::class, $message)
            );
        }
    }
}
