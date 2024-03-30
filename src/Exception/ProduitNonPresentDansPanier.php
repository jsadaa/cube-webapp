<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ProduitNonPresentDansPanier extends BadRequestHttpException
{
    public function __construct()
    {
        parent::__construct('Le produit n\'est pas présent dans le panier');
    }
}
