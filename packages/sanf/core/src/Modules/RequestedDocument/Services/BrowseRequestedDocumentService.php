<?php

namespace Sanf\Core\Modules\RequestedDocument\Services;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Container\BindingResolutionException;
use NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\RequestedDocument\Dtos\ListRequestedDocumentDto;
use Sanf\Core\Modules\RequestedDocument\Enums\RequestedDocumentStatusEnum;
use Sanf\Core\Modules\RequestedDocument\Repositories\RequestedDocumentRepositoryInterface;
use Sanf\Core\Modules\User\AuthModel;

class BrowseRequestedDocumentService implements ApplicationServiceInterface
{
    private AuthModel $userRepository;
    private BrowseRequestedDocumentFromCoreService $coreService;
    private BrowseRequestedDocumentFromDbService $dbService;
    private RequestedDocumentRepositoryInterface $requestedDocumentEloquentRepository;

    public function __construct(
        AuthModel $userRepository,
        BrowseRequestedDocumentFromCoreService $coreService,
        BrowseRequestedDocumentFromDbService $dbService,
        RequestedDocumentRepositoryInterface $requestedDocumentEloquentRepository
    ) {
        $this->userRepository = $userRepository;
        $this->coreService = $coreService;
        $this->dbService = $dbService;
        $this->requestedDocumentEloquentRepository = $requestedDocumentEloquentRepository;
    }

    /**
     * @param ListRequestedDocumentDto $dto
     * @return object
     * @throws BindingResolutionException
     * @throws UserNotFoundException
     * @throws GuzzleException
     * @throws EndpointNotDefinedException
     */
    public function execute($dto = null)
    {
        $this->getUser($dto);

        $dataFromCore = null;
        if ($dto->status === RequestedDocumentStatusEnum::REQUESTED) {
            $dataFromCore = $this->coreService->execute($dto);
            $this->emptyPage($dto, $dataFromCore->data);
        }

        $dataFromDb = $this->dbService->execute($dto);

        $data = null;
        if ($dto->status === RequestedDocumentStatusEnum::SUBMITTED) {
            $data = $dataFromDb->data;
        }

        if ($dto->status === RequestedDocumentStatusEnum::REQUESTED) {
            $data = array_map(function ($requestedDocumentCore) use ($dto, $dataFromDb) {
                $requestedDocumentDb = $this->getExistingRequestedDocument($dataFromDb, $requestedDocumentCore);
                $requestedDocumentDb = $this->storeIfDoesntExist(
                    $dto->user_id,
                    $requestedDocumentCore,
                    $requestedDocumentDb
                );

                $requestedDocumentCore->total_uploaded_document = $requestedDocumentDb->total_uploaded;
                $requestedDocumentCore->documents = $this->getExistingRequestedDocumentItem(
                    $requestedDocumentCore->documents,
                    $requestedDocumentDb->documents
                );

                return $requestedDocumentCore;
            }, $dataFromCore->data ?? []);
        }

        $this->emptyPage($dto, $data);

        return (object)[
            'data' => $data,
            'paginate' => (object)[
                'total' => $dataFromCore->total ?? $dataFromDb->total,
                'count' => count($data) ?? 0,
                'skip' => $dto->skip,
                'limit' => $dto->limit,
                'sort_by' => $dto->sort_by,
            ],
        ];
    }

    private function getUser(?ListRequestedDocumentDto $dto): void
    {
        $user = $this->userRepository->newQuery()->find($dto->user_id);
        if (!$user) {
            throw new UserNotFoundException();
        }
    }

    /**
     * @param ListRequestedDocumentDto $dto
     * @param ?array $data
     * @return void | object
     */
    private function emptyPage(ListRequestedDocumentDto $dto, ?array $data)
    {
        if (!$data) {
            return;
        }

        return (object)[
            'data' => [],
            'paginate' => (object)[
                'total' => 0,
                'count' => 0,
                'skip' => (int)$dto->skip,
                'limit' => (int)$dto->limit,
                'sortBy' => $dto->sort_by,
            ]
        ];
    }

    /**
     * @param object $dataFromDb
     * @param $requestedDocument
     * @return object|null
     */
    private function getExistingRequestedDocument(object $dataFromDb, $requestedDocument): ?object
    {
        $requestedDocumentDb = null;
        foreach ($dataFromDb->data as $model) {
            if ($model->request_no !== $requestedDocument->request_no) {
                continue;
            }
            $requestedDocumentDb = $model;
        }
        return $requestedDocumentDb;
    }

    /**
     * @param object $requestedDocument
     * @param int $userId
     * @param object|null $requestedDocumentDb
     * @return object
     * @throws BindingResolutionException
     */
    private function storeIfDoesntExist(int $userId, object $requestedDocument, ?object $requestedDocumentDb): object
    {
        if ($requestedDocumentDb) {
            return $requestedDocumentDb;
        }

        return $this->requestedDocumentEloquentRepository->create([
            'xid' => nano_id(),
            'user_id' => $userId,
            'request_no' => $requestedDocument->request_no,
            'request_at' => $requestedDocument->request_at,
            'document_no' => $requestedDocument->document_no,
            'type' => $requestedDocument->document_type,
            'total_item' => $requestedDocument->total_document,
            'total_uploaded' => 0,
            'status' => RequestedDocumentStatusEnum::REQUESTED,
        ]);
    }

    private function getExistingRequestedDocumentItem(
        array $requestedDocumentItemCore,
        array $requestedDocumentItemDb
    ): array {
        return array_map(function ($documentItem) use ($requestedDocumentItemDb) {
            foreach ($requestedDocumentItemDb as $documentItemDb) {
                if ($documentItemDb->id === $documentItem->id) {
                    $documentItem->is_uploaded = $documentItemDb->is_uploaded;
                }
            }
            return $documentItem;
        }, $requestedDocumentItemCore);
    }
}
