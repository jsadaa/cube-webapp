<?php

namespace App\Entity;

class FamilleProduit
{
    public function __construct(
        private int $id,
        private string $nom,
        private string $description
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
