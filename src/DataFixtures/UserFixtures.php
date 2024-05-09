<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setName('Test User');
        $user->setEmail('testuser@mail.com');
        $user->setRoles(['ROLE_USER']);
        $user->setVerified(true);
        $hash = $this->passwordHasher->hashPassword($user, 'Us3rP4s5');
        $user->setPassword($hash);
        
        $manager->persist($user);
        $manager->flush();
    }
}
