<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Serializer as Serializer;

class PanierController extends AbstractController
{
    public function __construct(private HttpClientInterface $client, private SerializerInterface $serializer)
    {
    }

    #[Route('/panier', name: 'panier')]
    public function index(Request $request): Response
    {
        /** @var User */
        $user = $this->getUser();
        $id = $user->getId();

        $response = $this->client->request('GET', 'http://localhost:5273/api/commandes-clients/panier/client/' . $id);

        $serializer = new Serializer([new ObjectNormalizer(null, null, null, new ReflectionExtractor())]);

        if ($response->getStatusCode() === 404) {
            $panier = null;
        } else {
            $panier = json_decode($response->getContent(), true);
            $panier = $serializer->denormalize($panier, 'App\Entity\Panier');
        }

        return $this->render('panier/index.html.twig', [
            'panier' => $panier,
        ]);
    }

    #[Route('/panier/ajouter/{id}', name: 'ajouter_panier')]
    public function ajouter(int $id, Request $request): Response
    {
        $data = $request->request->all();
        $quantite = $data['quantite'] ?? 1;

        /** @var User */
        $user = $this->getUser();

        // make a get request to http://localhost:5273/api/commandes-clients/panier/client/{id} to get the client's cart
        $response = $this->client->request('GET', 'http://localhost:5273/api/commandes-clients/panier/client/' . $user->getId());

        // if the response is 404, it means the cart does not exist, so we create it
        if ($response->getStatusCode() === 404) {
            $response = $this->client->request('POST', 'http://localhost:5273/api/commandes-clients/panier/' . $user->getId());

            if ($response->getStatusCode() !== 201) {
                throw new \Exception('Une erreur est survenue lors de la création du panier');
            } else {
                $response = $this->client->request('GET', 'http://localhost:5273/api/commandes-clients/panier/client/' . $user->getId());
            }
        }

        $panier = $this->serializer->deserialize($response->getContent(), 'App\Entity\Panier', 'json');

        $response = $this->client->request('GET', 'http://localhost:5273/api/produits/' . $id);

        /** @var Produit */
        $produit = $this->serializer->deserialize($response->getContent(), 'App\Entity\Produit', 'json');

        // make a post request to /api/commandes-clients/panier/{idPanier}/produit with this payload : {"idProduit": $idProduit,"quantite": $quantite}
        $response = $this->client->request('POST', 'http://localhost:5273/api/commandes-clients/panier/' . $panier->getId() . '/produit', [
            'json' => [
                'idProduit' => $produit->getId(),
                'quantite' => $quantite
            ]
        ]);

        if ($response->getStatusCode() !== 201) {
            throw new \Exception('Une erreur est survenue lors de l\'ajout du produit au panier');
        }

        return $this->redirectToRoute('panier');
    }

    #[Route('/panier/vider', name: 'vider_panier')]
    public function vider(Request $request): Response
    {
        /** @var User */
        $user = $this->getUser();

        $response = $this->client->request('GET', 'http://localhost:5273/api/commandes-clients/panier/client/' . $user->getId());

        $panier = $this->serializer->deserialize($response->getContent(), 'App\Entity\Panier', 'json');

        $response = $this->client->request('DELETE', 'http://localhost:5273/api/commandes-clients/panier/' . $panier->getId() . '/vider');

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Une erreur est survenue lors de la suppression des produits du panier');
        }

        return $this->redirectToRoute('panier');
    }

    #[Route('/panier/supprimer', name: 'supprimer_panier')]
    public function supprimer(): Response
    {
        /** @var User */
        $user = $this->getUser();

        $response = $this->client->request('DELETE', 'http://localhost:5273/api/commandes-clients/panier/' . $user->getId());

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Une erreur est survenue lors de la suppression du produit du panier');
        }

        return $this->redirectToRoute('panier');
    }

    #[Route('/panier/modifier/{idPanier}/produit/{idProduit}', name: 'modifier_panier')]
    public function modifier(int $idPanier, int $idProduit, Request $request): Response
    {
        $data = $request->request->all();
        $quantite = $data['quantite'] ?? 1;

        $response = $this->client->request('PUT', 'http://localhost:5273/api/commandes-clients/panier/' . $idPanier . '/produit', [
            'json' => [
                'idProduit' => $idProduit,
                'quantite' => $quantite
            ]
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Une erreur est survenue lors de la modification du produit du panier');
        }

        return $this->redirectToRoute('panier');
    }

    #[Route('/panier/supprimer/{idPanier}/produit/{idProduit}', name: 'supprimer_produit_panier')]
    public function supprimerProduit(int $idPanier, int $idProduit): Response
    {
        $response = $this->client->request('DELETE', 'http://localhost:5273/api/commandes-clients/panier/' . $idPanier . '/produit/' . $idProduit);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Une erreur est survenue lors de la suppression du produit du panier');
        }

        return $this->redirectToRoute('panier');
    }
}
