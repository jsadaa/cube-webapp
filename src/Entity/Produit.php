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
        private string $fournisseurNom
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

    public function getEnPromotion(): bool
    {
        return $this->enPromotion;
    }

    public function getFamilleProduitNom(): string
    {
        return $this->familleProduitNom;
    }

    public function getFournisseurNom(): string
    {
        return $this->fournisseurNom;
    }
}
