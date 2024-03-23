<?php

namespace Sanf\Core\Modules\Scanina\Specifications;

use Sanf\Core\Modules\Scanina\Dtos\BrowseProductBuyRequestDto;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductFilterRequestDto;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductRentRequestDto;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductServiceRequestDto;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductSparePartRequestDto;

class GuzzleScaninaProductSpecification implements ScaninaProductSpecificationInterface
{
    /**
     * @param BrowseProductBuyRequestDto $parameter
     * @return GuzzleGetBuySpecification
     */
    public function getBuy($parameter): GuzzleGetBuySpecification
    {
        return new GuzzleGetBuySpecification($parameter);
    }

    /**
     * @param BrowseProductRentRequestDto $parameter
     * @return GuzzleGetRentSpecification
     */
    public function getRent($parameter): GuzzleGetRentSpecification
    {
        return new GuzzleGetRentSpecification($parameter);
    }

    /**
     * @param BrowseProductServiceRequestDto $parameter
     * @return GuzzleGetServiceSpecification
     */
    public function getService($parameter): GuzzleGetServiceSpecification
    {
        return new GuzzleGetServiceSpecification($parameter);
    }

    /**
     * @param BrowseProductSparePartRequestDto $parameter
     * @return GuzzleGetSparePartSpecification
     */
    public function getSparePart($parameter): GuzzleGetSparePartSpecification
    {
        return new GuzzleGetSparePartSpecification($parameter);
    }

    /**
     * @param string $xid
     * @return GuzzleReadBuySpecification
     */
    public function readBuy(string $xid): GuzzleReadBuySpecification
    {
        return new GuzzleReadBuySpecification($xid);
    }

    /**
     * @param string $xid
     * @return GuzzleReadRentSpecification
     */
    public function readRent(string $xid): GuzzleReadRentSpecification
    {
        return new GuzzleReadRentSpecification($xid);
    }

    /**
     * @param string $xid
     * @return GuzzleGetSpecSpecification
     */
    public function getSpecification(string $xid, int $type): GuzzleGetSpecSpecification
    {
        return new GuzzleGetSpecSpecification($xid, $type);
    }

    /**
     * @param string $xid
     * @return GuzzleReadSparePartSpecification
     */
    public function readSparePart(string $xid): GuzzleReadSparePartSpecification
    {
        return new GuzzleReadSparePartSpecification($xid);
    }

    /**
     * @param string $xid
     * @return GuzzleReadServiceSpecification
     */
    public function readService(string $xid): GuzzleReadServiceSpecification
    {
        return new GuzzleReadServiceSpecification($xid);
    }

    /**
     * @param object $dto
     * @return GuzzleGetCustomerReviewSpecification
     */
    public function getCustomerReview($parameter): GuzzleGetCustomerReviewSpecification
    {
        return new GuzzleGetCustomerReviewSpecification($parameter);
    }

    /**
     * @param BrowseProductFilterRequestDto $parameter
     * @return GuzzleGetFilterCategorySpecification
     */
    public function getFilterCategory($parameter): GuzzleGetFilterCategorySpecification
    {
        return new GuzzleGetFilterCategorySpecification($parameter);
    }

    /**
     * @param BrowseProductFilterRequestDto $parameter
     * @return GuzzleGetFilterBrandSpecification
     */
    public function getFilterBrand($parameter): GuzzleGetFilterBrandSpecification
    {
        return new GuzzleGetFilterBrandSpecification($parameter);
    }

    /**
     * @param BrowseProductFilterRequestDto $parameter
     * @return GuzzleGetFilterTypeSpecification
     */
    public function getFilterType($parameter): GuzzleGetFilterTypeSpecification
    {
        return new GuzzleGetFilterTypeSpecification($parameter);
    }

    /**
     * @param BrowseProductFilterRequestDto $parameter
     * @return GuzzleGetFilterModelSpecification
     */
    public function getFilterModel($parameter): GuzzleGetFilterModelSpecification
    {
        return new GuzzleGetFilterModelSpecification($parameter);
    }

    /**
     * @param string $email
     * @param string $module
     * @return GuzzleGetCartSpecification
     */
    public function getCart(string $email, string $module): GuzzleGetCartSpecification
    {
        return new GuzzleGetCartSpecification($email, $module);
    }
}
