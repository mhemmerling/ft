<?php

namespace App\User\Application;

use App\User\Command\CreateUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    public function __construct(
        private readonly UserServiceInterface $userService
    ) {
    }

    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('user/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    public function register(Request $request): Response
    {
        if ('POST' === $request->getMethod()) {
            $this->userService->createUser(
                new CreateUser(
                    $request->request->get('_username'),
                    $request->request->get('_password'),
                )
            );

            return $this->redirectToRoute('user_login');
        }

        return $this->render('user/register.html.twig');
    }
}
