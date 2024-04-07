<?php

namespace App\Entity;

class Panier
{
    private ?int $id = null;
    private mixed $total;
    private array $lignePanierClients = [];

    /**
     * @var list<Produit>|null
     */
    private ?array $produits = null;

    public function __construct(int $id, mixed $total, array $lignePanierClients)
    {
        $this->id = $id;
        $this->total = $total;
        $this->lignePanierClients = $lignePanierClients;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getTotal(): mixed
    {
        return $this->total;
    }

    public function setTotal(mixed $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getLignePanierClients(): array
    {
        return $this->lignePanierClients;
    }

    public function setLignePanierClients(array $lignePanierClients): self
    {
        $this->lignePanierClients = $lignePanierClients;

        return $this;
    }

    /**
     * @return list<Produit>|null
     */
    public function getProduits(): ?array
    {
        return $this->produits;
    }


    public function addProduit(Produit $produit): self
    {
        $this->produits[] = $produit;

        return $this;
    }

    public function getProduitById(int $id): ?Produit
    {
        foreach ($this->produits as $produit) {
            if ($produit->getId() === $id) {
                return $produit;
            }
        }
        return null;
    }

    public function getNombreProduits(): int
    {
        return count($this->lignePanierClients);
    }

    public function getQuantitéTotale(): int
    {
        $quantitéTotale = 0;
        foreach ($this->lignePanierClients as $lignePanierClient) {
            $quantitéTotale += $lignePanierClient['quantite'];
        }
        return $quantitéTotale;
    }
}
