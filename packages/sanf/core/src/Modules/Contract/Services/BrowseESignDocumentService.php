<?php

namespace Sanf\Core\Modules\Contract\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dto\BrowseESignDocumentDto;
use Sanf\Core\Modules\Contract\Dtos\BrowseProcessFinancingUnitLocationSubmissionByUserRequestDto;
use Sanf\Core\Modules\Contract\Enums\ESignContractStatusEnum;
use Sanf\Core\Modules\Contract\Repositories\ESignRepositoryInterface;
use Sanf\Core\Modules\Contract\Specifications\ESignDocumentSpecificationFactoryInterface;
use Sanf\Core\Modules\Contract\Support\ESignHelper;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\Modules\SanfCore\Entities\SanfCoreESignDocumentCategoryEntity;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

final class BrowseESignDocumentService implements ApplicationServiceInterface
{
    protected UserRepositoryInterface $userRepository;
    protected ESignRepositoryInterface $eSignRepository;
    protected ESignDocumentSpecificationFactoryInterface $eSignDocumentSpecificationFactory;
    protected SanfCoreApiClient $client;
    private array $categories = [];

    public function __construct(
        UserRepositoryInterface $userRepository,
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
        $user = $this->userRepository->findById($dto->user_id);
        if (!$user) {
            throw new UserNotFoundException();
        }
        if (!$dto->status_id or $dto->status_id === ESignContractStatusEnum::SUBMITTED) {
            $result = [
                'data' => [],
            ];
            try {
                $result = $this->client->browseESignDocument($user->username, $dto->keyword);
            } catch (SanfInternalApiDataNotFoundException $exception) {
                $result['data'] = [];
            }

            $mapping = array_map(function ($item) {
                return (object) [
                    'documentName' => $item['FILE_NAME'] ?? null,
                    'categoryId' => $item['DOC_ID'] ?? null,
                    'categoryDesc' => $item['DOC_DESC'] ?? null,
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

            $existingNonSubmitDocumentId = [];
            $data = array_map(function ($item) use ($dto, $user, &$existingNonSubmitDocumentId) {
                $file = is_string($item->e_sign_document->document_file) ? json_decode($item->e_sign_document->document_file) : $item->e_sign_document->document_file;

                if ($item->assignee_status_id !== ESignContractStatusEnum::SUBMITTED) {
                    $existingNonSubmitDocumentId[] = $item->document_id;
                }

                return (object) [
                    'xid' => $item->xid,
                    'documentName' => $item->e_sign_document->document_name,
                    'documentId' => $item->document_id,
                    'referenceNo' => $item->e_sign_document->reference_no,
                    'documentFile' => $file,
                    'statusId' => $item->assignee_status_id,
                    'expiredAt' => Carbon::make($item->e_sign_document->expired_at),
                    'createdAt' => Carbon::make($item->created_at),
                    'categoryId' => $item->e_sign_document->category_id,
                    'categoryDesc' => $this->getCategoryName($item->e_sign_document->category_id),
                    'userId' => $dto->user_id,
                    'email' => $user->username,
                ];
            }, $query);

            // skip same document id from core and sanf db
            $existingDocumentId = array_pluck($data, 'documentId');
            $newDocumentId = array_pluck($mapping, 'documentId');

            $data = array_filter($data, function ($item) use ($newDocumentId) {
                return in_array($item->documentId, $newDocumentId) && $item->statusId !== ESignContractStatusEnum::SUBMITTED;
            });

            foreach ($mapping as $newDocument) {
                if (in_array($newDocument->documentId, $existingNonSubmitDocumentId) === true) {
                    continue;
                }

                $cacheKey = ESignHelper::checkSignStatusCacheKey($dto->user_id, $newDocument->documentId);
                $cachedRetryAt = Cache::get($cacheKey);

                $data[] = (object) [
                    'xid' => null,
                    'documentName' => $newDocument->documentName,
                    'documentId' => $newDocument->documentId,
                    'referenceNo' => $newDocument->referenceNo,
                    'documentFile' => null,
                    'expiredAt' => $newDocument->expiredAt,
                    'statusId' => ESignContractStatusEnum::SUBMITTED,
                    'createdAt' => $newDocument->createdAt,
                    'categoryId' => $newDocument->categoryId,
                    'categoryDesc' => $newDocument->categoryDesc,
                    'userId' => $dto->user_id,
                    'email' => $user->username,
                    'checkStatusAvailableAt' => $cachedRetryAt,
                ];
                $total++;
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
                $file = is_string($item->e_sign_document->document_file) ? json_decode($item->e_sign_document->document_file) : $item->e_sign_document->document_file;

                $cacheKey = ESignHelper::checkSignStatusCacheKey($dto->user_id, $item->document_id);
                $cachedRetryAt = Cache::get($cacheKey);

                return (object) [
                    'xid' => $item->xid,
                    'documentName' => $item->e_sign_document->document_name,
                    'documentId' => $item->document_id,
                    'referenceNo' => $item->e_sign_document->reference_no,
                    'documentFile' => $file,
                    'statusId' => ($item->e_sign_document->status_id === ESignContractStatusEnum::ON_PROGRESS && $item->assignee_status_id === ESignContractStatusEnum::DONE) ? ESignContractStatusEnum::ON_PROGRESS : $item->assignee_status_id,
                    'expiredAt' => Carbon::make($item->e_sign_document->expired_at),
                    'createdAt' => Carbon::make($item->created_at),
                    'categoryId' => $item->e_sign_document->category_id,
                    'categoryDesc' => $this->getCategoryName($item->e_sign_document->category_id),
                    'userId' => $dto->user_id,
                    'email' => $user->username,
                    'checkStatusAvailableAt' => $cachedRetryAt,
                ];
            }, $query);
        }

        if ($dto->status_id) {
            $data = array_filter($data, function ($item) use ($dto) {
                return $item->statusId == $dto->status_id;
            });
        }

        $data = array_filter($data, function ($item) {
            if ($item->statusId !== ESignContractStatusEnum::DONE
                && $item->statusId !== ESignContractStatusEnum::FAILED) {
                return $item->expiredAt > Carbon::now();
            }

            return empty($item->documentId) === false && $item->documentId !== ' '
                && empty($item->referenceNo) === false && $item->referenceNo !== ' ';
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

    private function getCategoryName(?string $id)
    {
        if ($id === null) {
            return null;
        }

        if (empty($this->categories)) {
            $response = $this->client->getESignDocumentCategoryList();

            /**
             * @var SanfCoreESignDocumentCategoryEntity[]
             */
            $data = $response['data'];

            $this->categories = array_pluck($data, 'DOC_DESC', 'DOC_ID');
        }

        return $this->categories[$id] ?? null;
    }
}
