<?php

namespace App\Entity;

enum StatutCommande: string {
    case EnCours = 'EnCours';
    case Livree = 'Livree';
    case Annulee = 'Annulee';
    case Autre = 'Autre';
}
