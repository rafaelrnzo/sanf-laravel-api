<?php

namespace Sanf\Core\Modules\Contract\Services;

use Carbon\Carbon;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dto\BrowseESignDocumentDto;
use Sanf\Core\Modules\Contract\Dtos\BrowseProcessFinancingUnitLocationSubmissionByUserRequestDto;
use Sanf\Core\Modules\Contract\Enums\ESignContractStatusEnum;
use Sanf\Core\Modules\User\AuthModel;

final class BrowseESignDocumentService implements ApplicationServiceInterface
{
    protected AuthModel $userRepository;

    public function __construct(AuthModel $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param BrowseProcessFinancingUnitLocationSubmissionByUserRequestDto $dto
     * @return object
     */
    public function execute($dto = null): object
    {
        /** @var BrowseESignDocumentDto $dto */
        $user = $this->userRepository->newQuery()->find($dto->user_id);
        if (!$user) {
            throw new UserNotFoundException();
        }

        // TODO remove this dummy data
        $collection = [
            [
                'xid' => nano_id(),
                'title' => 'Kontrak Pengajuan Pembiayaan No.1234.pdf',
                'status_id' => ESignContractStatusEnum::SUBMIT,
                'file_url' => file_get_temp_url('0CDXQ4eSK85BzjlNQJaMr705YVANM3RQvlkSLjqb.pdf'),
                'expired_at' => Carbon::now()->addDays(4),
                'created_at' => Carbon::now(),
            ],
            [
                'xid' => nano_id(),
                'title' => 'Kontrak Pengajuan Pembiayaan No.76126357.pdf',
                'status_id' => ESignContractStatusEnum::SUBMIT,
                'file_url' => file_get_temp_url('0CDXQ4eSK85BzjlNQJaMr705YVANM3RQvlkSLjqb.pdf'),
                'expired_at' => Carbon::now()->addDays(4),
                'created_at' => Carbon::now(),
            ],
            [
                'xid' => nano_id(),
                'title' => 'Kontrak Pengajuan Pembiayaan No.7871237.pdf',
                'status_id' => ESignContractStatusEnum::SUBMIT,
                'file_url' => file_get_temp_url('0CDXQ4eSK85BzjlNQJaMr705YVANM3RQvlkSLjqb.pdf'),
                'expired_at' => Carbon::now()->addDays(-2),
                'created_at' => Carbon::now(),
            ],
            [
                'xid' => nano_id(),
                'title' => 'Kontrak Pengajuan Pembiayaan No.8961723.pdf',
                'status_id' => ESignContractStatusEnum::SUBMIT,
                'file_url' => file_get_temp_url('0CDXQ4eSK85BzjlNQJaMr705YVANM3RQvlkSLjqb.pdf'),
                'expired_at' => Carbon::now()->addDays(1),
                'created_at' => Carbon::now(),
            ],
            [
                'xid' => nano_id(),
                'title' => 'Kontrak Pengajuan Pembiayaan No.88616.pdf',
                'status_id' => ESignContractStatusEnum::SUBMIT,
                'file_url' => file_get_temp_url('0CDXQ4eSK85BzjlNQJaMr705YVANM3RQvlkSLjqb.pdf'),
                'expired_at' => Carbon::now()->addDays(7),
                'created_at' => Carbon::now(),
            ],
            [
                'xid' => nano_id(),
                'title' => 'Kontrak Pengajuan Pembiayaan No.11188822.pdf',
                'status_id' => ESignContractStatusEnum::ON_PROGRESS,
                'file_url' => file_get_temp_url('0CDXQ4eSK85BzjlNQJaMr705YVANM3RQvlkSLjqb.pdf'),
                'expired_at' => Carbon::now()->addDays(6),
                'created_at' => Carbon::now(),
            ],
            [
                'xid' => nano_id(),
                'title' => 'Kontrak Pengajuan Pembiayaan No.98786123.pdf',
                'status_id' => ESignContractStatusEnum::ON_PROGRESS,
                'file_url' => file_get_temp_url('0CDXQ4eSK85BzjlNQJaMr705YVANM3RQvlkSLjqb.pdf'),
                'expired_at' => Carbon::now()->addDays(1),
                'created_at' => Carbon::now(),
            ],
            [
                'xid' => nano_id(),
                'title' => 'Kontrak Pengajuan Pembiayaan No.123876.pdf',
                'status_id' => ESignContractStatusEnum::ON_PROGRESS,
                'file_url' => file_get_temp_url('0CDXQ4eSK85BzjlNQJaMr705YVANM3RQvlkSLjqb.pdf'),
                'expired_at' => Carbon::now()->addDays(5),
                'created_at' => Carbon::now(),
            ],
            [
                'xid' => nano_id(),
                'title' => 'Kontrak Pengajuan Pembiayaan No.7612364.pdf',
                'status_id' => ESignContractStatusEnum::COMPLETED,
                'file_url' => file_get_temp_url('0CDXQ4eSK85BzjlNQJaMr705YVANM3RQvlkSLjqb.pdf'),
                'expired_at' => Carbon::now()->addDays(5),
                'created_at' => Carbon::now(),
            ],
        ];

        $data = collect($collection)
            ->where('status_id', $dto->status_id)
            ->map(function ($item) {
                return (object)[
                    'xid' => $item['xid'],
                    'title' => $item['title'],
                    'status_id' => $item['status_id'],
                    'file_url' => $item['file_url'],
                    'expired_at' => $item['expired_at'],
                    'created_at' => $item['created_at'],
                ];
            });

        return (object)[
            'data' => $data,
            'paginate' => (object)[
                'total' => $response->total ?? $data->count(),
                'count' => $data->count() ?? 0,
                'skip' => $dto->skip ?? 0,
                'limit' => $dto->limit ?? null,
                'sort_by' => $dto->sortBy ?? '',
            ],
        ];
    }
}
