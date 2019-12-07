<?php

namespace Lampion\User;

use Lampion\User\Entity\User;

class Auth
{
    public $loggedIn;

    public function __construct()
    {
        $this->loggedIn = false;
    }
}