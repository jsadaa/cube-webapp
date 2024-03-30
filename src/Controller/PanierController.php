<?php

namespace App\Controller;

use App\Exception\ClientNonTrouve;
use App\Exception\PanierNonTrouve;
use App\Exception\ProduitNonPresentDansPanier;
use App\Exception\ProduitNonTrouve;
use App\Exception\QuantitePanierInvalide;
use App\Exception\StockNonTrouve;
use App\Service\ApiClientService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;

class PanierController extends AbstractController
{
    public function __construct(private HttpClientInterface $client, private SerializerInterface $serializer)
    {
    }

    #[Route('/panier', name: 'panier')]
    public function index(ApiClientService $apiClient): Response
    {
        try {
            /** @var User */
            $user = $this->getUser();
            $id = $user->getId();

            /** @var Panier|null */
            $panier = $apiClient->getPanierClient($id);

            return $this->render('panier/index.html.twig', [
                'panier' => $panier,
            ]);
        } catch (ClientNonTrouve|PanierNonTrouve $e) {
            return $this->render('panier/index.html.twig', [
                'panier' => null,
            ]);
        } catch (\Exception $e) {
            return $this->render('home/index.html.twig', [
                'error' => "Une erreur est survenue lors de la récupération du panier. Veuillez réessayer."
            ]);
        }
    }

    #[Route('/panier/ajouter/{id}', name: 'ajouter_panier')]
    public function ajouter(int $id, Request $request, ApiClientService $apiClient): Response
    {
        try {
            $data = $request->request->all();
            $quantite = $data['quantite'] ?? 1;

            /** @var User */
            $user = $this->getUser();

            $panier = $apiClient->getPanierClient($user->getId());

            /** @var Produit */
            $produit = $apiClient->getProduit($id);

            $apiClient->ajouterProduitAuPanier($panier->getId(), $produit->getId(), $quantite);

            return $this->redirectToRoute('panier');
        } catch (ClientNonTrouve|ProduitNonTrouve|StockNonTrouve|PanierNonTrouve $e) {
            return $this->render('home/index.html.twig', [
                'error' => "Une erreur est survenue lors de l'ajout du produit au panier. Veuillez réessayer."
            ]);
        } catch (QuantitePanierInvalide $e) {
            return $this->render('home/index.html.twig', [
                'error' => "La quantité du produit est invalide. Veuillez réessayer."
            ]);
        } catch (\Exception $e) {
            return $this->render('home/index.html.twig', [
                'error' => $e->getMessage()
            ]);
        }
    }

    #[Route('/panier/vider', name: 'vider_panier')]
    public function vider(ApiClientService $apiClient): Response
    {
        try {
            /** @var User */
            $user = $this->getUser();

            $panier = $apiClient->getPanierClient($user->getId());

            $apiClient->viderUnPanier($panier->getId());

            return $this->redirectToRoute('panier');
        } catch (ClientNonTrouve|PanierNonTrouve $e) {
            return $this->render('home/index.html.twig', [
                'error' => "Une erreur est survenue lors de la suppression du panier. Veuillez réessayer."
            ]);
        } catch (\Exception $e) {
            return $this->render('home/index.html.twig', [
                'error' => $e->getMessage()
            ]);
        }
    }

    #[Route('/panier/modifier/{idPanier}/produit/{idProduit}', name: 'modifier_panier')]
    public function modifierProduitPanier(int $idPanier, int $idProduit, Request $request, ApiClientService $apiClient): Response
    {
        try {
            $data = $request->request->all();
            $quantite = $data['quantite'] ?? 1;

            $apiClient->modifierUnProduitDansLePanier($idPanier, $idProduit, $quantite);

            return $this->redirectToRoute('panier');
        } catch (ProduitNonTrouve|PanierNonTrouve $e) {
            return $this->render('home/index.html.twig', [
                'error' => "Une erreur est survenue lors de la modification du produit du panier. Veuillez réessayer."
            ]);
        } catch (QuantitePanierInvalide $e) {
            return $this->render('home/index.html.twig', [
                'error' => "La quantité du produit est invalide. Veuillez réessayer."
            ]);
        } catch (\Exception $e) {
            return $this->render('home/index.html.twig', [
                'error' => $e->getMessage()
            ]);
        }
    }

    #[Route('/panier/supprimer/{idPanier}/produit/{idProduit}', name: 'supprimer_produit_panier')]
    public function supprimerProduitPanier(int $idPanier, int $idProduit, ApiClientService $apiClient): Response
    {
        try {
            $apiClient->supprimerProduitDuPanier($idPanier, $idProduit);

            return $this->redirectToRoute('panier');
        } catch (PanierNonTrouve $e) {
            return $this->render('home/index.html.twig', [
                'error' => "Une erreur est survenue lors de la suppression du produit du panier. Veuillez réessayer."
            ]);
        } catch (ProduitNonPresentDansPanier $e) {
            return $this->render('home/index.html.twig', [
                'error' => "Le produit n'est pas présent dans le panier. Veuillez réessayer."
            ]);
        } catch (\Exception $e) {
            return $this->render('home/index.html.twig', [
                'error' => $e->getMessage()
            ]);
        }
    }

    #[Route('/panier/valider', name: 'valider_panier')]
    public function validerPanier(ApiClientService $apiClient): Response
    {
        try {
            /** @var User */
            $user = $this->getUser();
            $id = $user->getId();

            /** @var Client */
            $client = $apiClient->getClient($id);

            /** @var Panier|null */
            $panier = $apiClient->getPanierClient($id);

            return $this->render('panier/valider.html.twig', [
                'panier' => $panier,
                'client' => $client,
            ]);
        } catch (ClientNonTrouve|PanierNonTrouve $e) {
            return $this->render('home/index.html.twig', [
                'error' => "Une erreur est survenue lors de la validation du panier. Veuillez réessayer."
            ]);
        } catch (\Exception $e) {
            return $this->render('home/index.html.twig', [
                'error' => $e->getMessage()
            ]);
        }
    }

    #[Route('/panier/commander', name: 'commander_panier')]
    public function commanderPanier(ApiClientService $apiClient, Request $request): Response
    {
        try {
            $data = $request->request->all();

            /**
            * @var \App\Security\User $user
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

            /** @var Panier|null */
            $panier = $apiClient->getPanierClient($user->getId());

            $apiClient->validerPanier($panier->getId());

            return $this->redirectToRoute('panier');
        } catch (ClientNonTrouve|PanierNonTrouve $e) {
            return $this->render('home/index.html.twig', [
                'error' => "Une erreur est survenue lors de la validation du panier. Veuillez réessayer."
            ]);
        } catch (\Exception $e) {
            return $this->render('home/index.html.twig', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
