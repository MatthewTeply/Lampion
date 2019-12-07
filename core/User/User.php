<?php

namespace Lampion\User;

use Lampion\Database\ORM;

class User extends ORM
{
    public $id;
    public $username;
    protected $pwd;
    public $role;

    public $loggedIn;

    public function __construct($id = null) {
        $this->initORM($id, "users");
    }

    public function setPassword(string $pwd) {
        $this->pwd = password_hash($pwd, PASSWORD_DEFAULT);
    }

    public function save() {
        $this->saveORM();
    }
}