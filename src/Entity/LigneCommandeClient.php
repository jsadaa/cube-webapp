<?php

namespace App\Entity;

class LigneCommandeClient
{
    private ?int $id = null;
    private Produit $produit;
    private int $quantite;
    private float $prixUnitaire;
    private mixed $total;

    public function __construct(int $id, Produit $produit, int $quantite, float $prixUnitaire, mixed $total)
    {
        $this->id = $id;
        $this->produit = $produit;
        $this->quantite = $quantite;
        $this->prixUnitaire = $prixUnitaire;
        $this->total = $total;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduit(): Produit
    {
        return $this->produit;
    }

    public function getQuantite(): int
    {
        return $this->quantite;
    }

    public function getPrixUnitaire(): float
    {
        return $this->prixUnitaire;
    }

    public function getTotal(): mixed
    {
        return $this->total;
    }
}
