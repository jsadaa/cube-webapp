<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FormatMotDePasseInvalide extends BadRequestHttpException
{
    public function __construct()
    {
        parent::__construct('Le format du mot de passe est invalide');
    }
}
