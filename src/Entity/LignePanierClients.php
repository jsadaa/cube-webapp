<?php

namespace App\Entity;

class LignePanierClients
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
        return round($this->prixUnitaire, 2);
    }

    public function getTotal(): mixed
    {
        return round($this->total, 2);
    }
}
