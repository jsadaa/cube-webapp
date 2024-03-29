<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface as Serializer;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CatalogueController extends AbstractController
{
    #[Route('/catalogue', name: 'catalogue')]
    public function index(HttpClientInterface $client, Serializer $serializer): Response
    {
        $response = $client->request('GET', 'http://localhost:5273/api/produits');

        /** @var Produit[] */
        $produits = $serializer->deserialize($response->getContent(), 'App\Entity\Produit[]', 'json');

        $produits = array_map(function ($produit) use ($client, $serializer) {
            $response = $client->request('GET', 'http://localhost:5273/api/stocks/produit/' . $produit->getId());

            /** @var Stock */
            $stock = $serializer->deserialize($response->getContent(), 'App\Entity\Stock', 'json');

            $produit->setStock($stock);

            return $produit;
        }, $produits);

        $response = $client->request('GET', 'http://localhost:5273/api/famillesproduits');

        /** @var FamilleProduit[] */
        $famillesProduits = $serializer->deserialize($response->getContent(), 'App\Entity\FamilleProduit[]', 'json');

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
    public function produit(HttpClientInterface $client, Serializer $serializer, int $id): Response
    {
        $response = $client->request('GET', 'http://localhost:5273/api/produits/' . $id);

        /** @var Produit */
        $produit = $serializer->deserialize($response->getContent(), 'App\Entity\Produit', 'json');

        // make a get request to http://localhost:5273/api/stocks/produit/{id} to get the stock of the product
        $response = $client->request('GET', 'http://localhost:5273/api/stocks/produit/' . $id);

        /** @var Stock */
        $stock = $serializer->deserialize($response->getContent(), 'App\Entity\Stock', 'json');

        $produit->setStock($stock);

        return $this->render('catalogue/produit.html.twig', [
            'produit' => $produit,
        ]);
    }
}
