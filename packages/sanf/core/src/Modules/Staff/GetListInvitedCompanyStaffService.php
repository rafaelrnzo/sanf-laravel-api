<?php


namespace Sanf\Core\Modules\Staff;


use NbsPhp\Core\Enum\UserStatus;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\InternalApiClient;

class GetListInvitedCompanyStaffService extends StaffService implements ApplicationServiceInterface
{
    protected InternalApiClient $internalApiClient;

    protected AuthModel $userRepository;

    /**
     * GetListInvitedCompanyStaffService constructor.
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
        try{
            $response = $this->internalApiClient->getStaffs($dto->xid);
            $staffs = $response['data'];
        } catch (SanfInternalApiDataNotFoundException $exception){
            $staffs = [];
        }

        $data = collect($staffs)
            ->map(function ($item) {
                $user = $this->userRepository->whereNotNull('personal_xid')
                    ->with('status')
                    ->where('personal_xid', $item['CUST_ID'])
                    ->first();
                return (object)[
                    "no" => $item['SR_NO'] ?? '',
                    "name" => ucwords(strtolower($item['CUST_NAME'] ?? '')),
                    "email" => ucwords(strtolower($item['EMAIL'] ?? '')),
                    "status" => optional($user)->status,
                ];
            })->filter(function ($item){
                return !is_null($item->status) && $item->status->id != UserStatus::SUSPENDED;
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
