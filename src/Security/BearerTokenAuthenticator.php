<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

final class BearerTokenAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {}

    public function supports(Request $request): ?bool
    {
        $authorization = $request->headers->get('Authorization');

        return is_string($authorization)
            && str_starts_with($authorization, 'Bearer ');
    }

    public function authenticate(Request $request): SelfValidatingPassport
    {
        // Vérification du Bearer Token (authentification de l'agent)
        $authorization = $request->headers->get('Authorization', '');

        $token = trim(substr($authorization, 7));

        if ($token === '') {
            throw new CustomUserMessageAuthenticationException('Bearer token manquant');
        }

        $expected = $_ENV['DISCORD_TOKEN'] ?? null;

        if ($expected === null || !hash_equals($expected, $token)) {
            throw new CustomUserMessageAuthenticationException('Bearer token invalide');
        }

        // Vérification du Discord User ID (identification de l'utilisateur)
        $discordUserId = $request->headers->get('X-Discord-User-Id');

        if (!$discordUserId || trim($discordUserId) === '') {
            throw new CustomUserMessageAuthenticationException('Discord User ID manquant');
        }

        if (!ctype_digit($discordUserId)) {
            throw new CustomUserMessageAuthenticationException('Discord User ID invalide');
        }

        $badge = new UserBadge($discordUserId, $this->loadUser(...));

        return new SelfValidatingPassport($badge);
    }

    private function loadUser(string $discordId): User
    {
        $user = $this->userRepository->findOneByDiscordId($discordId);

        if (!$user) {
            throw new CustomUserMessageAuthenticationException(
                sprintf('Utilisateur avec Discord ID "%s" introuvable', $discordId)
            );
        }

        return $user;
    }

    public function onAuthenticationSuccess(Request $request, $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(
            [
                'error' => 'unauthorized',
                'message' => $exception->getMessage(),
            ],
            Response::HTTP_UNAUTHORIZED
        );
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new JsonResponse(
            ['error' => 'missing_bearer_token'],
            Response::HTTP_UNAUTHORIZED
        );
    }
}
