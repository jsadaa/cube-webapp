<?php

namespace App\Entity;

enum StatutFacture: string {
    case EnCours = 'EnCours';
    case Payee = 'Payee';
    case Annulee = 'Annulee';
    case Autre = 'Autre';
}
