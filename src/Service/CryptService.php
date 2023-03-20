<?php

namespace App\Service;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

class CryptService
{
    private ?string $cryptalgo = null;
    private ?string $cryptkey = null;

    public function __construct(string $cryptkey, string $cryptalgo){
        $this->cryptkey = $cryptkey;
        $this->cryptalgo = $cryptalgo;
    }

    public function decrypt($data)
    {
        if($data == null){
            return null;
        }
        return openssl_decrypt(base64_decode($data), $this->cryptalgo, base64_decode($this->cryptkey), true);
    }
    public function encrypt($data)
    {
        if($data == null){
            return null;
        }
        return base64_encode(openssl_encrypt($data, $this->cryptalgo, base64_decode($this->cryptkey), true));
    }

}