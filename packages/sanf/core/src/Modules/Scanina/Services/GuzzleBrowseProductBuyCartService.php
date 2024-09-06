<?php

namespace Sanf\Core\Modules\Scanina\Services;

use Exception;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductSpecificationResponseDto;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductSubSpecificationResponseDto;
use Sanf\Core\Modules\Scanina\Dtos\ReadProductBuyResponseDto;
use Sanf\Core\Modules\Scanina\Enums\ScaninaProductTypeEnum;
use Sanf\Core\Modules\Scanina\Exceptions\ScaninaProductNotFoundException;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaProductRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaProductSpecificationInterface;
use Sanf\Core\Modules\User\Repositories\ProfileRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

class GuzzleBrowseProductBuyCartService implements ApplicationServiceInterface
{
    private UserRepositoryInterface $userRepository;
    private ProfileRepositoryInterface $profileRepository;
    private ScaninaProductRepositoryInterface $productRepository;
    private ScaninaProductSpecificationInterface $productSpecification;

    public function __construct(
        UserRepositoryInterface $userRepository,
        ProfileRepositoryInterface $profileRepository,
        ScaninaProductRepositoryInterface $productRepository,
        ScaninaProductSpecificationInterface $productSpecification
    ) {
        $this->userRepository = $userRepository;
        $this->profileRepository = $profileRepository;
        $this->productRepository = $productRepository;
        $this->productSpecification = $productSpecification;
    }

    public function execute($dto = null)
    {
        $user = $this->userRepository->findById($dto->userId);
        if (!$user) {
            throw new UserNotFoundException();
        }

        $profile = $this->profileRepository->findById($dto->profileXid);
        if (is_null($profile)) {
            throw new UserNotFoundException('');
        }

        $response = $this->productRepository->get(
            $this->productSpecification->getCart($profile->getEmail(), 'buy') //TODO use const value
        );

        $records = [];
        foreach ($response->data as $merchant) {
            foreach ($merchant->products as $product) {
                $records[] = $product;
            }
        }

        $responseProductBuy = $this->syncWithApi($records);

        return (object) [
            'data' => $responseProductBuy,
            'paginate' => (object) [
                'total' => 0,
                'count' => 0,
                'skip' => null,
                'limit' => null,
                'sort_by' => null,
            ],
        ];
    }

    private function syncWithApi(array $records): array
    {
        $responses = [];
        foreach ($records as $product) {
            try {
                $productBuyResponse = $this->productRepository->get(
                    $this->productSpecification->readBuy($product->xid)
                );
            } catch (Exception $exception) {
                if ($exception instanceof ScaninaProductNotFoundException) {
                    continue;
                }
                throw $exception;
            }

            $data = (array) $productBuyResponse->data;
            unset($data['review']);
            $productBuyResponseDto = new ReadProductBuyResponseDto($data);
            $productBuyResponseDto->xid = $product->xid;
            $productBuyResponseDto->quantity = $product->quantity;

            $productBuySpecificationResponse = $this->productRepository->get(
                $this->productSpecification->getSpecification($product->xid, ScaninaProductTypeEnum::BUY)
            );

            $specifications = array_map(function ($specification) {
                $specification->subSpecification = array_map(function ($subSpecification) {
                    return new BrowseProductSubSpecificationResponseDto((array) $subSpecification);
                }, $specification->subSpecification);

                return new BrowseProductSpecificationResponseDto((array) $specification);
            }, $productBuySpecificationResponse->data->rows);

            $subSpecifications = [];
            foreach ($specifications as $specification) {
                foreach ($specification->subSpecification as $subSpecification) {
                    $subSpecifications[] = $subSpecification;
                }
            }
            $productBuyResponseDto->subSpecifications = $subSpecifications;

            $responses[] = $productBuyResponseDto;
        }

        return $responses;
    }
}
