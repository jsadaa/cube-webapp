<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class QuantiteStockInsuffisante extends BadRequestHttpException
{
    public function __construct()
    {
        parent::__construct('La quantité en stock est insuffisante');
    }
}
