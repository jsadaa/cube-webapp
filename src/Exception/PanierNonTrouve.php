<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PanierNonTrouve extends NotFoundHttpException
{
    public function __construct(string $message = 'Le panier n\'existe pas.')
    {
        parent::__construct($message);
    }
}
