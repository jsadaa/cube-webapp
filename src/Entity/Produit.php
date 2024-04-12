<?php

namespace App\Entity;

use DateTime;

class Produit
{
    public function __construct(
        private int $id,
        private string $nom,
        private string $description,
        private string $appellation,
        private string $cepage,
        private string $region,
        private int $annee,
        private float $degreAlcool,
        private float $prixAchat,
        private float $prixVente,
        private DateTime $datePeremption,
        private bool $enPromotion,
        private ?Promotion $promotion,
        private string $familleProduitNom,
        private string $fournisseurNom,
        private ?Stock $stock = null
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAppellation(): string
    {
        return $this->appellation;
    }

    public function getCepage(): string
    {
        return $this->cepage;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function getAnnee(): int
    {
        return $this->annee;
    }

    public function getDegreAlcool(): float
    {
        return $this->degreAlcool;
    }

    public function getPrixAchat(): float
    {
        return round($this->prixAchat, 2);
    }

    public function getPrixVente(): float
    {
        return round($this->prixVente, 2);
    }

    public function getDatePeremption(): DateTime
    {
        return $this->datePeremption;
    }

    public function estEnPromotion(): bool
    {
        return $this->enPromotion;
    }

    public function getPromotion(): ?Promotion
    {
        return $this->promotion;
    }

    public function getFamilleProduitNom(): string
    {
        return $this->familleProduitNom;
    }

    public function getNomImage(): string
    {
        return match ($this->familleProduitNom) {
            'Vin rouge' => 'vin_rouge.webp',
            'Vin blanc' => 'vin_blanc.webp',
            'Vin rosé', 'Vin de glace', 'Vin de liqueur' => 'vin_rose.webp',
            'Vin pétillant' => 'champagne.webp',
            'Vin doux', 'Vin de dessert', 'Vin de fruit', 'Vin de fleur' => 'vin_rose_2.webp',
            default => 'autres.png',
        };
    }

    public function getFournisseurNom(): string
    {
        return $this->fournisseurNom;
    }

    public function getStock(): ?Stock
    {
        return $this->stock;
    }

    public function setStock(Stock $stock): void
    {
        $this->stock = $stock;
    }

    public function getPrixSansPromotion(): float
    {
        return round($this->enPromotion ? $this->prixVente / (1 - $this->promotion->getPourcentage() / 100) : $this->prixVente, 2);
    }
}
