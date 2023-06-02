<?php

namespace Sanf\Integration\Modules\Scanina;

use NbsPhp\ApiWrapper\Api\Request;
use Sanf\Core\Modules\Scanina\Dtos\AddToCartRequestDto;
use Sanf\Core\Modules\Scanina\Dtos\ScaninaProductBuyFilterDto;
use Sanf\Core\Modules\Scanina\Dtos\ScaninaProductFilterDto;
use Sanf\Core\Modules\Scanina\Dtos\ScaninaProductRentFilterDto;
use Sanf\Core\Modules\Scanina\Dtos\ScaninaProductServiceFilterDto;
use Sanf\Core\Modules\Scanina\Dtos\ScaninaProductSparePartFilterDto;
use Sanf\Core\Modules\Scanina\Dtos\ScaninaUserRegisterRequestDto;

class ScaninaApiClient
{
    public const DEFAULT_SKIP = 0;
    public const DEFAULT_LIMIT = 2147483647;
    public const DEFAULT_ORDER = 'Latest';

    protected $client;

    public function __construct()
    {
        //TODO INJECT
        $this->client = app(\GuzzleHttp\Client::class);
    }

    public function getBuy(ScaninaProductBuyFilterDto $arguments)
    {
        $response = Request::route('scanina.product.buy.browse', $this->client)
            ->queryParams($arguments->toArray())
            ->send();

        return $response->json(false);
    }

    public function getRent(ScaninaProductRentFilterDto $arguments)
    {
        $response = Request::route('scanina.product.rent.browse', $this->client)
            ->queryParams($arguments->toArray())
            ->send();

        return $response->json(false);
    }

    public function getSparePart(ScaninaProductSparePartFilterDto $arguments)
    {
        $response = Request::route('scanina.product.spare-part.browse', $this->client)
            ->queryParams($arguments->toArray())
            ->send();

        return $response->json(false);
    }

    public function getService(ScaninaProductServiceFilterDto $arguments)
    {
        $response = Request::route('scanina.product.service.browse', $this->client)
            ->queryParams($arguments->toArray())
            ->send();

        return $response->json(false);
    }

    public function readBuy(string $xid)
    {
        $response = Request::route('scanina.product.buy.read', $this->client)
            ->pathParams([
                'xid' => $xid,
            ])
            ->send();

        return $response->json(false);
    }

    public function readRent(string $xid)
    {
        $response = Request::route('scanina.product.rent.read', $this->client)
            ->pathParams([
                'xid' => $xid,
            ])
            ->send();

        return $response->json(false);
    }

    public function getSpecification(string $xid, int $type)
    {
        $response = Request::route('scanina.product.specification.browse', $this->client)
            ->queryParams([
                'sellTypeId' => $type,
                'xid' => $xid,
            ])
            ->send();

        return $response->json(false);
    }

    public function readSparePart(string $xid)
    {
        $response = Request::route('scanina.product.spare-part.read', $this->client)
            ->pathParams([
                'xid' => $xid,
            ])
            ->send();

        return $response->json(false);
    }

    public function readService(string $xid)
    {
        $response = Request::route('scanina.product.service.read', $this->client)
            ->pathParams([
                'xid' => $xid,
            ])
            ->send();

        return $response->json(false);
    }

    public function getCustomerReview($arguments)
    {
        $response = Request::route('scanina.product.customer-review.browse', $this->client)
            ->queryParams([
                'productXid' => $arguments->productXid,
                'sellTypeId' => $arguments->type,
                'skip' => $arguments->skip,
                'limit' => $arguments->limit,
                'sort_by' => $arguments->sortBy,
            ])
            ->send();

        return $response->json(false);
    }

    public function getFilterCategory(ScaninaProductFilterDto $arguments)
    {
        $response = Request::route("scanina.product.{$arguments->type}.category.browse", $this->client)
            ->queryParams($arguments->toArray())
            ->send();

        return $response->json(false);
    }

    public function getFilterBrand(ScaninaProductFilterDto $arguments)
    {
        $response = Request::route("scanina.product.{$arguments->type}.brand.browse", $this->client)
            ->queryParams($arguments->toArray())
            ->send();

        return $response->json(false);
    }

    public function getFilterType(ScaninaProductFilterDto $arguments)
    {
        $response = Request::route("scanina.product.{$arguments->type}.type.browse", $this->client)
            ->queryParams($arguments->toArray())
            ->send();

        return $response->json(false);
    }

    public function getFilterModel(ScaninaProductFilterDto $arguments)
    {
        $response = Request::route("scanina.product.{$arguments->type}.model.browse", $this->client)
            ->queryParams($arguments->toArray())
            ->send();

        return $response->json(false);
    }

    public function getAccount(string $email)
    {
        $response = Request::route("scanina.user.account.check", $this->client)
            ->json(['email' => $email])
            ->send();

        return $response->json(false);
    }

    public function register(ScaninaUserRegisterRequestDto $dto)
    {
        $bodyRequest = array_filter($dto->toArray(), function ($value) {
            return !is_null($value);
        });

        $response = Request::route("scanina.user.account.register", $this->client)
            ->headers([
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])
            ->formParams($bodyRequest)
            ->send();

        return $response->json(false);
    }

    public function addToCart(AddToCartRequestDto $dto)
    {
        $bodyRequest = array_filter($dto->toArray(), function ($value) {
            return !is_null($value);
        });

        $response = Request::route("scanina.user.account.add-cart", $this->client)
            ->headers([
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])
            ->formParams($bodyRequest)
            ->send();

        return $response->json(false);
    }

    public function resendEmail(string $email)
    {
        $response = Request::route("scanina.user.account.resend-mail", $this->client)
            ->json(['email' => $email])
            ->send();

        return $response->json(false);
    }

    public function getCountry(object $arguments)
    {
        $response = Request::route('scanina.country.browse', $this->client)
            ->queryParams((array)$arguments)
            ->send();

        return $response->json(false);
    }

    public function getCity(object $arguments)
    {
        $response = Request::route('scanina.city.browse', $this->client)
            ->queryParams((array)$arguments)
            ->send();

        return $response->json(false);
    }

    public function getBusinessSector(object $arguments)
    {
        $response = Request::route('scanina.business-sector.browse', $this->client)
            ->queryParams((array)$arguments)
            ->send();

        return $response->json(false);
    }

    public function getMerchant(object $arguments)
    {
        $response = Request::route('scanina.merchant.browse', $this->client)
            ->queryParams((array)$arguments)
            ->send();

        return $response->json(false);
    }
}
