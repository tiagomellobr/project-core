<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserController extends AbstractController
{
    #[Route('/app/user/profile', name: 'app_user_profile')]
    public function index(
        Request $request,
        TranslatorInterface $translator,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $formUser = $this->createForm(UserType::class, $user);
        $formUser->handleRequest($request);

        if ($formUser->isSubmitted() && $formUser->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'success', $translator->trans('Your account has been updated successfully!')
            );
            return $this->redirectToRoute('app_user_profile');
        }

        $formPassword = $this->createForm(ChangePasswordFormType::class);
        $formPassword->handleRequest($request);

        if ($formPassword->isSubmitted() && $formPassword->isValid()) {
            // Encode(hash) the plain password, and set it.
            $encodedPassword = $passwordHasher->hashPassword(
                $user,
                $formPassword->get('plainPassword')->getData()
            );

            $user->setPassword($encodedPassword);
            $entityManager->persist($user);
            $entityManager->flush();

            // The session is cleaned up after the password has been changed.
            
            $this->addFlash('success', $translator->trans('Your password has been updated successfully'));

            return $this->redirectToRoute('app_user_profile');
        }
        
        return $this->render(
            'user/index.html.twig',
            [
                'formUser' => $formUser->createView(),
                'formPassword' => $formPassword->createView(),
            ]
        );
    }
}
