<?php

namespace Sanf\Core\Encryptions;

class SodiumEncryptor
{
    private $key;

    private $nonce;

    public static function make($nonce = null): self
    {
        return new self($nonce);
    }

    public function __construct($nonce = null)
    {
        $this->key = SodiumKeyManager::make()->getKeyBin();

        $this->nonce = $nonce ? $this->existingNonce($nonce) : $this->generateNonce();
    }

    /**
     * @param resource $nonce A stream resource (e.g. returned from fopen)
     */
    private function existingNonce($nonce): SodiumNonce
    {
        if (is_string($nonce)) {
            return new SodiumNonce(hex2bin(substr($nonce, 2)), $nonce);
        }

        $nonce = stream_get_contents($nonce, -1, 0);

        return new SodiumNonce($nonce, '\x' . bin2hex($nonce));
    }

    private function generateNonce(): SodiumNonce
    {
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $nonceHex = '\x' . bin2hex($nonce);

        return new SodiumNonce($nonce, $nonceHex);
    }

    public function encrypt($value): ?string
    {
        if (is_null($value)) {
            return null;
        }

        $encryptedData = sodium_crypto_secretbox(
            $value,
            $this->nonce->getNonce(),
            $this->key
        );

        // return hex string
        return '\x' . bin2hex($encryptedData);
    }

    public function encryptForJson($data)
    {
        $data = $data ? json_encode($data) : null;

        return $this->encrypt($data);
    }

    public function hash($value): string
    {
        $hashedValue = hash('sha256', $value, true); // binary

        return '\x' . bin2hex($hashedValue);
    }

    public function nonce(): SodiumNonce
    {
        return $this->nonce;
    }

    public function query(): SodiumQuery
    {
        return new SodiumQuery();
    }

    public function encryptBulkData(array $data, array $fields = [], array $jsonFields = []): array
    {
        foreach ($data as $key => $value) {
            if (in_array($key, $fields)) {
                $data[$key] = $this->encrypt($value);
                continue;
            }

            if (in_array($key, $jsonFields)) {
                $data[$key] = $this->encryptForJson($value);
            }
        }

        $data['nonce'] = $this->nonce()->getNonceHex();

        return $data;
    }
}
