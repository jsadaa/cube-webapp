<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FactureNonTrouvee extends NotFoundHttpException
{
    public function __construct()
    {
        parent::__construct('La facture demandée n\'a pas été trouvée.');
    }
}
