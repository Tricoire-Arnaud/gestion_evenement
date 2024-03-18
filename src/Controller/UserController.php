<?php

namespace App\Controller;

use App\Form\UserType;
use App\Service\AppManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private AppManager $appManager;

    public function __construct(AppManager $appManager)
    {
        $this->appManager = $appManager;
    }

    #[Route('/register', name: 'user_register')]
    public function register(Request $request): Response
    {
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userData = $form->getData();
            $this->appManager->registerUser($userData);

            $this->addFlash('success', 'Inscription réussie! Vous pouvez maintenant vous connecter.');
            return $this->redirectToRoute('user_login');
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/login', name: 'user_login')]
    public function login(): Response
    {
        // Gérer la logique de connexion ici
        return $this->render('user/login.html.twig');
    }

    #[Route('/logout', name: 'user_logout')]
    public function logout(): void
    {
        // Gérer la logique de déconnexion ici
    }
}
