<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UtilisateurExisteDeja extends BadRequestHttpException
{
    public function __construct()
    {
        parent::__construct('L\'utilisateur existe déjà');
    }
}
