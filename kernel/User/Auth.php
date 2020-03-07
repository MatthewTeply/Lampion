<?php

namespace Lampion\User;

use Lampion\Session\Lampion as LampionSession;
use Lampion\Database\Query;
use Lampion\User\Entity\User;

class Auth
{
    /**
     * @param string $username
     * @param string $pwd
     * @return
     *  - null = client is already logged in
     *  - 0    = wrong credentials
     *  - 1    = logged in
     */
    public static function login(string $username, string $pwd) {
        # If user is already set, return null
        if(LampionSession::get('user') !== null) {
            return null;
        }

        $q = Query::select('user', ['id'], [
            'username' => ['=', $username]
        ])[0];

        # Wrong credentials (username)
        if(!isset($q['id'])) {
            return 0;
        }

        $user = new User($q['id']);

        # Logged in
        if(password_verify($pwd, $user->getPassword())) {
            # Pass user entity into session, so it can be easily accessed later

            $user->token = Session::create($user->id);

            LampionSession::set('user', $user);
            
            return 1;
        }

        # Wrong credentials (password)
        else {
            return 0;
        }
    }
    
    public static function logout() {
        LampionSession::unset('user');
    }

    public static function isLoggedIn() {
        if(LampionSession::get('user') === null) {
            return false;
        }
        
        $user = new User(LampionSession::get('user')->id);

        if(!$user) {
            return false;
        }
    }
}