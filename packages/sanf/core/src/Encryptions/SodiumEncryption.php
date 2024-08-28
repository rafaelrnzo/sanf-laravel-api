<?php

namespace Sanf\Core\Encryptions;

class SodiumEncryption
{
    public static function hash($value): string
    {
        $hashedValue = hash('sha256', $value, true); // binary

        return '\x' . bin2hex($hashedValue);
    }

    public static function encryptor($nonce = null): SodiumEncryptor
    {
        return SodiumEncryptor::make($nonce);
    }

    public static function decryptor($nonce): SodiumDecryptor
    {
        return SodiumDecryptor::make($nonce);
    }

    public static function query(): SodiumQuery
    {
        return SodiumQuery::make();
    }

    public static function keyManager(): SodiumKeyManager
    {
        return SodiumKeyManager::make();
    }
}
