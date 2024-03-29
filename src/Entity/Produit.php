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
        return $this->prixAchat;
    }

    public function getPrixVente(): float
    {
        return $this->prixVente;
    }

    public function getDatePeremption(): DateTime
    {
        return $this->datePeremption;
    }

    public function estEnPromotion(): bool
    {
        return $this->enPromotion;
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
            'Vin rosé' => 'vin_rose.webp',
            'Vin pétillant' => 'champagne.webp',
            'Vin doux' => 'vin_rose_2.webp',
            'Vin de dessert' => 'vin_rose_2.webp',
            'Vin de glace' => 'vin_rose.webp',
            'Vin de liqueur' => 'vin_rose.webp',
            'Vin de fruit' => 'vin_rose_2.webp',
            'Vin de fleur' => 'vin_rose_2.webp',
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
}
