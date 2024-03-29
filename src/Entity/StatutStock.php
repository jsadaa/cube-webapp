<?php

namespace App\Entity;

enum StatutStock: string {
    case EnStock = 'EnStock';
    case EnRuptureDeStock = 'EnRuptureDeStock';
    case Indisponible = 'Indisponible';
    case EnCommande = 'EnCommande';
    case EnCoursDeLivraison = 'EnCoursDeLivraison';
    case Livre = 'Livre';
    case Perime = 'Perime';
    case Retourne = 'Retourne';
    case Vendu = 'Vendu';
    case Perdu = 'Perdu';
    case Vole = 'Vole';
    case Casse = 'Casse';
    case Supprime = 'Supprime';
    case Autre = 'Autre';
}
