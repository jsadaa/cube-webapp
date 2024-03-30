<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AuthController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        $error = $session->get('auth_error');
        $session->remove('auth_error');

        $lastUsername = $session->get('last_username');
        $session->remove('last_username'); // Effacer le dernier nom d'utilisateur de la session après l'avoir récupéré

        return $this->render('auth/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
}
