<?php

namespace App\Exception;

class ErreurApi extends \Exception
{
    public function __construct(string $message = 'Erreur API')
    {
        parent::__construct($message);
    }
}
