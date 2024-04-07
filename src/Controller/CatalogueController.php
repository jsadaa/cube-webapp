<?php

namespace App\Controller;

use App\Exception\StockNonTrouve;
use App\Exception\ProduitNonTrouve;
use App\Service\ApiClientService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface as Serializer;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CatalogueController extends AbstractController
{
    #[Route('/catalogue', name: 'catalogue')]
    public function index(ApiClientService $apiClient): Response
    {
        try {
            /** @var Produit[] $produits*/
            $produits = $apiClient->getProduits();

            /** @var FamilleProduit[] $famillesProduits*/
            $famillesProduits = $apiClient->getFamillesProduits();

            $cepages = array_unique(array_map(fn($produit) => $produit->getCepage(), $produits));
            $regions = array_unique(array_map(fn($produit) => $produit->getRegion(), $produits));
            $appellations = array_unique(array_map(fn($produit) => $produit->getAppellation(), $produits));
            $fournisseurs = array_unique(array_map(fn($produit) => $produit->getFournisseurNom(), $produits));

            return $this->render('catalogue/index.html.twig', [
                'produits' => $produits,
                'famillesProduits' => $famillesProduits,
                'cepages' => $cepages,
                'regions' => $regions,
                'appellations' => $appellations,
                'fournisseurs' => $fournisseurs,
            ]);
        } catch (ProduitNonTrouve|StockNonTrouve $e) {
            return $this->render('home/index.html.twig', [
                'error' => "Une erreur est survenue lors de la récupération des produits. Veuillez réessayer plus tard."
            ]);
        } catch (\Exception $e) {
            return $this->render('home/index.html.twig', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    #[Route('/catalogue/{id}', name: 'catalogue_produit')]
    public function produit(HttpClientInterface $client, Serializer $serializer, int $id, ApiClientService $apiClient): Response
    {
        try {
            /** @var Produit $produit*/
            $produit = $apiClient->getProduit($id);

            return $this->render('catalogue/produit.html.twig', [
                'produit' => $produit,
            ]);
        } catch (ProduitNonTrouve|StockNonTrouve $e) {
            return $this->render('home/index.html.twig', [
                'error' => "Une erreur est survenue lors de la récupération du produit. Veuillez réessayer plus tard."
            ]);
        } catch (\Exception $e) {
            return $this->render('home/index.html.twig', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
