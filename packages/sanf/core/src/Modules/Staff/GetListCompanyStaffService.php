<?php

namespace Sanf\Core\Modules\Staff;

use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;

class GetListCompanyStaffService extends StaffService implements ApplicationServiceInterface
{
    public function execute($dto = null)
    {
        try {
            $response = $this->internalApiClient->getStaffs($dto->xid);
            $staffs = $response['data'];
        } catch (SanfInternalApiDataNotFoundException $exception) {
            $staffs = [];
        }
        $user = $this->userRepository->findById($dto->userId);
        if (is_null($user)) {
            throw new UserNotFoundException();
        }

        $sodiumQuery = SodiumEncryption::query();

        $activeStaffs = $this->staffRepository->getByCompanyXid($dto->xid);
        $activeStaffCollections = collect($activeStaffs);
        $data = collect($staffs)
            ->map(function ($item) use ($activeStaffCollections, $user, $sodiumQuery) {
                $email = strtolower($item['EMAIL'] ?? '');
                $activeStaff = $activeStaffCollections->filter(function ($activeStaff) use ($email) {
                    return optional($activeStaff->user)->username === $email;
                })->first();
                $registeredUser = $sodiumQuery->transaction(function () use ($email) {
                    return $this->userRepository->findByEmail($email);
                });
                $status = optional($registeredUser)->status;

                return (object) [
                    'no' => $item['SR_NO'] ?? '',
                    'name' => ucwords(strtolower($item['CUST_NAME'] ?? '')),
                    'email' => $email,
                    'status' => $status,
                    'isMe' => $email == $user->username,
                    'isInvited' => !is_null($activeStaff),
                ];
            });

        return (object) [
            'data' => $data,
            'paginate' => (object) [
                'total' => (int) $data->count(),
                'count' => (int) $data->count(),
                'skip' => (int) ($dto->skip ?? null),
                'limit' => (int) ($dto->limit ?? null),
                'sort_by' => $dto->sort_by ?? null,
            ],
        ];
    }
}
