<?php

namespace App\Controller;

use App\Exception\ClientNonTrouve;
use App\Exception\FormatMotDePasseInvalide;
use App\Exception\UtilisateurExisteDeja;
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
    public function index(HttpClientInterface $client, Serializer $serializer, ApiClientService $apiClient, $error = null): Response
    {
        try {
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
                'error' => $error,
            ]);
        } catch (ClientNonTrouve $e) {
            return $this->render('home/index.html.twig', [
                'error' => "Vous devez être connecté pour accéder à votre compte"
            ]);
        } catch (\Exception $e) {
            return $this->render('home/index.html.twig', [
                'error' => "Une erreur est survenue lors de la récupération de votre compte. Veuillez réessayer."
            ]);
        }
    }

    // modifier l'utilisateur
    #[Route('/user/account/edit', name: 'user_account_edit')]
    public function edit(HttpClientInterface $HttpClient, Serializer $serializer, Request $request, ApiClientService $apiClient): Response
    {
        try {
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
        } catch (ClientNonTrouve $e) {
            return $this->render('home/index.html.twig', [
                'error' => "Vous devez être connecté pour accéder à votre compte"
            ]);
        } catch (FormatMotDePasseInvalide|UtilisateurExisteDeja $e) {
            return $this->render('home/index.html.twig', [
                'error' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            return $this->render('home/index.html.twig', [
                'error' => "Une erreur est survenue lors de la modification de votre compte. Veuillez réessayer."
            ]);
        }
    }
}
