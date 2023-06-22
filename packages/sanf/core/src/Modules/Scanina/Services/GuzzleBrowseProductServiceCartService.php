<?php

namespace Sanf\Core\Modules\Scanina\Services;

use Exception;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Models\AuthModel;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Dtos\ReadProductServiceResponseDto;
use Sanf\Core\Modules\Scanina\Exceptions\ScaninaProductNotFoundException;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaProductRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaProductSpecificationInterface;
use Sanf\Core\Modules\User\Repositories\ProfileRepositoryInterface;

class GuzzleBrowseProductServiceCartService implements ApplicationServiceInterface
{
    private AuthModel $userRepository;
    private ProfileRepositoryInterface $profileRepository;
    private ScaninaProductRepositoryInterface $productRepository;
    private ScaninaProductSpecificationInterface $productSpecification;

    public function __construct(
        AuthModel $userRepository,
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
        $user = $this->userRepository->findOrFail($dto->userId);
        if (!$user) {
            throw new UserNotFoundException();
        }

        $profile = $this->profileRepository->findById($dto->profileXid);
        if (is_null($profile)) {
            throw new UserNotFoundException('');
        }

        $response = $this->productRepository->get(
            $this->productSpecification->getCart($profile->getEmail(), 'service') //TODO use const value
        );

        $records = [];
        foreach ($response->data as $merchant) {
            foreach ($merchant->products as $product) {
                $records[] = $product;
            }
        }

        $responseProductService = $this->syncWithApi($records);

        return (object)[
            'data' => $responseProductService,
            'paginate' => (object)[
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
                $productServiceResponse = $this->productRepository->get(
                    $this->productSpecification->readService($product->xid)
                );
            } catch (Exception $exception) {
                if ($exception instanceof ScaninaProductNotFoundException) {
                    continue;
                }
                throw $exception;
            }

            $data = (array)$productServiceResponse->data;
            unset($data['reviews']);
            $productServiceResponseDto =  new ReadProductServiceResponseDto($data);
            $productServiceResponseDto->xid = $product->xid;
            $productServiceResponseDto->servicedAt = $product->scheduleDate;
            $productServiceResponseDto->notes = optional($product)->notes;
            $productServiceResponseDto->customerReviews = [];

            $responses[] = $productServiceResponseDto;
        }

        return $responses;
    }
}
