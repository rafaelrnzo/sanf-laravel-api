<?php

namespace Sanf\Core\Modules\Scanina\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductSpecificationResponseDto;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductSubSpecificationResponseDto;
use Sanf\Core\Modules\Scanina\Dtos\ReadProductRentResponseDto;
use Sanf\Core\Modules\Scanina\Enums\ScaninaProductTypeEnum;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaProductRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaProductSpecificationInterface;

class ReadRentCartByUserService implements ApplicationServiceInterface
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
        $productRentResponse = $this->productRepository->get(
            $this->productSpecification->readRent($dto->productXid)
        );

        $data = (array) $productRentResponse->data;
        unset($data['review']);

        $productRentResponseDto = new ReadProductRentResponseDto($data);
        $productRentResponseDto->xid = $dto->productXid;

        $productRentSpecificationResponse = $this->productRepository->get(
            $this->productSpecification->getSpecification(
                $dto->productXid,
                ScaninaProductTypeEnum::RENT
            )
        );

        $specifications = array_map(function ($specification) {
            $specification->subSpecification = array_map(function ($subSpecification) {
                return new BrowseProductSubSpecificationResponseDto((array) $subSpecification);
            }, $specification->subSpecification);

            return new BrowseProductSpecificationResponseDto((array) $specification);
        }, $productRentSpecificationResponse->data->rows);

        $subSpecifications = [];
        foreach ($specifications as $index => $specification) {
            $subSpecifications[$index] = (object) [
                'name' => $specification->name,
                'subSpecificationColumn' => $specification->specificationColumn ?? [],
            ];
            if (!is_null($specification->subSpecification)) {
                foreach ($specification->subSpecification as $subSpecification) {
                    $subSpecifications[$index] = $subSpecification;
                }
            }
        }
        $productRentResponseDto->subSpecifications = $subSpecifications;

        return $productRentResponseDto;
    }
}
