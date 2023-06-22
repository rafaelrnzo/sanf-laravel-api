<?php

namespace Sanf\Core\Modules\Scanina\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductSpecificationResponseDto;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductSubSpecificationResponseDto;
use Sanf\Core\Modules\Scanina\Dtos\ReadProductBuyResponseDto;
use Sanf\Core\Modules\Scanina\Enums\ScaninaProductTypeEnum;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaProductRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaProductSpecificationInterface;

class ReadBuyCartByUserService implements ApplicationServiceInterface
{
    private ScaninaProductRepositoryInterface $productRepository;
    private ScaninaProductSpecificationInterface $productSpecification;

    public function __construct(
        ScaninaProductRepositoryInterface $productRepository,
        ScaninaProductSpecificationInterface $productSpecification
    ) {
        $this->productRepository = $productRepository;
        $this->productSpecification = $productSpecification;
    }

    public function execute($dto = null)
    {
        $productBuyResponse = $this->productRepository->get(
            $this->productSpecification->readBuy($dto->productXid)
        );

        $data = (array)$productBuyResponse->data;
        unset($data['review']);

        $productBuyResponseDto = new ReadProductBuyResponseDto($data);
        $productBuyResponseDto->xid = $dto->productXid;

        $productBuySpecificationResponse = $this->productRepository->get(
            $this->productSpecification->getSpecification(
                $dto->productXid,
                ScaninaProductTypeEnum::BUY
            )
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
                'subSpecificationColumn' => $specification->specificationColumn ?? [],
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
