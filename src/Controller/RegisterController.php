<?php

namespace App\Controller;

use App\Entity\Client;
use App\Service\ApiClientService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'register')]
    public function index(): Response
    {
        return $this->render('register/index.html.twig');
    }

    #[Route('/register/submit', name: 'register_submit')]
    public function submit(ApiClientService $apiClient, Request $request): Response
    {
        $data = $request->request->all();

        $client = new Client(
            0,
            "user",
            $data['nom'],
            $data['prenom'],
            $data['adresse'],
            $data['code_postal'],
            $data['ville'],
            $data['pays'],
            $data['telephone'],
            $data['email'],
            new \DateTime($data['date_naissance']),
            new \DateTime(),
            $data['password']
        );

        $apiClient->creerClient($client);

        return $this->redirectToRoute('register_validation');
    }

    #[Route('/register/validation', name: 'register_validation')]
    public function validation(): Response
    {
        return $this->render('register/validation.html.twig');
    }


}
