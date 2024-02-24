<?php

namespace App\Helpers;

class UserAuthHelper 
{

    public static function signatureVerification(array $userData)
    {
        $sig = $userData['sig'];
        unset($userData['sig']);

        ksort($userData);
        
        $string = '';

        foreach ($userData as $key => $value) {
            $string .= $key . '=' . $value;
        }

        $string .= env("SEKRET_KEY");
        $md5String = mb_strtolower(md5($string), 'UTF-8');
        
        return $md5String === $sig;
    }


}