<?php

namespace Sanf\Core\Modules\StandbyFinancing\Services;

use NbsPhp\Core\Exceptions\ForbiddenException;
use Sanf\Core\Modules\StandbyFinancing\Exceptions\StandbyFinancingValidationException;
use Sanf\Core\Modules\User\Repositories\ProfileRepositoryInterface;

class StandbyFinancingCustomerAccessService
{
    private ProfileRepositoryInterface $profileRepository;

    public function __construct(ProfileRepositoryInterface $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    public function resolveCustomerId($user, ?string $requestedCustomerId = null): string
    {
        if (!empty($requestedCustomerId)) {
            $this->assertCanAccess($user, $requestedCustomerId);

            return $requestedCustomerId;
        }

        $customerId = $this->getUserCustomerId($user);
        if (!empty($customerId)) {
            return $customerId;
        }

        $email = $this->getUserEmail($user);
        if (empty($email)) {
            throw new StandbyFinancingValidationException('Customer id is required.');
        }

        $profiles = $this->profileRepository->findByEmail($email);
        if (empty($profiles)) {
            throw new StandbyFinancingValidationException('Customer profile not found.');
        }

        return $profiles[0]->getCustomerId();
    }

    public function assertCanAccess($user, string $customerId): void
    {
        if ($this->matchesUserCustomerId($user, $customerId)) {
            return;
        }

        $profile = $this->profileRepository->findById($customerId);
        if (is_null($profile)) {
            throw new StandbyFinancingValidationException('Customer profile not found.');
        }

        $userEmail = $this->getUserEmail($user);
        if (empty($userEmail) || $profile->getEmail() !== $userEmail) {
            throw new ForbiddenException('Unauthorized access to profile data');
        }
    }

    public function actor($user): array
    {
        return [
            'id' => isset($user->id) ? (string) $user->id : null,
            'name' => $this->getUserEmail($user),
        ];
    }

    private function getUserEmail($user): ?string
    {
        return $user->username ?? $user->email ?? null;
    }

    private function matchesUserCustomerId($user, string $customerId): bool
    {
        return $this->getUserCustomerId($user) === $customerId;
    }

    private function getUserCustomerId($user): ?string
    {
        foreach (['customer_id', 'cust_id', 'xid'] as $field) {
            if (!empty($user->{$field})) {
                return (string) $user->{$field};
            }
        }

        return null;
    }
}
