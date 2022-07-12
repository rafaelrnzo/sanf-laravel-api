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

        $userTekenAja = $this->eSignRepository->findUserByUserId($user->id);

        $query = $this->eSignRepository->documentQuery(
            $this->eSignDocumentSpecificationFactory->paginateByUserId($user->id, $dto->status_id, $dto->keyword)
        );
        $total = $this->eSignRepository->documentSize(
            $this->eSignDocumentSpecificationFactory->paginateByUserId($user->id, $dto->status_id)
        );

        $data = array_map(function ($item) {
            return (object)[
                'xid' => $item->xid,
                'documentName' => $item->document_name,
                'documentId' => $item->document_id,
                'documentFile' => $item->document_file ?? null,
                'statusId' => $item->status_id,
                'expiredAt' => Carbon::make($item->expired_at),
                'createdAt' => Carbon::make($item->created_at),
            ];
        }, $query);

        // get list document from core
        if (!$dto->status_id OR $dto->status_id === ESignContractStatusEnum::SUBMIT) {
            $userTekenAja->email = 'anton@sanf.co.id'; // TODO remove this
            try {
                $result = $this->client->browseESignDocument($userTekenAja->email, $dto->keyword);
            } catch (SanfInternalApiDataNotFoundException $exception) {
                $result['data'] = [];
            }

            $mapping = array_map(function ($item) {
                return (object)[
                    'documentName' => $item['NAME'] ?? null,
                    'documentId' => $item['DOC_ID_TEKENAJA'] ?? null,
                    'expiredAt' => isset($item['EXPIRED_AT']) ? Carbon::createFromFormat('Y-m-d', $item['EXPIRED_AT']) : null,
                    'createdAt' => isset($item['CREATED_AT']) ? Carbon::createFromFormat('Y-m-d', $item['CREATED_AT']) : null,
                ];
            }, $result['data']);

            // skip same document id from core and sanf db

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
