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

    public function getProvinces()
    {
        $response = \NbsPhp\ApiWrapper\Api\Request::route('location.provinces')->send();

        return $response->json();
    }

    public function getCities($province_id)
    {
        $response = \NbsPhp\ApiWrapper\Api\Request::route('location.cities')
            ->pathParams(['province_id' => $province_id])
            ->send();

        return $response->json();
    }

    public function getDistrict($province_id, $city_id)
    {
        $response = \NbsPhp\ApiWrapper\Api\Request::route('location.districts')
            ->pathParams([
                'province_id' => $province_id,
                'city_id' => $city_id,
            ])->send();

        return $response->json();
    }

    public function getSubDistrict($province_id, $city_id, $district_name)
    {
        $response = \NbsPhp\ApiWrapper\Api\Request::route('location.sub-districts')
            ->pathParams([
                'province_id' => $province_id,
                'city_id' => $city_id,
                'district_id' => $district_name,
            ])->send();

        return $response->json();
    }

    public function getPosition()
    {
        $response = \NbsPhp\ApiWrapper\Api\Request::route('customer.positions')->send();

        return $response->json();
    }

    public function getTitle($type)
    {
        $response = \NbsPhp\ApiWrapper\Api\Request::route('customer.titles')
            ->pathParams([
                'type' => $type,
            ])->send();

        return $response->json();

    }
}
