<?php

namespace Lampion\User;

use Lampion\Session\Lampion as LampionSession;
use Lampion\User\Entity\Session as EntitySession;

class Session {

    public static function create(int $user_id) {
        $session = new EntitySession();

        $session->setToken();

        $session->created = time();
        $session->updated = time();
        $session->user_id = $user_id;
        
        $session->persist();

        return $session->getToken();
    }

}