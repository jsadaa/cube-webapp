<?php

namespace App\Entity;

use DateTime;

class Stock {

    private int $id;
    private int $quantite;
    private int $seuilDisponibilite;
    private StatutStock $statut;
    private DateTime $dateCreation;
    private DateTime $dateModification;
    private ?DateTime $dateSuppression = null;

    public function __construct(
        int $id,
        int $quantite,
        int $seuilDisponibilite,
        StatutStock $statut,
        DateTime $dateCreation,
        DateTime $dateModification,
        ?DateTime $dateSuppression = null
    ) {
        $this->id = $id;
        $this->quantite = $quantite;
        $this->seuilDisponibilite = $seuilDisponibilite;
        $this->statut = $statut;
        $this->dateCreation = $dateCreation;
        $this->dateModification = $dateModification;
        $this->dateSuppression = $dateSuppression;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getQuantite(): int
    {
        return $this->quantite;
    }

    public function getSeuilDisponibilite(): int
    {
        return $this->seuilDisponibilite;
    }

    public function getStatut(): string
    {
        return preg_replace('/(?<!^)[A-Z]/', ' $0', $this->statut->value);
    }

    public function getDateCreation(): DateTime
    {
        return $this->dateCreation;
    }

    public function getDateModification(): DateTime
    {
        return $this->dateModification;
    }

    public function getDateSuppression(): ?DateTime
    {
        return $this->dateSuppression;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setQuantite(int $quantite): void
    {
        $this->quantite = $quantite;
    }

    public function setSeuilDisponibilite(int $seuilDisponibilite): void
    {
        $this->seuilDisponibilite = $seuilDisponibilite;
    }

    public function setStatut(StatutStock $statutStock): void
    {
        $this->statut = $statutStock;
    }

    public function setDateCreation(DateTime $dateCreation): void
    {
        $this->dateCreation = $dateCreation;
    }

    public function setDateModification(DateTime $dateModification): void
    {
        $this->dateModification = $dateModification;
    }

    public function setDateSuppression(?DateTime $dateSuppression): void
    {
        $this->dateSuppression = $dateSuppression;
    }
    
}
