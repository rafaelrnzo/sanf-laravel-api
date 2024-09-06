<?php

namespace Sanf\Core\Modules\Scanina\Services;

use Exception;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Dtos\ReadProductSparePartResponseDto;
use Sanf\Core\Modules\Scanina\Exceptions\ScaninaProductNotFoundException;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaProductRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaProductSpecificationInterface;
use Sanf\Core\Modules\User\Repositories\ProfileRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

class GuzzleBrowseProductSparePartCartService implements ApplicationServiceInterface
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
            $this->productSpecification->getCart($profile->getEmail(), 'spare-part') //TODO use const value
        );

        $records = [];
        foreach ($response->data as $merchant) {
            foreach ($merchant->products as $product) {
                $records[] = $product;
            }
        }

        $responseProductSparePart = $this->syncWithApi($records);

        return (object) [
            'data' => $responseProductSparePart,
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
                $productSparePartResponse = $this->productRepository->get(
                    $this->productSpecification->readSparePart($product->xid)
                );
            } catch (Exception $exception) {
                if ($exception instanceof ScaninaProductNotFoundException) {
                    continue;
                }
                throw $exception;
            }

            $data = (array) $productSparePartResponse->data;
            unset($data['reviews']);
            $productSparePartResponseDto = new ReadProductSparePartResponseDto($data);
            $productSparePartResponseDto->xid = $product->xid;
            $productSparePartResponseDto->quantity = $product->quantity;
            $productSparePartResponseDto->customerReviews = [];

            $responses[] = $productSparePartResponseDto;
        }

        return $responses;
    }
}
