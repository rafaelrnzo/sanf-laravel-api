<?php

namespace Sanf\Core\Modules\Scanina\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductSpecificationResponseDto;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductSubSpecificationResponseDto;
use Sanf\Core\Modules\Scanina\Dtos\ReadProductBuyResponseDto;
use Sanf\Core\Modules\Scanina\Enums\ScaninaProductTypeEnum;
use Sanf\Core\Modules\Scanina\Exceptions\ScaninaProductNotFoundException;
use Sanf\Core\Modules\Scanina\Repositories\ProductCartRepositoryInterface;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaProductRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaProductSpecificationInterface;

class ReadBuyCartByUserService implements ApplicationServiceInterface
{
    private ProductCartRepositoryInterface $repository;
    private ScaninaProductRepositoryInterface $productRepository;
    private ScaninaProductSpecificationInterface $productSpecification;

    public function __construct(
        ProductCartRepositoryInterface $repository,
        ScaninaProductRepositoryInterface $productRepository,
        ScaninaProductSpecificationInterface $productSpecification
    ) {
        $this->repository = $repository;
        $this->productRepository = $productRepository;
        $this->productSpecification = $productSpecification;
    }

    public function execute($dto = null)
    {
        $productBuyRecord = $this->repository->findByXid($dto->productXid);
        if (is_null($productBuyRecord)) {
            throw new ScaninaProductNotFoundException();
        }

        $productBuyResponse = $this->productRepository->get(
            $this->productSpecification->readBuy($productBuyRecord->snapshot_response_body->xid)
        );

        $data = (array)$productBuyResponse->data;
        unset($data['review']);

        $productBuyResponseDto = new ReadProductBuyResponseDto($data);
        $productBuyResponseDto->xid = $productBuyRecord->xid;

        $productBuySpecificationResponse = $this->productRepository->get(
            $this->productSpecification->getSpecification(
                $productBuyRecord->snapshot_response_body->xid,
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
