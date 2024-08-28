<?php

namespace Sanf\Core\Encryptions;

class SodiumNonce
{
    private $nonce;
    private $nonceHex;

    public function __construct($nonce, $nonceHex)
    {
        $this->nonce = $nonce;
        $this->nonceHex = $nonceHex;
    }

    public function getNonce()
    {
        return $this->nonce;
    }

    public function getNonceHex()
    {
        return $this->nonceHex;
    }
}
