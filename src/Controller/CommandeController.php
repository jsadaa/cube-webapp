<?php

namespace App\Controller;

use App\Exception\ClientNonTrouve;
use App\Exception\CommandeNonTrouvee;
use App\Exception\FactureNonTrouvee;
use App\Security\User;
use App\Service\ApiClientService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CommandeController extends AbstractController
{
    #[Route('/commande', name: 'commande')]
    public function index(ApiClientService $apiClientService): Response
    {
        try {
            /**
            * @var User $user
            */
            $user = $this->getUser();

            $commandes = $apiClientService->getCommandesClient($user->getId());

            return $this->render('commande/index.html.twig', [
                'commandes' => $commandes,
            ]);
        } catch (ClientNonTrouve $e) {
            return $this->render('home/index.html.twig', ['error' => "Une erreur est survenue, veuillez réessayer."]);
        } catch (\Exception $e) {
            return $this->render('home/index.html.twig', ['error' => $e->getMessage()]);
        }
    }

    #[Route('/commande/{id}', name: 'commande_detail')]
    public function detail(ApiClientService $apiClientService, int $id): Response
    {
        try {
            /**
            * @var User $user
            */
            $user = $this->getUser();
            $client = $apiClientService->getClient($user->getId());
            $commande = $apiClientService->getCommande($id);
            $facture = $apiClientService->getFactureParCommande($commande->getId());

            return $this->render('commande/commande.html.twig', [
                'commande' => $commande,
                'facture' => $facture,
                'client' => $client,
            ]);
        } catch (ClientNonTrouve $e) {
            return $this->render('home/index.html.twig', ['error' => "Une erreur est survenue, veuillez réessayer."]);
        } catch (\Exception $e) {
            return $this->render('home/index.html.twig', ['error' => $e->getMessage()]);
        }
    }
}
