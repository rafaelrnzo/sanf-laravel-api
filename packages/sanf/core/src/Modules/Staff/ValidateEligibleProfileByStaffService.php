<?php

namespace Sanf\Core\Modules\Staff;

use NbsPhp\Core\Services\ApplicationServiceInterface;

class ValidateEligibleProfileByStaffService extends StaffService implements ApplicationServiceInterface
{
    public function execute($dto = null) //customerProfiles, userId
    {
        $activeStaffs = $this->staffRepository->getByUserId($dto->userId);
        $activeStaffCollections = collect($activeStaffs);
        $data = collect($dto->customerProfiles)
            ->map(function ($item) use ($activeStaffCollections) {
                if ($item->isPic) {
                    $item->isEligible = true;

                    return $item;
                }
                $staffCompany = $activeStaffCollections->firstWhere('company_xid', $item->xid);
                if (!is_null($staffCompany)) {
                    $item->isEligible = true;

                    return $item;
                }
                $item->isEligible = false;

                return $item;
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
