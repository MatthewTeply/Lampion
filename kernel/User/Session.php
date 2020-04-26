<?php

namespace Lampion\User;

use Lampion\Entity\EntityManager;
use Lampion\Http\Client;
use Lampion\User\Entity\Session as EntitySession;
use Lampion\Session\Lampion as LampionSession;
use Firebase\JWT\JWT;

class Session {

    public static function create($user) {
        $user    = unserialize($user);
        $session = new EntitySession();
        $em      = new EntityManager();

        $session->session_id = uniqid();
        $session->created    = time();
        $session->updated    = time();
        $session->user       = $user;
        $session->ip         = Client::ip();
        $session->device     = Client::device();
        $session->os         = Client::os();

        $secret_key      = JWT_SECRET_KEY;
        $issuer_claim    = WEB_ROOT; // this can be the servername
        $audience_claim  = '';
        $issuedat_claim  = time(); // issued at
        $notbefore_claim = $issuedat_claim; //not before in seconds
        $expire_claim    = $issuedat_claim + (60 * 60 * 24 * 7); // expire time in seconds

        $token = [
            'iss'  => $issuer_claim,
            'aud'  => $audience_claim,
            'iat'  => $issuedat_claim,
            'nbf'  => $notbefore_claim,
            'exp'  => $expire_claim,
            'data' => [
                'id'   => $session->session_id,
                'user' => [
                    'id'       => $user->id,
                    'username' => $user->username,
                    'role'     => $user->role,
                    'img'      => $user->img
                ]
            ]
        ];

        $token = JWT::encode($token, $secret_key);
        
        if(!$em->persist($session)) {
            exit('Session could not be created!');
        }

        return $token;
    }

    public function set(object $user) {
        LampionSession::set('user', serialize($user));
    }

}