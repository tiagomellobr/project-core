<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OAuthController extends AbstractController
{
    private const SCOPES = [
        'google' => ['email']
    ];

    public function __construct(
        private ClientRegistry $clientRegistry
    )
    {}

    #[Route('/oauth/service/{service}', name: 'oauth_service', methods: ['GET'])]
    public function login(string $service): RedirectResponse
    {
        if (!in_array($service, array_keys(self::SCOPES), true)) {
            throw $this->createNotFoundException();
        }

        return $this->clientRegistry
            ->getClient($service)
            ->redirect(self::SCOPES[$service]);
    }

    #[Route('/oauth/check/{service}', name: 'oauth_check', methods:['GET', 'POST'])]
    public function check(): Response
    {
        return new Response(200);
    }
}
