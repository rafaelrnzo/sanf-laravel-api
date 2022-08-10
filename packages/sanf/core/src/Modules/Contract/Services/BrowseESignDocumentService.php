<?php

namespace Sanf\Core\Modules\Contract\Services;

use Carbon\Carbon;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dto\BrowseESignDocumentDto;
use Sanf\Core\Modules\Contract\Dtos\BrowseProcessFinancingUnitLocationSubmissionByUserRequestDto;
use Sanf\Core\Modules\Contract\Enums\ESignContractStatusEnum;
use Sanf\Core\Modules\Contract\Exceptions\ESignUserNotRegisteredException;
use Sanf\Core\Modules\Contract\Specifications\ESignDocumentSpecificationFactoryInterface;
use Sanf\Core\Modules\Contract\Repositories\ESignRepositoryInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\InternalApiClient;

final class BrowseESignDocumentService implements ApplicationServiceInterface
{
    protected AuthModel $userRepository;
    protected ESignRepositoryInterface $eSignRepository;
    protected ESignDocumentSpecificationFactoryInterface $eSignDocumentSpecificationFactory;
    protected InternalApiClient $client;

    public function __construct(
        AuthModel $userRepository,
        ESignRepositoryInterface $eSignRepository,
        ESignDocumentSpecificationFactoryInterface $eSignDocumentSpecificationFactory,
        InternalApiClient $client
    ) {
        $this->userRepository = $userRepository;
        $this->eSignRepository = $eSignRepository;
        $this->eSignDocumentSpecificationFactory = $eSignDocumentSpecificationFactory;
        $this->client = $client;
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
        if (!$userTekenAja) {
            throw new ESignUserNotRegisteredException();
        }

        // get list document from core
        if (!$dto->status_id or $dto->status_id === ESignContractStatusEnum::SUBMITTED) {
//            try {
//                $result = $this->client->browseESignDocument($userTekenAja->email, $dto->keyword);
//            } catch (SanfInternalApiDataNotFoundException $exception) {
//                $result['data'] = [];
//            }

            $string = '{"status":true,"code":"S_GetData","message":"Success","count":5,"data":[{"BR_ID":"201","AGREE_NO":"10707001466","SR_NO":"9","CUST_NAME":"ERAKARYA PRIMA","DOC_ID_TEKENAJA":"96d28273-bd00-4a9d-96f4-b65c6838dbeb","EXPIRATION_DATE":"27/07/2022","FILENAME":"tes multiple 3.pdf"},{"BR_ID":"201","AGREE_NO":"10707001466","SR_NO":"12","CUST_NAME":"ERAKARYA PRIMA","DOC_ID_TEKENAJA":"96de5ae1-b8ad-4433-bd35-14bc21cd0131","EXPIRATION_DATE":"02/08/2022","FILENAME":"single3.pdf"},{"BR_ID":"201","AGREE_NO":"10707001466","SR_NO":"16","CUST_NAME":"ERAKARYA PRIMA","DOC_ID_TEKENAJA":"96eca777-9ab2-4a7d-a52a-edabf846b468","EXPIRATION_DATE":"09/08/2022","FILENAME":"multi2.pdf"},{"BR_ID":"201","AGREE_NO":"10707001466","SR_NO":"8","CUST_NAME":"ERAKARYA PRIMA","DOC_ID_TEKENAJA":"96d257bf-c592-42d5-8c36-ea3070aac38d","EXPIRATION_DATE":"27/07/2022","FILENAME":"tes multiple 2.pdf"},{"BR_ID":"201","AGREE_NO":"10707001466","SR_NO":"3","CUST_NAME":"ERAKARYA PRIMA","DOC_ID_TEKENAJA":"96d04e2a-41de-4de7-8a87-e61a77e1efda","EXPIRATION_DATE":"26/07/2022","FILENAME":"tes1.pdf"}]}';
            $result = json_decode($string, true);

            $mapping = array_map(function ($item) {
                return (object)[
                    'documentName' => $item['FILENAME'] ?? null,
                    'documentId' => $item['DOC_ID_TEKENAJA'] ?? null,
                    'expiredAt' => isset($item['EXPIRATION_DATE']) ? Carbon::createFromFormat('d/m/Y', $item['EXPIRATION_DATE'])->endOfDay() : null,
                    'createdAt' => isset($item['CREATED_AT']) ? Carbon::createFromFormat('d/m/Y', $item['CREATED_AT']) : null,
                ];
            }, $result['data']);

            $query = $this->eSignRepository->documentAssigneeQuery(
                $this->eSignDocumentSpecificationFactory->paginateDocumentAssigneeByUserId($user->id, null, $dto->keyword)
            );
            $total = $this->eSignRepository->documentAssigneeSize(
                $this->eSignDocumentSpecificationFactory->paginateDocumentAssigneeByUserId($user->id, null)
            );

            $data = array_map(function ($item) use ($dto, $user){
                $file = is_string($item->document_file) ? json_decode($item->document_file) : $item->document_file;
                return (object)[
                    'xid' => $item->xid,
                    'documentName' => $item->document_name,
                    'documentId' => $item->document_id,
                    'documentFile' => $file,
                    'statusId' => $item->assignee_status_id,
                    'expiredAt' => Carbon::make($item->expired_at),
                    'createdAt' => Carbon::make($item->created_at),
                    'userId' => $dto->user_id,
                    'email' => $user->username,
                ];
            }, $query);

            // skip same document id from core and sanf db
            $existingDocumentId = array_pluck($data, 'documentId');
            $newDocumentId = array_pluck($mapping, 'documentId');
            $diffDocumentId = array_diff($newDocumentId, $existingDocumentId);

            foreach ($mapping as $newDocument) {
                if (in_array($newDocument->documentId, $diffDocumentId)) {
                    $data[] = (object) [
                        'xid' => null,
                        'documentName' => $newDocument->documentName,
                        'documentId' => $newDocument->documentId,
                        'documentFile' => null,
                        'statusId' => ESignContractStatusEnum::SUBMITTED,
                        'expiredAt' => $newDocument->expiredAt,
                        'createdAt' => $newDocument->createdAt,
                        'userId' => $dto->user_id,
                        'email' => $user->username,
                    ];
                    $total++;
                }
            }

            // filter based on submit status
            if ($dto->status_id === ESignContractStatusEnum::SUBMITTED) {
                $data = array_filter($data, function ($item) {
                    return $item->statusId === ESignContractStatusEnum::SUBMITTED;
                });
            }
        } else {
            $query = $this->eSignRepository->documentAssigneeQuery(
                $this->eSignDocumentSpecificationFactory->paginateDocumentAssigneeByUserId($user->id, $dto->status_id, $dto->keyword)
            );
            $total = $this->eSignRepository->documentAssigneeSize(
                $this->eSignDocumentSpecificationFactory->paginateDocumentAssigneeByUserId($user->id, $dto->status_id)
            );

            $data = array_map(function ($item) use ($dto, $user) {
                $file = is_string($item->document_file) ? json_decode($item->document_file) : $item->document_file;
                return (object)[
                    'xid' => $item->xid,
                    'documentName' => $item->document_name,
                    'documentId' => $item->document_id,
                    'documentFile' => $file,
                    'statusId' => ($item->status_id === ESignContractStatusEnum::ON_PROGRESS && $item->assignee_status_id === ESignContractStatusEnum::DONE) ? ESignContractStatusEnum::ON_PROGRESS : $item->assignee_status_id,
                    'expiredAt' => Carbon::make($item->expired_at),
                    'createdAt' => Carbon::make($item->created_at),
                    'userId' => $dto->user_id,
                    'email' => $user->username,
                ];
            }, $query);
        }

        if ($dto->status_id) {
            $data = array_filter($data, function ($item) use ($dto) {
                return $item->statusId == $dto->status_id;
            });
        }

        $data = array_filter($data, function ($item) {
            if ($item->statusId !== ESignContractStatusEnum::DONE) {
                return $item->expiredAt > Carbon::now();
            }

            return $item;
        });

        return (object)[
            'data' => $data,
            'paginate' => (object)[
                'total' => $total,
                'count' => count($data),
                'skip' => $dto->skip ?? 0,
                'limit' => $dto->limit ?? null,
                'sort_by' => $dto->sortBy ?? '',
            ],
        ];
    }
}
