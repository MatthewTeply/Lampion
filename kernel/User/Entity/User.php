<?php

namespace Lampion\User\Entity;

class User
{
    /** @var(type="int", length=11) */
    public $id;

    /** @var(type="varchar", length=255) */
    public $username;

    /** @var(type="json") */
    public $role;

    /** @var(type="varchar", length=255) */
    public $pwd;

    public function setPwd(string $pwd) {
        if(!empty($pwd)) {
            if(!password_verify($pwd, $this->pwd)) {
                return password_hash($pwd, PASSWORD_DEFAULT);
            }
        }
    }

    public function getPwd() {
        return $this->pwd;
    }

    public function getRole() {
        $role = json_decode($this->role, true);

        if(is_array($role)) {
            if(!in_array('ROLE_USER', $role) || empty($role)) {
                $role[] = 'ROLE_USER';
            }

            $this->role = json_encode($role);
        }

        return $this->role;
    }

    public function __toString() {
        return $this->username;
    }
}