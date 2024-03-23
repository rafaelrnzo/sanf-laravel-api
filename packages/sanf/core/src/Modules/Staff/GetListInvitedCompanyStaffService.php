<?php

namespace Sanf\Core\Modules\Staff;

use NbsPhp\Core\Enum\UserStatus;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;

class GetListInvitedCompanyStaffService extends StaffService implements ApplicationServiceInterface
{
    public function execute($dto = null)
    {
        try {
            $response = $this->internalApiClient->getStaffs($dto->xid);
            $staffs = $response['data'];
        } catch (SanfInternalApiDataNotFoundException $exception) {
            $staffs = [];
        }

        $activeStaffs = $this->staffRepository->getByCompanyXid($dto->xid);
        $activeStaffCollections = collect($activeStaffs);
        $data = collect($staffs)
            ->map(function ($item) use ($activeStaffCollections) {
                $email = strtolower($item['EMAIL'] ?? '');
                $activeStaff = $activeStaffCollections->filter(function ($activeStaff) use ($email) {
                    return optional($activeStaff->user)->username === $email;
                })->first();
                $status = optional(optional($activeStaff)->user)->status;

                return (object) [
                    'no' => $item['SR_NO'] ?? '',
                    'name' => ucwords(strtolower($item['CUST_NAME'] ?? '')),
                    'email' => $email,
                    'status' => $status,
                ];
            })->filter(function ($item) {
                return !is_null($item->status) && $item->status->id != UserStatus::NEED_ACTIVATION;
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
