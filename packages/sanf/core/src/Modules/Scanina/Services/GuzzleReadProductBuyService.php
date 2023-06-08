<?php

namespace Sanf\Core\Modules\Scanina\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductSpecificationResponseDto;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductSubSpecificationResponseDto;
use Sanf\Core\Modules\Scanina\Dtos\ReadProductBuyResponseDto;
use Sanf\Core\Modules\Scanina\Enums\ScaninaProductTypeEnum;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaProductRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaProductSpecificationInterface;

class GuzzleReadProductBuyService implements ApplicationServiceInterface
{
    private ScaninaProductRepositoryInterface $repository;
    private ScaninaProductSpecificationInterface $specification;

    public function __construct(
        ScaninaProductRepositoryInterface $repository,
        ScaninaProductSpecificationInterface $specification
    ) {
        $this->repository = $repository;
        $this->specification = $specification;
    }

    public function execute($dto = null)
    {
        $productBuyResponse = $this->repository->get(
            $this->specification->readBuy($dto->xid)
        );

        $data = (array)$productBuyResponse->data;
        unset($data['review']);
        $productBuyResponseDto = new ReadProductBuyResponseDto($data);

        $productBuySpecificationResponse = $this->repository->get(
            $this->specification->getSpecification($dto->xid, ScaninaProductTypeEnum::BUY)
        );

        $specifications = array_map(function ($specification) {
            $specification->subSpecification = array_map(function ($subSpecification) {
                return new BrowseProductSubSpecificationResponseDto((array)$subSpecification);
            }, $specification->subSpecification);

            return new BrowseProductSpecificationResponseDto((array)$specification);
        }, $productBuySpecificationResponse->data->rows);

        $subSpecifications = [];
        foreach ($specifications as $index => $specification) {
            $subSpecifications[$index] = (object)[
                'name' => $specification->name,
                'subSpecificationColumn' => [],
            ];
            if (!is_null($specification->subSpecification)) {
                foreach ($specification->subSpecification as $subSpecification) {
                    $subSpecifications[$index] = $subSpecification;
                }
            }
        }
        $productBuyResponseDto->subSpecifications = $subSpecifications;

        return $productBuyResponseDto;
    }
}
