<?php

namespace App\Controller;

use App\Service\ApiClientService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface as Serializer;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CatalogueController extends AbstractController
{
    #[Route('/catalogue', name: 'catalogue')]
    public function index(HttpClientInterface $client, Serializer $serializer, ApiClientService $apiClient): Response
    {
        /** @var Produit[] */
        $produits = $apiClient->getProduits();

        /** @var FamilleProduit[] */
        $famillesProduits = $apiClient->getFamillesProduits();

        $cepages = array_map(fn($produit) => $produit->getCepage(), $produits);
        $regions = array_map(fn($produit) => $produit->getRegion(), $produits);
        $appellations = array_map(fn($produit) => $produit->getAppellation(), $produits);
        $fournisseurs = array_map(fn($produit) => $produit->getFournisseurNom(), $produits);

        return $this->render('catalogue/index.html.twig', [
            'produits' => $produits,
            'famillesProduits' => $famillesProduits,
            'cepages' => array_unique($cepages),
            'regions' => array_unique($regions),
            'appellations' => array_unique($appellations),
            'fournisseurs' => array_unique($fournisseurs),
        ]);
    }

    #[Route('/catalogue/{id}', name: 'catalogue_produit')]
    public function produit(HttpClientInterface $client, Serializer $serializer, int $id, ApiClientService $apiClient): Response
    {
        /** @var Produit */
        $produit = $apiClient->getProduit($id);

        return $this->render('catalogue/produit.html.twig', [
            'produit' => $produit,
        ]);
    }
}
