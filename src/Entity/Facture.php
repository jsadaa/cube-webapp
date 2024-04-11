<?php

namespace App\Entity;

use DateTime;

class Facture
{
    private int $id;
    private DateTime $dateFacture;
    private StatutFacture $statut;
    private mixed $prixHt;
    private mixed $prixTtc;
    private mixed $tva;
    private ?Commande $commande = null;

    public function __construct(
        int $id,
        DateTime $dateFacture,
        StatutFacture $statut,
        mixed $prixHt,
        mixed $prixTtc,
        mixed $tva,
        ?Commande $commande = null
    ) {
        $this->id = $id;
        $this->dateFacture = $dateFacture;
        $this->statut = $statut;
        $this->prixHt = $prixHt;
        $this->prixTtc = $prixTtc;
        $this->tva = $tva;
        $this->commande = $commande;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateFacture(): DateTime
    {
        return $this->dateFacture;
    }

    public function getStatut(): StatutFacture
    {
        return $this->statut;
    }

    public function getPrixHt(): mixed
    {
        return round($this->prixHt, 2);
    }

    public function getPrixTtc(): mixed
    {
        return round($this->prixTtc, 2);
    }

    public function getTva(): mixed
    {
        return round($this->tva * $this->prixHt, 2);
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): void
    {
        $this->commande = $commande;
    }

    public function setStatut(StatutFacture $statut): void
    {
        $this->statut = $statut;
    }
}
