<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class GoogleAuthenticator extends AbstractOauthAuthenticator
{
    protected string $service = 'google';    

    public function getUserFromResourceOwner(
        ResourceOwnerInterface $googleUser,
        UserRepository $userRepository
    ): ?User
    {
        if (!($googleUser instanceof GoogleUser)) {
            throw new \RuntimeException('GoogleAuthenticator no support this GoogleUser');
        }

        if (true !== ($googleUser->toArray()['email_verified'] ?? null)) {
            throw new AuthenticationException('This email has not been verified');
        }

        /**
         * @var User $user;
         */
        $user = $userRepository->findOneBy([
            'email' => $googleUser->getEmail()
        ]);

        if (empty($user) || (!empty($user->getGoogleId()) && $user->getGoogleId() != $googleUser->getId())) {
            return null;
        }

        if (empty($user->getGoogleId())) {
            $user->setGoogleId($googleUser->getId());
            $userRepository->add($user, true);
        }

        return $user;

        


    }
}