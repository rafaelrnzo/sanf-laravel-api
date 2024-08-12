<?php

namespace Sanf\Core\Modules\Contract\Services;

use Carbon\Carbon;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dto\BrowseESignDocumentDto;
use Sanf\Core\Modules\Contract\Dtos\BrowseProcessFinancingUnitLocationSubmissionByUserRequestDto;
use Sanf\Core\Modules\Contract\Enums\ESignContractStatusEnum;
use Sanf\Core\Modules\Contract\Exceptions\ESignUserNotRegisteredException;
use Sanf\Core\Modules\Contract\Repositories\ESignRepositoryInterface;
use Sanf\Core\Modules\Contract\Specifications\ESignDocumentSpecificationFactoryInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

final class BrowseESignDocumentService implements ApplicationServiceInterface
{
    protected AuthModel $userRepository;
    protected ESignRepositoryInterface $eSignRepository;
    protected ESignDocumentSpecificationFactoryInterface $eSignDocumentSpecificationFactory;
    protected SanfCoreApiClient $client;

    public function __construct(
        AuthModel $userRepository,
        ESignRepositoryInterface $eSignRepository,
        ESignDocumentSpecificationFactoryInterface $eSignDocumentSpecificationFactory,
        SanfCoreApiClient $client
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

        // $userTekenAja = $this->eSignRepository->findUserByUserId($user->id);
        // if (!$userTekenAja) {
        //     throw new ESignUserNotRegisteredException();
        // }

        // get list document from core
        if (!$dto->status_id or $dto->status_id === ESignContractStatusEnum::SUBMITTED) {
            $result['data'] = [];
            try {
                $result = $this->client->browseESignDocument($user->username, $dto->keyword);
            } catch (SanfInternalApiDataNotFoundException $exception) {
                $result['data'] = [];
            }

            $mapping = array_map(function ($item) {
                return (object) [
                    'documentName' => $item['FILENAME'] ?? null,
                    'documentId' => $item['DOC_ID_TEKENAJA'] ?? null,
                    'referenceNo' => $item['REFERENCE_NO'] ?? null,
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

            $data = array_map(function ($item) use ($dto, $user) {
                $file = is_string($item->document_file) ? json_decode($item->document_file) : $item->document_file;

                return (object) [
                    'xid' => $item->xid,
                    'documentName' => $item->document_name,
                    'documentId' => $item->document_id,
                    'referenceNo' => $item->reference_no,
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
                        'referenceNo' => $newDocument->referenceNo,
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

                return (object) [
                    'xid' => $item->xid,
                    'documentName' => $item->document_name,
                    'documentId' => $item->document_id,
                    'referenceNo' => $item->reference_no,
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

        $data = array_filter($data, function ($item) {
            return empty($item->documentId) === false && $item->documentId !== ' ';
        });
        $data = array_filter($data, function ($item) {
            return empty($item->referenceNo) === false && $item->referenceNo !== ' ';
        });

        return (object) [
            'data' => $data,
            'paginate' => (object) [
                'total' => $total,
                'count' => count($data),
                'skip' => $dto->skip ?? 0,
                'limit' => $dto->limit ?? null,
                'sort_by' => $dto->sortBy ?? '',
            ],
        ];
    }
}
