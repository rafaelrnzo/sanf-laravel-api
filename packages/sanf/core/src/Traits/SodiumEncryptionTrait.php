<?php

namespace Sanf\Core\Traits;

use Sanf\Core\Encryptions\SodiumDecryptor;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Encryptions\SodiumEncryptor;

trait SodiumEncryptionTrait
{
    protected $decryptor;

    protected $encryptor;

    public function decryptor(): SodiumDecryptor
    {
        if ($this->decryptor) {
            return $this->decryptor;
        }

        return $this->decryptor = SodiumEncryption::decryptor($this->attributes['nonce']);
    }

    public function encryptor(): SodiumEncryptor
    {
        if ($this->encryptor) {
            return $this->encryptor;
        }

        return $this->encryptor = SodiumEncryption::encryptor($this->attributes['nonce']);
    }
}
