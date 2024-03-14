<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface as Serializer;

class UserAccountController extends AbstractController
{
    #[Route('/user/account', name: 'user_account')]
    public function index(HttpClientInterface $client, Serializer $serializer): Response
    {
        /**
         * @var \App\Entity\User $user
         */
        $user = $this->getUser();

        $response = $client->request('GET', 'http://localhost:5273/api/clients/' . $user->getId());

        /**
         * @var \App\Entity\Client $client
         */
        $client = $serializer->deserialize($response->getContent(), 'App\Entity\Client', 'json');

        return $this->render('user_account/index.html.twig', [
            'client' => $client,
        ]);
    }

    // modifier l'utilisateur
    #[Route('/user/account/edit', name: 'user_account_edit')]
    public function edit(HttpClientInterface $HttpClient, Serializer $serializer, Request $request): Response
    {
        $data = $request->request->all();
        
        /**
         * @var \App\Entity\User $user
         */
        $user = $this->getUser();

        $response = $HttpClient->request('GET', 'http://localhost:5273/api/clients/' . $user->getId());

        /**
         * @var \App\Entity\Client $client
         */
        $client = $serializer->deserialize($response->getContent(), 'App\Entity\Client', 'json');

        $client->setNom($data['nom'] ?? $client->getNom());
        $client->setPrenom($data['prenom'] ?? $client->getPrenom());
        $client->setAdresse($data['adresse'] ?? $client->getAdresse());
        $client->setCodePostal($data['codePostal'] ?? $client->getCodePostal());
        $client->setVille($data['ville'] ?? $client->getVille());
        $client->setPays($data['pays'] ?? $client->getPays());
        $client->setTelephone($data['telephone'] ?? $client->getTelephone());

        $response = $HttpClient->request('PUT', 'http://localhost:5273/api/clients/' . $user->getId(), [
            'json' => $client->toArray(),
        ]);

        if (200 !== $response->getStatusCode()) {
            throw new \Exception('Erreur lors de la modification du client . ' . $response->getContent() . ' ' . $response->getStatusCode());
        }

        return $this->render('user_account/index.html.twig', [
            'client' => $client,
        ]);
    }
}
