<?php

namespace Sanf\Core\Modules\Scanina\Specifications;

use Sanf\Core\Modules\Scanina\Dtos\AddToCartRequestDto;
use Sanf\Core\Modules\Scanina\Dtos\ScaninaUserRegisterRequestDto;

class GuzzleScaninaUserSpecification implements ScaninaUserSpecificationInterface
{
    /**
     * @return GuzzlePostUserAccountSpecification
     */
    public function account(string $email): GuzzlePostUserAccountSpecification
    {
        return new GuzzlePostUserAccountSpecification($email);
    }

    /**
     * @param ScaninaUserRegisterRequestDto $parameter
     * @return GuzzlePostUserRegisterSpecification
     */
    public function register($parameter): GuzzlePostUserRegisterSpecification
    {
        return new GuzzlePostUserRegisterSpecification($parameter);
    }

    /**
     * @param AddToCartRequestDto $parameter
     * @return GuzzleAddToCartSpecification
     */
    public function addToCart($parameter)
    {
        return new GuzzleAddToCartSpecification($parameter);
    }

    /**
     * @return GuzzleResendEmailVerificationSpecification
     */
    public function resendMailVerification(string $email): GuzzleResendEmailVerificationSpecification
    {
        return new GuzzleResendEmailVerificationSpecification($email);
    }
}
