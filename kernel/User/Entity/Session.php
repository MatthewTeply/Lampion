<?php

namespace Lampion\User\Entity;

class Session {

    # Public:

    /** @var(type="int", length="11") */    
    public $id;

    /** @var(type="varchar", length="255") */
    public $session_id;

    /** @var(type="entity", mappedBy="user_id") */ 
    public $user;

    /** @var(type="int", length="11") */
    public $created;

    /** @var(type="int", length="11") */
    public $updated;

    /** @var(type="varchar", length="255") */
    public $ip;
    
    /** @var(type="varchar", length="255") */
    public $device;

    /** @var(type="varchar", length="255") */
    public $os;
}