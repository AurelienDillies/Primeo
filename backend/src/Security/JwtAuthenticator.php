<?php
// src/Security/JwtAuthenticator.php
namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class JwtAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private UserProviderInterface $userProvider,
        private JwtService $jwtService
    ) {}

    public function supports(Request $request): ?bool
    {
        return str_starts_with($request->getPathInfo(), '/api');
    }

    // ✅ Signature correcte pour Symfony 7.4
    public function authenticate(Request $request): SelfValidatingPassport
    {
        dump($request->headers->all());
        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            throw new AuthenticationException('Token manquant');
        }

        $token = substr($authHeader, 7);

        try {
            $payload = $this->jwtService->decode($token);
        } catch (\Exception $e) {
            throw new AuthenticationException('Token invalide ou expiré');
        }

        if (!isset($payload['email'])) {
            throw new AuthenticationException('Payload invalide');
        }
        

        return new SelfValidatingPassport(
            new UserBadge($payload['email'])
        );
    }

    public function onAuthenticationSuccess(Request $request, $token, string $firewallName): ?JsonResponse
    {
        return null; // continue
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?JsonResponse
    {
        return new JsonResponse(['error' => $exception->getMessage()], 401);
    }
}