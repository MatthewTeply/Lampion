<?php

namespace Lampion\User;

use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Lampion\Core\Cookie;
use Lampion\Session\Lampion as LampionSession;
use Lampion\Database\Query;
use Lampion\Entity\EntityManager;
use Lampion\User\Entity\User;
use Lampion\User\Session as UserSession;

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
            'username' => $username
        ])[0];

        # Wrong credentials (username)
        if(!isset($q['id'])) {
            return 0;
        }

        $em = new EntityManager();

        $user = $em->find(User::class, $q['id']);

        # Logged in
        if(password_verify($pwd, $user->getPwd())) {
            unset($user->pwd);

            # Pass user entity into session, so it can be easily accessed later
            $token = UserSession::create(serialize($user));

            Cookie::set('lampionToken', $token, [ 'w' => 1 ]);
            //Cookie::set('_lampionToken', ' ', [ 'w' => 1 ]);

            UserSession::set($user);

            return true;
        }

        # Wrong credentials (password)
        else {
            return false;
        }
    }
    
    public static function logout() {
        $em = new EntityManager();
        $jwt = self::decodeJWT(Cookie::get('lampionToken'));

        $userSession = $em->findBy(Session::class, [
            'session_id' => $jwt->data->id
        ]);

        if($userSession) {
            $em->destroy($userSession);
        }
        
        Cookie::destroy('lampionToken');
        
        LampionSession::unset('user');
    }

    public static function isLoggedIn($token = null) {
        $token = $token ?? Cookie::get('lampionToken');
        $jwt   = self::decodeJWT($token);

        if(!$jwt) {
            return false;
        }

        $em = new EntityManager();
        
        # Check if user exists
        $user = $em->find(User::class, $jwt->data->user->id);

        if(!$user) {
            self::logout();

            return false;
        }

        # If user exists, but is not set in session, set it
        @UserSession::set($user);

        # Check if session is valid
        $userSession = $em->findBy(Session::class, [
            'session_id' => $jwt->data->id
        ]);

        if(!$userSession) {
            self::logout();

            return false;
        }

        return true;
    }

    public static function decodeJWT($token) {
        if($token == null) {
            return null;
        }
        
        try {
            $tks = explode('.', $token);       
            list($headb64, $bodyb64, $cryptob64) = $tks;
            $header = JWT::jsonDecode(JWT::urlsafeB64Decode($headb64));

            try {
                return JWT::decode($token, JWT_SECRET_KEY, [$header->alg]);
            }

            catch(BeforeValidException $e) {
                return null;
            }
        }

        catch(ExpiredException $e) {
            return null;
        }
    }
}