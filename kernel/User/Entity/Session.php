<?php

namespace Lampion\User\Entity;

use Lampion\Entity\Entity;

class Session extends Entity {

    # Public:
    public $id;
    public $created;
    public $updated;
    public $user_id;
    public $ip;
    public $device;
    public $os;

    # Protected:
    protected $token;

    public function __construct($id = null) {
        $this->init($id);
    }

    public function setToken() {
        $this->token = bin2hex(random_bytes(64));
    }

    public function getToken() {
        return $this->token;
    }

    public function persist() {
        $this->save();
    }

    public function destroy() {
        $this->delete();
    }
}