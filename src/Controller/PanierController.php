<?php

namespace App\Controller;

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
    public function index(Request $request, ApiClientService $apiClient): Response
    {
        /** @var User */
        $user = $this->getUser();
        $id = $user->getId();

        /** @var Panier|null */
        $panier = $apiClient->getPanierClient($id);

        return $this->render('panier/index.html.twig', [
            'panier' => $panier,
        ]);
    }

    #[Route('/panier/ajouter/{id}', name: 'ajouter_panier')]
    public function ajouter(int $id, Request $request, ApiClientService $apiClient): Response
    {
        $data = $request->request->all();
        $quantite = $data['quantite'] ?? 1;

        /** @var User */
        $user = $this->getUser();

        $panier = $apiClient->getPanierClient($user->getId());

        /** @var Produit */
        $produit = $apiClient->getProduit($id);

        $apiClient->ajouterProduitAuPanier($panier->getId(), $produit->getId(), $quantite);

        return $this->redirectToRoute('panier');
    }

    #[Route('/panier/vider', name: 'vider_panier')]
    public function vider(Request $request, ApiClientService $apiClient): Response
    {
        /** @var User */
        $user = $this->getUser();

        $panier = $apiClient->getPanierClient($user->getId());

        $apiClient->viderUnPanier($panier->getId());

        return $this->redirectToRoute('panier');
    }

    #[Route('/panier/supprimer', name: 'supprimer_panier')]
    public function supprimer(ApiClientService $apiClient): Response
    {
        /** @var User */
        $user = $this->getUser();
        $apiClient->supprimerPanierClient($user->getId());

        return $this->redirectToRoute('panier');
    }

    #[Route('/panier/modifier/{idPanier}/produit/{idProduit}', name: 'modifier_panier')]
    public function modifierProduitPanier(int $idPanier, int $idProduit, Request $request, ApiClientService $apiClient): Response
    {
        $data = $request->request->all();
        $quantite = $data['quantite'] ?? 1;

        $apiClient->modifierUnProduitDansLePanier($idPanier, $idProduit, $quantite);

        return $this->redirectToRoute('panier');
    }

    #[Route('/panier/supprimer/{idPanier}/produit/{idProduit}', name: 'supprimer_produit_panier')]
    public function supprimerProduitPanier(int $idPanier, int $idProduit, ApiClientService $apiClient): Response
    {
        $apiClient->supprimerProduitDuPanier($idPanier, $idProduit);

        return $this->redirectToRoute('panier');
    }
}
