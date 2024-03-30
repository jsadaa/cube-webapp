<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StockNonTrouve extends NotFoundHttpException
{
    public function __construct(string $message = 'Le stock n\'existe pas.')
    {
        parent::__construct($message);
    }
}
