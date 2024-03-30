<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ProduitDejaDansPanier extends BadRequestHttpException
{
    public function __construct()
    {
        parent::__construct('Le produit est déjà dans le panier');
    }
}
