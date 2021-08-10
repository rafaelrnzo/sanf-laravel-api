<?php


namespace Sanf\Integration;


class InternalApiClient
{
    public function findCustomerByEmail($email)
    {
        $response = \NbsPhp\ApiWrapper\Api\Request::route('customer.find-by-email')
            ->pathParams(['email' => $email])
            ->send();
        return $response->json();
    }

    public function findCustomerById($id)
    {
        $response = \NbsPhp\ApiWrapper\Api\Request::route('customer.find-by-id')
            ->pathParams(['id' => 1])
            ->send();
        return $response->json();
    }
}
