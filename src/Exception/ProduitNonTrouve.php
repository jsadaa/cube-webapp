<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProduitNonTrouve extends NotFoundHttpException
{
    public function __construct(string $message = 'Le produit n\'existe pas.')
    {
        parent::__construct($message);
    }
}
