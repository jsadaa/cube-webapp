<?php

namespace App\Entity;

use DateTime;

class Promotion
{
    public function __construct(
        private int $id,
        private string $nom,
        private string $description,
        private float $pourcentage,
        private readonly DateTime $dateDebut,
        private readonly DateTime $dateFin,
    )
    {
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

    public function getPourcentage(): float
    {
        return $this->pourcentage;
    }

    public function getDateDebut(): DateTime
    {
        return $this->dateDebut;
    }

    public function getDateFin(): DateTime
    {
        return $this->dateFin;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setPourcentage(float $pourcentage): void
    {
        $this->pourcentage = $pourcentage;
    }
}