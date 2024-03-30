<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class QuantitePanierInvalide extends BadRequestHttpException
{
    public function __construct()
    {
        parent::__construct('La quantité du panier est invalide');
    }
}
