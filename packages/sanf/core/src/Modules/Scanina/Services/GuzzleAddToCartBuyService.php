<?php

namespace Sanf\Core\Modules\Scanina\Services;

use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Models\AuthModel;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Api\Modules\Scanina\Events\ProductBuyAddToCartEvent;
use Sanf\Core\Modules\Scanina\Dtos\AddToCartRequestDto;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductSpecificationResponseDto;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductSubSpecificationResponseDto;
use Sanf\Core\Modules\Scanina\Dtos\ReadProductBuyResponseDto;
use Sanf\Core\Modules\Scanina\Enums\ScaninaProductTypeEnum;
use Sanf\Core\Modules\Scanina\Repositories\ProductCartRepositoryInterface;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaProductRepositoryInterface;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaUserRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaProductSpecificationInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaUserSpecificationInterface;
use Sanf\Core\Modules\User\Repositories\ProfileRepositoryInterface;

class GuzzleAddToCartBuyService implements ApplicationServiceInterface
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

        $productBuyResponse = $this->getProduct($dto->productXid);

        $requestBodyDto = new AddToCartRequestDto([
            'email' => $profile->getEmail(),
            'type' => ScaninaProductTypeEnum::BUY,
            'productXid' => $dto->productXid,
        ]);

        $this->repository->post(
            $this->specification->addToCart($requestBodyDto)
        );

        $cart = $this->productCartRepository->create([
            'xid' => nano_id(),
            'profile_xid' => $dto->xid,
            'type_id' => ScaninaProductTypeEnum::BUY,
            'snapshot_request_body' => $requestBodyDto,
            'snapshot_response_body' => $productBuyResponse,
        ]);

        $productBuyResponse->createdAt = $cart->created_at->timestamp;

        event(new ProductBuyAddToCartEvent($productBuyResponse, $profile));

        return true;
    }

    /**
     * @param string $xid
     * @return ReadProductBuyResponseDto
     */
    private function getProduct(string $xid): ReadProductBuyResponseDto
    {
        $productBuyResponse = $this->productRepository->get(
            $this->productSpecification->readBuy($xid)
        );

        $data = (array)$productBuyResponse->data;
        unset($data['review']);
        $productBuyResponseDto = new ReadProductBuyResponseDto($data);

        $productBuySpecificationResponse = $this->productRepository->get(
            $this->productSpecification->getSpecification($xid, ScaninaProductTypeEnum::BUY)
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
        $productBuyResponseDto->subSpecifications = $subSpecifications;

        return $productBuyResponseDto;
    }
}
