<?php

namespace Sanf\Core\Modules\RequestedDocument\Services;

use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Container\BindingResolutionException;
use NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\RequestedDocument\Dtos\ListRequestedDocumentDto;
use Sanf\Core\Modules\RequestedDocument\Enums\RequestedDocumentStatusEnum;
use Sanf\Core\Modules\RequestedDocument\Repositories\RequestedDocumentItemRepositoryInterface;
use Sanf\Core\Modules\RequestedDocument\Repositories\RequestedDocumentRepositoryInterface;
use Sanf\Core\Modules\User\AuthModel;

class BrowseRequestedDocumentService implements ApplicationServiceInterface
{
    private AuthModel $userRepository;
    private BrowseRequestedDocumentFromCoreService $coreService;
    private BrowseRequestedDocumentFromDbService $dbService;
    private RequestedDocumentRepositoryInterface $requestedDocumentEloquentRepository;
    private RequestedDocumentItemRepositoryInterface $requestedDocItemEloquentRepository;

    public function __construct(
        AuthModel $userRepository,
        BrowseRequestedDocumentFromCoreService $coreService,
        BrowseRequestedDocumentFromDbService $dbService,
        RequestedDocumentRepositoryInterface $requestedDocumentEloquentRepository,
        RequestedDocumentItemRepositoryInterface $requestedDocItemEloquentRepository
    ) {
        $this->userRepository = $userRepository;
        $this->coreService = $coreService;
        $this->dbService = $dbService;
        $this->requestedDocumentEloquentRepository = $requestedDocumentEloquentRepository;
        $this->requestedDocItemEloquentRepository = $requestedDocItemEloquentRepository;
    }

    /**
     * @param ListRequestedDocumentDto $dto
     * @return object
     * @throws UserNotFoundException
     * @throws GuzzleException
     * @throws EndpointNotDefinedException
     */
    public function execute($dto = null)
    {
        $this->getUser($dto);
        $status = $dto->status;
        $dataFromCore = null;
        $data = [];

        if ($status == RequestedDocumentStatusEnum::SUBMITTED) {
            $dataFromDb = $this->dbService->execute($dto);
            $data = $dataFromDb->data;
        }

        if ($status == RequestedDocumentStatusEnum::REQUESTED) {
            $dataFromCore = $this->coreService->execute($dto);
            $this->emptyPage($dto, $dataFromCore->data);

            $argument = $dto->toArray();
            $argument['status'] = null;
            $argument['keyword'] = null;

            $dataFromDb = $this->dbService->execute((object)$argument);

            $getOrCreateData = $this->getOrCreateData($dataFromDb, $dataFromCore, $dto);

            $argument['status'] = RequestedDocumentStatusEnum::REQUESTED;
            $argument['keyword'] = $dto->keyword;

            $dataFromDb = $this->dbService->execute((object)$argument);

            foreach ($dataFromDb->data as $dataDb) {
                $append = $this->requestDocumentAppendItem($dataDb, $dataFromCore);
                if (is_null($append)) {
                    continue;
                }

                $data[] = $append;
            }
        }

        $this->emptyPage($dto, $data);

        if ($dto->document_type) {
            $data = array_filter($data, function ($item) use ($dto) {
                return $item->document_type == $dto->document_type;
            });
        }

        switch ($dto->sort_by) {
            case 'oldest':
                $data = collect($data)->sortBy('request_no');
                break;
            case 'earliest':
            default:
                $data = collect($data)->sortByDesc('request_no');
        }

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
    private function emptyPage(ListRequestedDocumentDto $dto, array $data)
    {
        if (empty($data)) {
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

    private function getOrCreateData(object $dataFromDb, object $dataFromCore, ListRequestedDocumentDto $dto): array
    {
        return array_map(
            function ($requestedDocumentCore) use ($dto, $dataFromDb) {
                $requestedDocumentDb = $this->getExistingRequestedDocument($dataFromDb, $requestedDocumentCore);

                $this->storeIfDoesntExist(
                    $dto->user_id,
                    $dto->profile_xid,
                    $requestedDocumentCore,
                    $requestedDocumentDb
                );

                return $requestedDocumentCore;
            },
            $dataFromCore->data ?? []
        );
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
    private function storeIfDoesntExist(int $userId, string $profileXid, object $requestedDocument, ?object $requestedDocumentDb): object
    {
        if ($requestedDocumentDb) {
            if ($requestedDocumentDb->total_document !== $requestedDocument->total_document) {
                $this->requestedDocumentEloquentRepository->update($requestedDocumentDb->id, [
                    'total_item' => $requestedDocument->total_document,
                    'updated_at' => Carbon::now(),
                ]);
            }
            if (!$requestedDocumentDb->profile_xid) {
                $this->requestedDocumentEloquentRepository->update($requestedDocumentDb->id, [
                    'profile_xid' => $profileXid,
                    'updated_at' => Carbon::now(),
                ]);
            }
            if (count($requestedDocumentDb->documents) !== count($requestedDocument->documents)) {
                $existingId = array_pluck($requestedDocumentDb->documents, 'id');
                foreach ($requestedDocument->documents as $document) {
                    if (in_array($document->id, $existingId)) {
                        continue;
                    }
                    $this->requestedDocItemEloquentRepository->create([
                        'requested_document_id' => $requestedDocumentDb->id,
                        'document_id' => $document->id,
                        'document_name' => $document->title,
                        'document_file' => null,
                        'is_submitted' => false,
                    ]);
                }
            }

            return $requestedDocumentDb;
        }

        $requestedDocumentDb = $this->requestedDocumentEloquentRepository->create([
            'xid' => nano_id(),
            'user_id' => $userId,
            'profile_xid' => $profileXid,
            'request_no' => $requestedDocument->request_no,
            'request_at' => $requestedDocument->request_at,
            'document_no' => $requestedDocument->document_no,
            'type' => $requestedDocument->document_type,
            'total_item' => $requestedDocument->total_document,
            'total_uploaded' => 0,
            'status' => RequestedDocumentStatusEnum::REQUESTED,
        ]);

        foreach ($requestedDocument->documents as $document) {
            $this->requestedDocItemEloquentRepository->create([
                'requested_document_id' => $requestedDocumentDb->id,
                'document_id' => $document->id,
                'document_name' => $document->title,
                'document_file' => null,
                'is_submitted' => false,
            ]);
        }

        return $requestedDocumentDb;
    }

    /**
     * @param $dataDb
     * @param object $dataFromCore
     * @return null|object
     */
    private function requestDocumentAppendItem($dataDb, object $dataFromCore)
    {
        $documents = $dataDb->documents;

        $requestDocumentFromCore = null;
        foreach ($dataFromCore->data as $dataCore) {
            if ($dataCore->request_no === $dataDb->request_no) {
                $requestDocumentFromCore = $dataCore;
            }
        }

        if (is_null($requestDocumentFromCore)) {
            return null;
        }

        if (is_null($documents)) {
            $documents = $requestDocumentFromCore->documents;
        }

        $dataDb->documents = $this->getExistingRequestedDocumentItem($requestDocumentFromCore->documents, $documents);

        return $dataDb;
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
