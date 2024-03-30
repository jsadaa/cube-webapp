<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CommandeNonTrouvee extends NotFoundHttpException
{
    public function __construct(string $message = 'La commande n\'existe pas.')
    {
        parent::__construct($message);
    }
}
