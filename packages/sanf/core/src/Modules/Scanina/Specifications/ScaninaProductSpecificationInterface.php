<?php

namespace Sanf\Core\Modules\Scanina\Specifications;

interface ScaninaProductSpecificationInterface
{
    public function getBuy($parameter);

    public function getRent($parameter);

    public function getService($parameter);

    public function getSparePart($parameter);

    public function readBuy(string $xid);

    public function readRent(string $xid);

    public function readSparePart(string $xid);

    public function readService(string $xid);

    public function getSpecification(string $xid, int $type);

    public function getCustomerReview($parameter);

    public function getFilterCategory($parameter);

    public function getFilterBrand($parameter);

    public function getFilterType($parameter);

    public function getFilterModel($parameter);

    public function getCart(string $email, string $module);
}
