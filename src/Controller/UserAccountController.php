<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface as Serializer;
use App\Service\ApiClientService;

class UserAccountController extends AbstractController
{
    #[Route('/user/account', name: 'user_account')]
    public function index(HttpClientInterface $client, Serializer $serializer, ApiClientService $apiClient): Response
    {
        /**
         * @var \App\Security\User $user
         */
        $user = $this->getUser();

        /**
         * @var \App\Entity\Client $client
         */
        $client = $apiClient->getClient($user->getId());

        return $this->render('user_account/index.html.twig', [
            'client' => $client,
        ]);
    }

    // modifier l'utilisateur
    #[Route('/user/account/edit', name: 'user_account_edit')]
    public function edit(HttpClientInterface $HttpClient, Serializer $serializer, Request $request, ApiClientService $apiClient): Response
    {
        $data = $request->request->all();

        /**
         * @var \App\Entity\User $user
         */
        $user = $this->getUser();

        /**
         * @var \App\Entity\Client $client
         */
        $client = $apiClient->getClient($user->getId());

        $client->setNom($data['nom'] ?? $client->getNom());
        $client->setPrenom($data['prenom'] ?? $client->getPrenom());
        $client->setAdresse($data['adresse'] ?? $client->getAdresse());
        $client->setCodePostal($data['codePostal'] ?? $client->getCodePostal());
        $client->setVille($data['ville'] ?? $client->getVille());
        $client->setPays($data['pays'] ?? $client->getPays());
        $client->setTelephone($data['telephone'] ?? $client->getTelephone());

        $apiClient->ModifierClient($client);

        return $this->redirectToRoute('user_account');
    }
}
