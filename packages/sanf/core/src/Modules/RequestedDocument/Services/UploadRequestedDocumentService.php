<?php

namespace Sanf\Core\Modules\RequestedDocument\Services;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Storage;
use NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\RequestedDocument\Dtos\UploadRequestedDocumentDto;
use Sanf\Core\Modules\RequestedDocument\Exceptions\RequestedDocumentNotFoundException;
use Sanf\Core\Modules\RequestedDocument\Repositories\RequestedDocumentItemRepositoryInterface;
use Sanf\Core\Modules\RequestedDocument\Repositories\RequestedDocumentRepositoryInterface;
use Sanf\Core\Modules\User\AuthModel;

class UploadRequestedDocumentService implements ApplicationServiceInterface
{
    private AuthModel $userRepository;
    private RequestedDocumentRepositoryInterface $eloquentRequestedDocRepository;
    private RequestedDocumentItemRepositoryInterface $eloquentRequestedDocItemRepository;

    public function __construct(
        AuthModel $userRepository,
        RequestedDocumentRepositoryInterface $eloquentRequestedDocRepository,
        RequestedDocumentItemRepositoryInterface $eloquentRequestedDocItemRepository
    ) {
        $this->userRepository = $userRepository;
        $this->eloquentRequestedDocRepository = $eloquentRequestedDocRepository;
        $this->eloquentRequestedDocItemRepository = $eloquentRequestedDocItemRepository;
    }

    /**
     * @param UploadRequestedDocumentDto $dto
     * @return bool
     * @throws GuzzleException
     * @throws EndpointNotDefinedException
     * @throws UserNotFoundException
     * @throws RequestedDocumentNotFoundException
     */
    public function execute($dto = null)
    {
        $this->getUser($dto->user_id);
        $requestedDocument = $this->getRequestedDocument($dto->request_id, $dto->profile_xid);
        $requestedDocumentItem = $this->getRequestedDocumentItem($requestedDocument->id, $dto->document_id);

        $tempDir = config('image-path.temp');
        $dir = config('image-path.requested-document');

        $metadata = $this->moveFile($dto->filename, $dir, $tempDir);

        $this->eloquentRequestedDocItemRepository->update($requestedDocumentItem->id, [
            'document_file' => [
                'origin' => $dto->origin,
                'file_name' => $dto->filename,
                'directory' => $dir,
                'path' => $dir . $dto->filename,
                'mime_type' => $metadata['mimetype'],
                'size' => $metadata['size'],
            ],
        ]);

        $this->eloquentRequestedDocRepository->incrementTotalUploaded($requestedDocument->id);

        return true;
    }

    private function getUser(string $user_id)
    {
        $user = $this->userRepository->newQuery()->find($user_id);
        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    private function getRequestedDocument(string $request_id, string $profile_xid)
    {
        $requestedDocument = $this->eloquentRequestedDocRepository->findByRequestNo($request_id, $profile_xid);
        if (!$requestedDocument) {
            throw new RequestedDocumentNotFoundException();
        }

        return $requestedDocument;
    }

    private function getRequestedDocumentItem(string $requestDocId, string $documentNo)
    {
        $requestedDocumentItem = $this->eloquentRequestedDocItemRepository->findByRequestIdAndDocNo(
            $requestDocId,
            $documentNo
        );

        if (!$requestedDocumentItem) {
            throw new RequestedDocumentNotFoundException();
        }

        return $requestedDocumentItem;
    }

    private function moveFile(string $filename, string $dir, string $tempDir): array
    {
        $exist = Storage::exists($tempDir . $filename);
        if ($exist) {
            Storage::move($tempDir . $filename, $dir . $filename);
        }

        return Storage::getMetaData($dir . $filename);
    }
}
