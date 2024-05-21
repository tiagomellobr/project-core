<?php

namespace App\EventSubscriber;

use App\Form\LoginType;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3Validator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\SecurityRequestAttributes;

class LoginSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private Recaptcha3Validator $recaptcha3Validator
    ){}
    
    public function onKernelRequest(RequestEvent $event): void
    {
        if ('app_login' !== $event->getRequest()->attributes->get('_route')) {
            return;
        }

        $loginForm = $this->formFactory->createNamed('login', LoginType::class);
        if (!$loginForm->has('captcha')) {
            return;
        }

        $loginForm->handleRequest($event->getRequest());

        if (!$loginForm->isSubmitted()) {
            return;
        }

        $validator = $this->recaptcha3Validator->getLastResponse();
        if (!$loginForm->get('captcha')->isValid() || !$validator->isSuccess()) {
            $errors = $loginForm->get('captcha')->getErrors();
            $message = count($errors) ? $errors[0]->getMessage() : 'Failed to pass robot test';
            
            /** @var Session $session */
            $session = $event->getRequest()->getSession();
            $session->getFlashBag()->add('error', $message);

            $session->set(SecurityRequestAttributes::LAST_USERNAME, $loginForm->get('email')->getData());

            $event->setResponse(new RedirectResponse($event->getRequest()->getRequestUri()));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 9],
        ];
    }
}
