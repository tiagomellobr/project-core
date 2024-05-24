<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class OAuthRegistrationService
{
    public function __construct(
        private readonly UserRepository $repository,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    /**
     * @param GoogleUser $resourceOwner
     */
    public function persist(ResourceOwnerInterface $resourceOwner): User
    {
        $user = (new User())
            ->setEmail($resourceOwner->getEmail())
            ->setName($resourceOwner->getName())
            ->setGoogleId($resourceOwner->getId())
            ->setRoles(['ROLE_USER']);

        $hash = $this->passwordHasher->hashPassword(
            $user,
            bin2hex(openssl_random_pseudo_bytes(12))
        );

        $user->setPassword($hash);

        $this->repository->add($user, true);
        
        return $user;
    }
}