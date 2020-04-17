<?php

namespace Lampion\User\Entity;

class User
{
    # Public:
    public $id;
    public $username;
    public $role;
    public $pwd;

    public function setPwd(string $pwd) {
        $this->pwd = password_hash($pwd, PASSWORD_DEFAULT);
    }

    public function getPwd() {
        return $this->pwd;
    }

    public function getRole() {
        if(empty($this->role)) {
            $this->role = '["ROLE_USER"]';
        }

        return $this->role;
    }

    public function __toString() {
        return $this->username;
    }
}