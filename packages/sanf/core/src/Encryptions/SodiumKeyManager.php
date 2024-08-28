<?php

namespace Sanf\Core\Encryptions;

class SodiumKeyManager
{
    protected $key;

    public static function make(): self
    {
        return new self();
    }

    public function __construct()
    {
        $this->key = $this->existingKey();
    }

    /**
     * @return string hexadecimal key
     */
    public function generateKey(): string
    {
        $newKey = sodium_crypto_secretbox_keygen();

        return bin2hex($newKey);
    }

    /**
     * @return string|null
     */
    public function existingKey()
    {
        return config('encryption.key');
    }

    /**
     * @return string|null binary
     */
    public function getKeyBin()
    {
        if (is_null($this->key)) {
            return null;
        }

        return hex2bin($this->getKeyHex());
    }

    /**
     * @return string|null hexadecimal
     */
    public function getKeyHex()
    {
        if (is_null($this->key)) {
            return null;
        }

        return $this->key;
    }

    /**
     * @return string|null key with prefix "\x"
     */
    public function getPrefixKeyHex(): string
    {
        if (is_null($this->key)) {
            return null;
        }

        return '\x' . $this->getKeyHex();
    }
}
