<?php

namespace Sanf\Core\Modules\RequestedDocument\Services;

use Carbon\Carbon;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\RequestedDocument\Exceptions\RequestedDocumentNotFoundException;
use Sanf\Core\Modules\RequestedDocument\Repositories\RequestedDocumentRepositoryInterface;
use Sanf\Core\Modules\User\AuthModel;

class ReadUploadRequestedDocumentService implements ApplicationServiceInterface
{
    private AuthModel $userRepository;
    private RequestedDocumentRepositoryInterface $requestedDocumentEloquentRepository;

    public function __construct(
        AuthModel $userRepository,
        RequestedDocumentRepositoryInterface $requestedDocumentEloquentRepository
    ) {
        $this->userRepository = $userRepository;
        $this->requestedDocumentEloquentRepository = $requestedDocumentEloquentRepository;
    }

    /**
     * @param null $dto
     * @return object
     * @throws UserNotFoundException|RequestedDocumentNotFoundException
     */
    public function execute($dto = null)
    {
        $this->getUser($dto);
        $requestDocument = $this->getRequestedDocument($dto->request_id, $dto->profile_xid);

        return (object) [
            'id' => $requestDocument->id,
            'profile_xid' => $requestDocument->profile_xid,
            'request_no' => $requestDocument->request_no,
            'request_at' => Carbon::parse($requestDocument->request_at),
            'document_no' => $requestDocument->document_no,
            'document_type' => $requestDocument->type,
            'documents' => $this->mapRequestedItemDocs($requestDocument->items),
            'total_document' => $requestDocument->total_item,
            'total_uploaded' => $requestDocument->total_uploaded,
            'status' => $requestDocument->status,
        ];
    }

    private function getUser($dto): void
    {
        $user = $this->userRepository->newQuery()->find($dto->user_id);
        if (!$user) {
            throw new UserNotFoundException();
        }
    }

    private function getRequestedDocument(string $request_id, string $profile_xid)
    {
        $requestedDocument = $this->requestedDocumentEloquentRepository->findByRequestNo($request_id, $profile_xid);
        if (!$requestedDocument) {
            throw new RequestedDocumentNotFoundException();
        }

        return $requestedDocument;
    }

    private function mapRequestedItemDocs($items): array
    {
        return array_map(function ($data) {
            return (object) [
                'id' => $data['document_id'],
                'title' => $data['document_name'],
                'is_uploaded' => !is_null($data['document_file']),
            ];
        }, $items->toArray());
    }
}
