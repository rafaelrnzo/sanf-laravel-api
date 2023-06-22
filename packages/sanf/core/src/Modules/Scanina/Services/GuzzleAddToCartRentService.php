<?php

namespace Sanf\Core\Modules\Scanina\Services;

use Carbon\Carbon;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Models\AuthModel;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Api\Modules\Scanina\Events\ProductRentAddToCartEvent;
use Sanf\Core\Modules\Scanina\Dtos\AddToCartRequestDto;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductSpecificationResponseDto;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductSubSpecificationResponseDto;
use Sanf\Core\Modules\Scanina\Dtos\ReadProductRentResponseDto;
use Sanf\Core\Modules\Scanina\Enums\ScaninaProductTypeEnum;
use Sanf\Core\Modules\Scanina\Exceptions\ScaninaProductInvalidRequestException;
use Sanf\Core\Modules\Scanina\Repositories\ProductCartRepositoryInterface;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaProductRepositoryInterface;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaUserRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaProductSpecificationInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaUserSpecificationInterface;
use Sanf\Core\Modules\User\Repositories\ProfileRepositoryInterface;

class GuzzleAddToCartRentService implements ApplicationServiceInterface
{
    private ScaninaUserRepositoryInterface $repository;
    private ScaninaUserSpecificationInterface $specification;
    private AuthModel $userRepository;
    private ProfileRepositoryInterface $profileRepository;
    private ScaninaProductRepositoryInterface $productRepository;
    private ScaninaProductSpecificationInterface $productSpecification;
    private ProductCartRepositoryInterface $productCartRepository;

    public function __construct(
        AuthModel $userRepository,
        ProfileRepositoryInterface $profileRepository,
        ScaninaUserRepositoryInterface $repository,
        ScaninaUserSpecificationInterface $specification,
        ScaninaProductRepositoryInterface $productRepository,
        ScaninaProductSpecificationInterface $productSpecification,
        ProductCartRepositoryInterface $productCartRepository
    ) {
        $this->repository = $repository;
        $this->specification = $specification;
        $this->userRepository = $userRepository;
        $this->profileRepository = $profileRepository;
        $this->productRepository = $productRepository;
        $this->productSpecification = $productSpecification;
        $this->productCartRepository = $productCartRepository;
    }

    public function execute($dto = null)
    {
        $user = $this->userRepository->findOrFail($dto->userId);
        if (!$user) {
            throw new UserNotFoundException();
        }

        $profile = $this->profileRepository->findById($dto->xid);
        if (is_null($profile)) {
            throw new UserNotFoundException('');
        }

        $productRentResponse = $this->getProduct($dto->productXid);

        if (is_null($productRentResponse->stock) || $productRentResponse->stock === 0) {
            throw new ScaninaProductInvalidRequestException("Product out of stock");
        }

        // TODO using UTC format
        $startedAt = Carbon::parse($dto->startedAt / 1000)
            ->timezone('Asia/Jakarta')
            ->format('Y-m-d');
        $endedAt = Carbon::parse($dto->endedAt / 1000)
            ->timezone('Asia/Jakarta')
            ->format('Y-m-d');

        if ($startedAt > $endedAt) {
            throw new ScaninaProductInvalidRequestException("Invalid request date");
        }
        if ($startedAt < $productRentResponse->rentStartDate || $endedAt > $productRentResponse->rentEndDate) {
            throw new ScaninaProductInvalidRequestException("Invalid request date");
        }

        if ($startedAt > $productRentResponse->rentEndDate || $endedAt < $productRentResponse->rentStartDate) {
            throw new ScaninaProductInvalidRequestException("Invalid request date");
        }

        $requestBodyDto = new AddToCartRequestDto([
            'email' => $profile->getEmail(),
            'type' => ScaninaProductTypeEnum::RENT,
            'productXid' => $dto->productXid,
            'rentStartDate' => $dto->startedAt / 1000,
            'rentEndDate' => $dto->endedAt / 1000,
        ]);

        $this->repository->post(
            $this->specification->addToCart($requestBodyDto)
        );

        $cart = $this->productCartRepository->create([
            'xid' => nano_id(),
            'profile_xid' => $dto->xid,
            'type_id' => ScaninaProductTypeEnum::RENT,
            'snapshot_request_body' => $requestBodyDto,
            'snapshot_response_body' => $productRentResponse,
        ]);

        $productRentResponse->createdAt = $cart->created_at->timestamp;
        $productRentResponse->startDateAvailable = $dto->startedAt;
        $productRentResponse->endDateAvailable = $dto->endedAt;

        event(new ProductRentAddToCartEvent($productRentResponse, $profile));

        return true;
    }

    /**
     * @param string $xid
     * @return ReadProductRentResponseDto
     */
    private function getProduct(string $xid): ReadProductRentResponseDto
    {
        $productRentResponse = $this->productRepository->get(
            $this->productSpecification->readRent($xid)
        );

        $data = (array)$productRentResponse->data;
        unset($data['review']);
        $productRentResponseDto = new ReadProductRentResponseDto($data);

        $productBuySpecificationResponse = $this->productRepository->get(
            $this->productSpecification->getSpecification($xid, ScaninaProductTypeEnum::RENT)
        );

        $specifications = array_map(function ($specification) {
            $specification->subSpecification = array_map(function ($subSpecification) {
                return new BrowseProductSubSpecificationResponseDto((array)$subSpecification);
            }, $specification->subSpecification);

            return new BrowseProductSpecificationResponseDto((array)$specification);
        }, $productBuySpecificationResponse->data->rows);

        $subSpecifications = [];
        foreach ($specifications as $specification) {
            foreach ($specification->subSpecification as $subSpecification) {
                $subSpecifications[] = $subSpecification;
            }
        }
        $productRentResponseDto->subSpecifications = $subSpecifications;

        return $productRentResponseDto;
    }
}
