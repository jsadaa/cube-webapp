<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Serializer\Serializer as Serializer;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\Produit;
use App\Entity\Stock;
use App\Entity\FamilleProduit;
use App\Entity\Client;
use App\Entity\Panier;
use App\Entity\Commande;
use App\Entity\Facture;

class ApiClientService
{
    private $client;
    private $baseUrl;
    private $serializer;

    public function __construct(HttpClientInterface $client, string $baseUrl, SerializerInterface $serializer)
    {
        $this->client = $client;
        $this->baseUrl = $baseUrl;
        $this->serializer = $serializer;
    }

    public function getClient(int $id) : Client
    {
        $response = $this->client->request('GET', 'http://localhost:5273/api/clients/' . $id);

        if (200 !== $response->getStatusCode()) {
            throw new \Exception('Erreur lors de la récupération du client. ' . $response->getContent() . ' ' . $response->getStatusCode());
        }

        /**
        * @var \App\Entity\Client $client
        */
        $client = $this->serializer->deserialize($response->getContent(), 'App\Entity\Client', 'json');

        return $client;
    }

    public function getProduit(int $id) : Produit
    {
        $response = $this->client->request('GET', $this->baseUrl . '/produits/' . $id);

        if (200 !== $response->getStatusCode()) {
            throw new \Exception('Erreur lors de la récupération du produit. ' . $response->getContent() . ' ' . $response->getStatusCode());
        }

        /** @var Produit */
        $produit = $this->serializer->deserialize($response->getContent(), 'App\Entity\Produit', 'json');

        $stock = $this->getStockParProduit($produit->getId());
        $produit->setStock($stock);

        return $produit;
    }

    /**
     * @return Produit[]
     */
    public function getProduits() : array
    {
        $response = $this->client->request('GET', $this->baseUrl . '/produits');

        if (200 !== $response->getStatusCode()) {
            throw new \Exception('Erreur lors de la récupération des produits. ' . $response->getContent() . ' ' . $response->getStatusCode());
        }

        /** @var Produit[] */
        $produits = $this->serializer->deserialize($response->getContent(), 'App\Entity\Produit[]', 'json');

        $produits = array_map(function ($produit) {
            $stock = $this->getStockParProduit($produit->getId());
            $produit->setStock($stock);

            return $produit;
        }, $produits);

        return $produits;
    }

    public function getStockParProduit(int $id) : Stock
    {
        $response = $this->client->request('GET', $this->baseUrl . '/stocks/produit/' . $id);

        if (200 !== $response->getStatusCode()) {
            throw new \Exception('Erreur lors de la récupération du stock du produit. ' . $response->getContent() . ' ' . $response->getStatusCode());
        }

        /** @var Stock */
        $stock = $this->serializer->deserialize($response->getContent(), 'App\Entity\Stock', 'json');

        return $stock;
    }

    /**
     * @return FamilleProduit[]
     */
    public function getFamillesProduits() : array
    {
        $response = $this->client->request('GET', $this->baseUrl . '/famillesproduits');

        if (200 !== $response->getStatusCode()) {
            throw new \Exception('Erreur lors de la récupération des familles de produits . ' . $response->getContent() . ' ' . $response->getStatusCode());
        }

        /** @var FamilleProduit[] */
        $famillesProduits = $this->serializer->deserialize($response->getContent(), 'App\Entity\FamilleProduit[]', 'json');

        return $famillesProduits;
    }

    public function getPanierClient(int $id) : ?Panier
    {
        $response = $this->client->request('GET',  $this->baseUrl . '/commandes-clients/panier/client/' . $id);

        $serializer = new Serializer([new ObjectNormalizer(null, null, null, new ReflectionExtractor())]);

        if ($response->getStatusCode() === 404) {
            $this->creerPanierClient($id);
            $response = $this->client->request('GET',  $this->baseUrl . '/commandes-clients/panier/client/' . $id);

            if (200 !== $response->getStatusCode()) {
                throw new \Exception('Erreur lors de la récupération du panier. ' . $response->getContent() . ' ' . $response->getStatusCode());
            }
        }

        $panier = json_decode($response->getContent(), true);

        /* @var Panier */
        $panier = $serializer->denormalize($panier, 'App\Entity\Panier');

        return $panier;
    }

    public function creerPanierClient(int $id) : void
    {
        $response = $this->client->request('POST',  $this->baseUrl . '/commandes-clients/panier/' . $id);

        if (201 !== $response->getStatusCode()) {
            throw new \Exception('Erreur lors de la création du panier. ' . $response->getContent() . ' ' . $response->getStatusCode());
        }
    }

    public function ajouterProduitAuPanier(int $idPanier, int $idProduit, int $quantite) : void
    {
        $response = $this->client->request('POST', $this->baseUrl . '/commandes-clients/panier/' . $idPanier. '/produit', [
            'json' => [
                'idProduit' => $idProduit,
                'quantite' => $quantite
            ]
        ]);

        if ($response->getStatusCode() === 409) {
            $this->modifierUnProduitDansLePanier($idPanier, $idProduit, $quantite);
        }

        if ($response->getStatusCode() !== 201 && $response->getStatusCode() !== 409) {
            throw new \Exception('Une erreur est survenue lors de l\'ajout du produit au panier');
        }
    }

    public function modifierUnProduitDansLePanier(int $idPanier, int $idProduit, int $quantite) : void
    {
        $response = $this->client->request('PUT', $this->baseUrl . '/commandes-clients/panier/' . $idPanier . '/produit', [
            'json' => [
                'idProduit' => $idProduit,
                'quantite' => $quantite
            ]
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Une erreur est survenue lors de la modification du produit du panier');
        }
    }

    public function supprimerProduitDuPanier(int $idPanier, int $idProduit) : void
    {
        $response = $this->client->request('DELETE', $this->baseUrl . '/commandes-clients/panier/' . $idPanier . '/produit/' . $idProduit);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Une erreur est survenue lors de la suppression du produit du panier');
        }
    }

    public function ModifierClient(Client $client) : void
    {
        $response = $this->client->request('PUT', $this->baseUrl . '/clients/' . $client->getId(), [
            'json' => $client->toArray()
        ]);

        if (200 !== $response->getStatusCode()) {
            throw new \Exception('Erreur lors de la modification du client . ' . $response->getContent() . ' ' . $response->getStatusCode());
        }
    }

    public function ViderUnPanier(int $idPanier) : void
    {
        $response = $this->client->request('DELETE', $this->baseUrl . '/commandes-clients/panier/' . $idPanier . '/vider');

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Une erreur est survenue lors de la suppression des produits du panier');
        }
    }

    public function supprimerPanierClient(int $idUser) : void
    {
        $response = $this->client->request('DELETE', $this->baseUrl . '/commandes-clients/panier/' . $idUser);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Une erreur est survenue lors de la suppression du produit du panier');
        }
    }

    public function validerPanier(int $idPanier) : void
    {
        $response = $this->client->request('PUT', $this->baseUrl . '/commandes-clients/panier/' . $idPanier . '/valider');

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Une erreur est survenue lors de la validation du panier');
        }
    }

    // /api/commandes-clients/commande/client/{idClient} GET
    public function getCommandesClient(int $id) : array
    {
        $response = $this->client->request('GET', $this->baseUrl . '/commandes-clients/commande/client/' . $id);

        if (200 !== $response->getStatusCode()) {
            throw new \Exception('Erreur lors de la récupération des commandes du client. ' . $response->getContent() . ' ' . $response->getStatusCode());
        }

        /** @var Commande[] */
        $commandes = $this->serializer->deserialize($response->getContent(), 'App\Entity\Commande[]', 'json');

        return $commandes;
    }

    public function getCommande(int $id) : Commande
    {
        $response = $this->client->request('GET', $this->baseUrl . '/commandes-clients/commande/' . $id);

        if (200 !== $response->getStatusCode()) {
            throw new \Exception('Erreur lors de la récupération de la commande. ' . $response->getContent() . ' ' . $response->getStatusCode());
        }

        /** @var Commande */
        $commande = $this->serializer->deserialize($response->getContent(), 'App\Entity\Commande', 'json');

        return $commande;
    }

    public function getFacture(int $id) : Facture
    {
        $response = $this->client->request('GET', $this->baseUrl . '/factures/commandes/' . $id);

        if (200 !== $response->getStatusCode()) {
            throw new \Exception('Erreur lors de la récupération de la facture. ' . $response->getContent() . ' ' . $response->getStatusCode());
        }

        /** @var Facture */
        $facture = $this->serializer->deserialize($response->getContent(), 'App\Entity\Facture', 'json');

        return $facture;
    }
}
