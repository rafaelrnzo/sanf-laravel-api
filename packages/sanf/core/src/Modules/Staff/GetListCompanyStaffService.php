<?php


namespace Sanf\Core\Modules\Staff;


use NbsPhp\Core\Enum\UserStatus;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Integration\InternalApiClient;

class GetListCompanyStaffService extends StaffService implements ApplicationServiceInterface
{
    protected InternalApiClient $internalApiClient;

    protected AuthModel $userRepository;

    /**
     * GetListCompanyStaffService constructor.
     * @param InternalApiClient $internalApiClient
     * @param AuthModel $userRepository
     */
    public function __construct(InternalApiClient $internalApiClient, AuthModel $userRepository)
    {
        $this->internalApiClient = $internalApiClient;
        $this->userRepository = $userRepository;
    }

    public function execute($dto = null)
    {
        $response = $this->internalApiClient->getStaffs($dto->xid);

        $data = collect($response['data'])
            ->map(function ($item) {
                $user = $this->userRepository->whereNotNull('personal_xid')
                    ->with('status')
                    ->where('personal_xid', $item['CUST_ID'])
                    ->first();
                $status = optional($user)->status;
                if (optional($status)->id == UserStatus::SUSPENDED) {
                    $status = null;
                }
                return (object)[
                    "no" => $item['SR_NO'] ?? '',
                    "name" => ucwords(strtolower($item['CUST_NAME'] ?? '')),
                    "email" => ucwords(strtolower($item['EMAIL'] ?? '')),
                    "status" => $status,
                ];
            });

        return (object)[
            'data' => $data,
            'paginate' => (object)[
                'total' => (int)$data->count(),
                'count' => (int)$data->count(),
                'skip' => (int)($dto->skip ?? null),
                'limit' => (int)($dto->limit ?? null),
                'sort_by' => $dto->sort_by ?? null,
            ],
        ];
    }
}
