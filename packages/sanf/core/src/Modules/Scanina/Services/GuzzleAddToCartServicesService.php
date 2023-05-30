<?php

namespace Sanf\Core\Modules\Scanina\Services;

use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Models\AuthModel;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Api\Modules\Scanina\Events\ProductServiceAddToCartEvent;
use Sanf\Core\Modules\Scanina\Dtos\AddToCartRequestDto;
use Sanf\Core\Modules\Scanina\Dtos\ReadProductServiceResponseDto;
use Sanf\Core\Modules\Scanina\Enums\ScaninaProductTypeEnum;
use Sanf\Core\Modules\Scanina\Repositories\ProductCartRepositoryInterface;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaProductRepositoryInterface;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaUserRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaProductSpecificationInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaUserSpecificationInterface;
use Sanf\Core\Modules\User\Repositories\ProfileRepositoryInterface;

class GuzzleAddToCartServicesService implements ApplicationServiceInterface
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

        $productServiceResponse = $this->getProduct($dto->productXid);

        $requestBodyDto = new AddToCartRequestDto([
            'email' => $profile->getEmail(),
            'typeId' => ScaninaProductTypeEnum::BUY,
            'serviceDate' => $dto->servicedAt,
            'note' => $dto->notes,
            'productId' => $dto->productXid,
        ]);

        $this->repository->post(
            $this->specification->addToCart($requestBodyDto)
        );

        $cart = $this->productCartRepository->create([
            'xid' => nano_id(),
            'profile_xid' => $dto->xid,
            'type_id' => ScaninaProductTypeEnum::SERVICE,
            'snapshot_request_body' => $requestBodyDto,
            'snapshot_response_body' => $productServiceResponse,
        ]);

        $productServiceResponse->createdAt = $cart->created_at->timestamp;

        event(new ProductServiceAddToCartEvent($productServiceResponse, $profile));

        return true;
    }

    /**
     * @param string $xid
     * @return ReadProductServiceResponseDto
     */
    private function getProduct(string $xid): ReadProductServiceResponseDto
    {
        $productServiceResponse = $this->productRepository->get(
            $this->productSpecification->readService($xid)
        );

        $data = (array)$productServiceResponse->data;
        unset($data['review']);
        $productServiceResponseDto = new ReadProductServiceResponseDto((array)$productServiceResponse->data);
        $productServiceResponseDto->customerReviews = [];

        return $productServiceResponseDto;
    }
}
