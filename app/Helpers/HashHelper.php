<?php

namespace App\Helpers;

use Exception;
use phpseclib3\Crypt\AES;

class HashHelper 
{

    public static function prepareHash(array $data): string
    {
        // условия кодирования. Ключ и последовательность
        // Ключи должны быть как у клиента так и у сервера. В нашем случае только у нас.
        $key = env("ENKRYPT_KEY");
        $iv = env("IV_KEY");

        $data = json_encode($data);

        $cipher = new AES('cbc');

        $keyHash = substr(hash('sha256', $key, false), 0, 32);
        $ivHash = substr(hash('sha256', $iv, false), 0, 16);

        $cipher->setKey($keyHash);
        $cipher->setIV($ivHash);
        $encrypted = $cipher->encrypt($data);
        $encode = base64_encode($encrypted);

        return str_replace(['+','/','='], ['-','_','&&'], $encode);
    }


    public static function decryptHash(string $hash): array
    {
        $key = env("ENKRYPT_KEY");
        $iv = env("IV_KEY");

        $cipher = new AES('cbc');

        $keyHash = substr(hash('sha256', $key, false), 0, 32);
        $ivHash = substr(hash('sha256', $iv, false), 0, 16);

        $cipher->setKey($keyHash);
        $cipher->setIV($ivHash);

        $encrypt = str_replace(['-','_','&&'], ['+','/','='], $hash);

        try {
            $decrypted = $cipher->decrypt(base64_decode($encrypt));
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        return  json_decode($decrypted, true);

        
    }

}