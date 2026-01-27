<?php

namespace Sanf\Core\Encryptions;

class SodiumDecryptor
{
    private $key;

    private $nonce;

    public function __construct($nonce)
    {
        $this->key = SodiumKeyManager::make()->getKeyBin();
        $this->nonce = $nonce;
    }

    public static function make($nonce): self
    {
        return new self($nonce);
    }

    public function decrypt($encryptedValue)
    {
        if (empty($encryptedValue)) {
            return null;
        }

        // supply a maxlength, and offset to handle return empty string
        $encryptedBinary = stream_get_contents($encryptedValue, -1, 0);

        // supply a maxlength, and offset to handle return empty string
        $nonceBinary = stream_get_contents($this->nonce, -1, 0);

        $result = sodium_crypto_secretbox_open(
            $encryptedBinary,
            $nonceBinary,
            $this->key
        );

        // return null when failed to decrypt
        if ($result === false) {
            return null;
        }

        return $result;
    }

    public function decryptFromHex($encryptedHex)
    {
        if (empty($encryptedHex)) {
            return null;
        }

        // supply a maxlength, and offset to handle return empty string
        $encryptedBinary = hex2bin(substr($encryptedHex, 2));

        // supply a maxlength, and offset to handle return empty string
        $nonceBinary = stream_get_contents($this->nonce, -1, 0);

        $result = sodium_crypto_secretbox_open(
            $encryptedBinary,
            $nonceBinary,
            $this->key
        );

        // return null when failed to decrypt
        if ($result === false) {
            return null;
        }

        return $result;
    }
}
