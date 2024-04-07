<?php

namespace App\Service;

use DateTime;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
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
use App\Exception\ClientNonTrouve;
use App\Exception\CommandeNonTrouvee;
use App\Exception\ErreurApi;
use App\Exception\FactureNonTrouvee;
use App\Exception\FormatMotDePasseInvalide;
use App\Exception\ProduitNonTrouve;
use App\Exception\QuantitePanierInvalide;
use App\Exception\StockNonTrouve;
use App\Exception\UtilisateurExisteDeja;
use App\Exception\PanierNonTrouve;
use App\Exception\ProduitNonPresentDansPanier;
use App\Exception\QuantiteStockInsuffisante;
use function PHPUnit\Framework\returnArgument;

class ApiClientService
{
    private HttpClientInterface $client;
    private string $baseUrl;
    private SerializerInterface $serializer;

    public function __construct(HttpClientInterface $client, string $baseUrl, SerializerInterface $serializer)
    {
        $this->client = $client;
        $this->baseUrl = $baseUrl;
        $this->serializer = $serializer;
    }

    /**
     * @param int $idClient
     * @return Client
     * @throws ClientExceptionInterface
     * @throws ErreurApi
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getClient(int $idClient) : Client
    {
        $response = $this->client->request('GET', 'http://localhost:5273/api/clients/' . $idClient);

        match ($response->getStatusCode()) {
            200 => null,
            404 => throw new ClientNonTrouve('Le client n\'existe pas.'),
            default => throw new ErreurApi('Erreur lors de la récupération du client. ' . $response->getContent() . ' ' . $response->getStatusCode())
        };

        /**
        * @var Client $client
        */
        $client = $this->serializer->deserialize($response->getContent(), 'App\Entity\Client', 'json');

        return $client;
    }

    /**
     * @param int $idProduit
     * @return Produit
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ErreurApi
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getProduit(int $idProduit) : Produit
    {
        $response = $this->client->request('GET', $this->baseUrl . '/produits/' . $idProduit);

        match ($response->getStatusCode()) {
            200 => null,
            404 => throw new ProduitNonTrouve('Le produit n\'existe pas.'),
            default => throw new ErreurApi('Erreur lors de la récupération du produit. ' . $response->getContent() . ' ' . $response->getStatusCode())
        };

        /** @var Produit $produit */
        $produit = $this->serializer->deserialize($response->getContent(), 'App\Entity\Produit', 'json');

        $stock = $this->getStockParProduit($produit->getId());
        $produit->setStock($stock);

        return $produit;
    }

    /**
     * @param Client $client
     * @return Client
     * @throws ClientExceptionInterface
     * @throws ErreurApi
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function creerClient(Client $client) : Client
    {
        $response = $this->client->request('POST', $this->baseUrl . '/clients', [
            'json' => $client->toArrayAvecMotDePasse()
        ]);

        /** @var Client $client */
        $client = $this->serializer->deserialize($response->getContent(), 'App\Entity\Client', 'json');

        match ($response->getStatusCode()) {
            201 => null,
            400 => throw new FormatMotDePasseInvalide(),
            409 => throw new UtilisateurExisteDeja(),
            default => throw new ErreurApi('Erreur lors de la création du client. ' . $response->getContent() . ' ' . $response->getStatusCode())
        };

        return $client;
    }

    /**
     * @return Produit[]
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ErreurApi
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getProduits() : array
    {
        $response = $this->client->request('GET', $this->baseUrl . '/produits');

        match ($response->getStatusCode()) {
            200 => null,
            default => throw new ErreurApi('Erreur lors de la récupération des produits. ' . $response->getContent() . ' ' . $response->getStatusCode())
        };

        /** @var Produit[] $produits */
        $produits = $this->serializer->deserialize($response->getContent(), 'App\Entity\Produit[]', 'json');

        return array_map(function ($produit) {
            $stock = $this->getStockParProduit($produit->getId());
            $produit->setStock($stock);

            return $produit;
        }, $produits);
    }

    /**
     * @param int $idProduit
     * @return Stock
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ErreurApi
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getStockParProduit(int $idProduit) : Stock
    {
        $response = $this->client->request('GET', $this->baseUrl . '/stocks/produit/' . $idProduit);

        match ($response->getStatusCode()) {
            200 => null,
            404 => $response->toArray()['code'] === 'stock_introuvable' ? throw new StockNonTrouve() : throw new ProduitNonTrouve(),
            default => throw new ErreurApi('Erreur lors de la récupération du stock. ' . $response->getContent() . ' ' . $response->getStatusCode())
        };

        return $this->serializer->deserialize($response->getContent(), 'App\Entity\Stock', 'json');
    }

    /**
     * @return FamilleProduit[]
     * @throws ClientExceptionInterface
     * @throws ErreurApi
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getFamillesProduits() : array
    {
        $response = $this->client->request('GET', $this->baseUrl . '/famillesproduits');

        match ($response->getStatusCode()) {
            200 => null,
            default => throw new ErreurApi('Erreur lors de la récupération des familles de produits. ' . $response->getContent() . ' ' . $response->getStatusCode())
        };

        /** @var FamilleProduit[] $famillesProduits */
        $famillesProduits = $this->serializer->deserialize($response->getContent(), 'App\Entity\FamilleProduit[]', 'json');

        return $famillesProduits;
    }

    /**
     * @param int $idClient
     * @return Panier
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ErreurApi
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getPanierClient(int $idClient) : Panier
    {
        $response = $this->client->request('GET',  $this->baseUrl . '/commandes-clients/panier/client/' . $idClient);

        $serializer = new Serializer([new ObjectNormalizer(null, null, null, new ReflectionExtractor())]);

        switch ($response->getStatusCode()) {
            case 200:
                break;
            case 404:
                if ($response->toArray()['code'] === 'panier_introuvable') {
                    throw new PanierNonTrouve();
                } else {
                    throw new ClientNonTrouve();
                }
            default:
                throw new ErreurApi('Erreur lors de la récupération du panier. ' . $response->getContent() . ' ' . $response->getStatusCode());
        }

        $panier = json_decode($response->getContent(), true);

        /* @var Panier $panier */
        $panier = $serializer->denormalize($panier, 'App\Entity\Panier');

        return $panier;
    }

    /**
     * @param int $idClient
     * @return void
     * @throws ClientExceptionInterface
     * @throws ErreurApi
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function creerPanierClient(int $idClient) : void
    {
        $response = $this->client->request('POST',  $this->baseUrl . '/commandes-clients/panier/' . $idClient);

        match ($response->getStatusCode()) {
            201 => null,
            404 => throw new ClientNonTrouve(),
            default => throw new ErreurApi('Erreur lors de la création du panier. ' . $response->getContent() . ' ' . $response->getStatusCode())
        };
    }

    /**
     * @param int $idPanier
     * @param int $idProduit
     * @param int $quantite
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ErreurApi
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function ajouterProduitAuPanier(int $idPanier, int $idProduit, int $quantite) : void
    {
        $response = $this->client->request('POST', $this->baseUrl . '/commandes-clients/panier/' . $idPanier. '/produit', [
            'json' => [
                'idProduit' => $idProduit,
                'quantite' => $quantite
            ]
        ]);

        match ($response->getStatusCode()) {
            201 => null,
            400 => throw new QuantitePanierInvalide(),
            404 => $response->toArray()['code'] === 'produit_introuvable'
                ? throw new ProduitNonTrouve()
                : ($response->toArray()['code'] === 'panier_introuvable'
                    ? throw new PanierNonTrouve()
                    : throw new ClientNonTrouve()),
            409 => $this->modifierUnProduitDansLePanier($idPanier, $idProduit, $quantite),
            default => throw new ErreurApi('Erreur lors de l\'ajout du produit au panier. ' . $response->getContent() . ' ' . $response->getStatusCode())
        };
    }

    /**
     * @param int $idPanier
     * @param int $idProduit
     * @param int $quantite
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ErreurApi
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function modifierUnProduitDansLePanier(int $idPanier, int $idProduit, int $quantite) : void
    {
        $response = $this->client->request('PUT', $this->baseUrl . '/commandes-clients/panier/' . $idPanier . '/produit', [
            'json' => [
                'idProduit' => $idProduit,
                'quantite' => $quantite
            ]
        ]);

       match ($response->getStatusCode()) {
            200 => null,
            400 => throw new QuantitePanierInvalide(),
            404 => $response->toArray()['code'] === 'produit_introuvable'
                ? throw new ProduitNonTrouve()
                : throw new PanierNonTrouve(),
            default => throw new ErreurApi('Erreur lors de la modification du produit dans le panier. ' . $response->getContent() . ' ' . $response->getStatusCode())
        };
    }

    /**
     * @param int $idPanier
     * @param int $idProduit
     * @return void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ErreurApi
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function supprimerProduitDuPanier(int $idPanier, int $idProduit) : void
    {
        $response = $this->client->request('DELETE', $this->baseUrl . '/commandes-clients/panier/' . $idPanier . '/produit/' . $idProduit);

        switch ($response->getStatusCode()) {
            case 200:
                break;
            case 404:
                if ($response->toArray()['code'] === 'produit_introuvable') {
                    throw new ProduitNonTrouve();
                } else if ($response->toArray()['code'] === 'produit_non_present_dans_panier') {
                    throw new ProduitNonPresentDansPanier();
                } else {
                    throw new PanierNonTrouve();
                }
            default:
                throw new ErreurApi('Erreur lors de la suppression du produit du panier. ' . $response->getContent() . ' ' . $response->getStatusCode());
        }
    }

    /**
     * @param Client $client
     * @return void
     * @throws ClientExceptionInterface
     * @throws ErreurApi
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function ModifierClient(Client $client) : void
    {
        $response = $this->client->request('PUT', $this->baseUrl . '/clients/' . $client->getId(), [
            'json' => $client->toArray()
        ]);

        match ($response->getStatusCode()) {
            200 => null,
            400 => throw new FormatMotDePasseInvalide(),
            404 => throw new ClientNonTrouve(),
            409 => throw new UtilisateurExisteDeja(),
            default => throw new ErreurApi('Erreur lors de la modification du client. ' . $response->getContent() . ' ' . $response->getStatusCode())
        };
    }

    /**
     * @param int $idPanier
     * @return void
     * @throws ClientExceptionInterface
     * @throws ErreurApi
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function ViderUnPanier(int $idPanier) : void
    {
        $response = $this->client->request('DELETE', $this->baseUrl . '/commandes-clients/panier/' . $idPanier . '/vider');

        match ($response->getStatusCode()) {
            200 => null,
            404 => throw new PanierNonTrouve(),
            default => throw new ErreurApi('Erreur lors de la suppression du produit du panier. ' . $response->getContent() . ' ' . $response->getStatusCode())
        };
    }

    /**
     * @param int $idPanier
     * @param DateTime $dateLivraison
     * @return int
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ErreurApi
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function validerPanier(int $idPanier, DateTime $dateLivraison) : int
    {
        $response = $this->client->request('PUT', $this->baseUrl . '/commandes-clients/panier/' . $idPanier . '/valider', [
            'json' => [
                'dateLivraison' => $dateLivraison->format('Y-m-d')
            ]
        ]);

        switch ($response->getStatusCode()) {
            case 200:
                return $response->toArray()['idCommande'];
                break;
            case 400:
                throw new QuantiteStockInsuffisante();
            case 404:
                if ($response->toArray()['code'] === 'panier_introuvable') {
                    throw new PanierNonTrouve();
                } else if ($response->toArray()['code'] === 'produit_introuvable') {
                    throw new ProduitNonTrouve();
                } else if ($response->toArray()['code'] === 'client_introuvable') {
                    throw new ClientNonTrouve();
                } else if ($response->toArray()['code'] === 'stock_introuvable') {
                    throw new StockNonTrouve();
                } else {
                    throw new ErreurApi('Erreur lors de la validation du panier. ' . $response->getContent() . ' ' . $response->getStatusCode());
                }
            default:
                throw new ErreurApi('Erreur lors de la validation du panier. ' . $response->getContent() . ' ' . $response->getStatusCode());
        }
    }

    /**
     * @param int $idClient
     * @return Commande[]
     * @throws ErreurApi
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getCommandesClient(int $idClient) : array
    {
        $response = $this->client->request('GET', $this->baseUrl . '/commandes-clients/commande/client/' . $idClient);

        match ($response->getStatusCode()) {
            200 => null,
            404 => throw new ClientNonTrouve(),
            default => throw new ErreurApi('Erreur lors de la récupération des commandes du client. ' . $response->getContent() . ' ' . $response->getStatusCode())
        };

        /** @var Commande[] $commandes */
        $commandes = $this->serializer->deserialize($response->getContent(), 'App\Entity\Commande[]', 'json');

        return $commandes;
    }

    /**
     * @param int $idCommande
     * @return Commande
     * @throws ClientExceptionInterface
     * @throws CommandeNonTrouvee
     * @throws ErreurApi
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getCommande(int $idCommande) : Commande
    {
        $response = $this->client->request('GET', $this->baseUrl . '/commandes-clients/commande/' . $idCommande);

        match ($response->getStatusCode()) {
            200 => null,
            404 => throw new CommandeNonTrouvee(),
            default => throw new ErreurApi('Erreur lors de la récupération de la commande. ' . $response->getContent() . ' ' . $response->getStatusCode())
        };

        /** @var Commande $commande */
        $commande = $this->serializer->deserialize($response->getContent(), 'App\Entity\Commande', 'json');

        return $commande;
    }

    /**
     * @param int $idCommande
     * @return Facture
     * @throws ClientExceptionInterface
     * @throws CommandeNonTrouvee
     * @throws FactureNonTrouvee
     * @throws ErreurApi
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws DecodingExceptionInterface
     */
    public function getFactureParCommande(int $idCommande) : Facture
    {
        $response = $this->client->request('GET', $this->baseUrl . '/factures/commandes/' . $idCommande);

        match ($response->getStatusCode()) {
            200 => null,
            404 => $response->toArray()['code'] === 'commande_introuvable' ? throw new CommandeNonTrouvee() : throw new FactureNonTrouvee(),
            default => throw new ErreurApi('Erreur lors de la récupération de la facture. ' . $response->getContent() . ' ' . $response->getStatusCode())
        };

        /** @var Facture $facture */
        $facture = $this->serializer->deserialize($response->getContent(), 'App\Entity\Facture', 'json');

        return $facture;
    }

    /**
     * @param int $idFacture
     * @return Facture
     * @throws ClientExceptionInterface
     * @throws ErreurApi
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getFacture(int $idFacture) : Facture
    {
        $response = $this->client->request('GET', $this->baseUrl . '/factures/' . $idFacture);

        match ($response->getStatusCode()) {
            200 => null,
            404 => throw new FactureNonTrouvee(),
            default => throw new ErreurApi('Erreur lors de la récupération de la facture. ' . $response->getContent() . ' ' . $response->getStatusCode())
        };

        /** @var Facture $facture */
        $facture = $this->serializer->deserialize($response->getContent(), 'App\Entity\Facture', 'json');

        return $facture;
    }

    /**
     * @param int $idFacture
     * @return void
     * @throws ClientExceptionInterface
     * @throws ErreurApi
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function payerFacture(int $idFacture) : void
    {
        $response = $this->client->request('PUT', $this->baseUrl . '/factures/' . $idFacture . '/payer');

        match ($response->getStatusCode()) {
            200 => null,
            404 => throw new FactureNonTrouvee(),
            default => throw new ErreurApi('Erreur lors du paiement de la facture. ' . $response->getContent() . ' ' . $response->getStatusCode())
        };
    }
}
