<?php


namespace Sanf\Integration;


class InternalService
{
    public function findCustomerByEmail($email)
    {
        $response = \NbsPhp\ApiWrapper\Api\Request::route('customer.find-by-email')
            ->pathParams(['email' => $email])
            ->send();
        return $response->json();
    }
}
