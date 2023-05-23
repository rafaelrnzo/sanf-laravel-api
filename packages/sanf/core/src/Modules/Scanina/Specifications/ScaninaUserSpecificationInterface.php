<?php

namespace Sanf\Core\Modules\Scanina\Specifications;

interface ScaninaUserSpecificationInterface
{
    public function account(string $email);
    public function register($parameter);

    public function addToCart($parameter);
    public function resendMailVerification(string $email);
}
