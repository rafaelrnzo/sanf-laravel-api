<?php

namespace Sanf\Core\Modules\Scanina\Services;

use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Api\Modules\Scanina\Events\ProductSparePartAddToCartEvent;
use Sanf\Core\Modules\Scanina\Dtos\AddToCartRequestDto;
use Sanf\Core\Modules\Scanina\Dtos\ReadProductSparePartResponseDto;
use Sanf\Core\Modules\Scanina\Enums\ScaninaProductTypeEnum;
use Sanf\Core\Modules\Scanina\Exceptions\ScaninaProductInvalidRequestException;
use Sanf\Core\Modules\Scanina\Repositories\ProductCartRepositoryInterface;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaProductRepositoryInterface;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaUserRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaProductSpecificationInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaUserSpecificationInterface;
use Sanf\Core\Modules\User\Repositories\ProfileRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

class GuzzleAddToCartSparePartService implements ApplicationServiceInterface
{
    private ScaninaUserRepositoryInterface $repository;
    private ScaninaUserSpecificationInterface $specification;
    private UserRepositoryInterface $userRepository;
    private ProfileRepositoryInterface $profileRepository;
    private ScaninaProductRepositoryInterface $productRepository;
    private ScaninaProductSpecificationInterface $productSpecification;
    private ProductCartRepositoryInterface $productCartRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
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
        $user = $this->userRepository->findById($dto->userId);
        if (!$user) {
            throw new UserNotFoundException();
        }

        $profile = $this->profileRepository->findById($dto->xid);
        if (is_null($profile)) {
            throw new UserNotFoundException('');
        }

        $productSparePartResponse = $this->getProduct($dto->productXid);

        if (is_null($productSparePartResponse->stock) || $productSparePartResponse->stock === 0 || $productSparePartResponse->stock < $dto->quantity) {
            throw new ScaninaProductInvalidRequestException('Product out of stock');
        }

        $requestBodyDto = new AddToCartRequestDto([
            'email' => $profile->getEmail(),
            'type' => ScaninaProductTypeEnum::SPARE_PART,
            'productXid' => $dto->productXid,
            'quantity' => $dto->quantity,
        ]);

        $this->repository->post(
            $this->specification->addToCart($requestBodyDto)
        );

        $cart = $this->productCartRepository->create([
            'xid' => nano_id(),
            'profile_xid' => $dto->xid,
            'type_id' => ScaninaProductTypeEnum::SPARE_PART,
            'snapshot_request_body' => $requestBodyDto,
            'snapshot_response_body' => $productSparePartResponse,
        ]);

        $productSparePartResponse->createdAt = $cart->created_at->timestamp;
        $productSparePartResponse->quantity = $dto->quantity;

        event(new ProductSparePartAddToCartEvent($productSparePartResponse, $profile));

        return true;
    }

    /**
     * @param string $xid
     * @return ReadProductSparePartResponseDto
     */
    private function getProduct(string $xid): ReadProductSparePartResponseDto
    {
        $productSparePartResponse = $this->productRepository->get(
            $this->productSpecification->readSparePart($xid)
        );

        $data = (array) $productSparePartResponse->data;
        unset($data['review']);
        $productSparePartResponseDto = new ReadProductSparePartResponseDto((array) $productSparePartResponse->data);
        $productSparePartResponseDto->customerReviews = [];

        return $productSparePartResponseDto;
    }
}
