<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ClientNonTrouve extends NotFoundHttpException
{
    public function __construct(string $message = 'Le client n\'existe pas.')
    {
        parent::__construct($message);
    }
}
