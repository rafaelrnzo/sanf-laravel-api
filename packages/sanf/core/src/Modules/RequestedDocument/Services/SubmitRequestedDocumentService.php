<?php

namespace Sanf\Core\Modules\RequestedDocument\Services;

use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\RequestedDocument\Enums\RequestedDocumentStatusEnum;
use Sanf\Core\Modules\RequestedDocument\Exceptions\RequestedDocumentNotFoundException;
use Sanf\Core\Modules\RequestedDocument\Exceptions\SubmitRequestedDocumentNotCompleteException;
use Sanf\Core\Modules\RequestedDocument\Repositories\RequestedDocumentItemRepositoryInterface;
use Sanf\Core\Modules\RequestedDocument\Repositories\RequestedDocumentRepositoryInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Integration\Exceptions\SanfInternalApiException;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class SubmitRequestedDocumentService implements ApplicationServiceInterface
{

    private AuthModel $userRepository;
    private RequestedDocumentRepositoryInterface $eloquentRequestedDocRepository;
    private RequestedDocumentItemRepositoryInterface $eloquentRequestedDocItemRepository;
    private SanfCoreApiClient $internalApiClient;

    public function __construct(
        AuthModel $userRepository,
        RequestedDocumentRepositoryInterface $eloquentRequestedDocRepository,
        RequestedDocumentItemRepositoryInterface $eloquentRequestedDocItemRepository,
        SanfCoreApiClient $internalApiClient
    ) {
        $this->userRepository = $userRepository;
        $this->eloquentRequestedDocRepository = $eloquentRequestedDocRepository;
        $this->eloquentRequestedDocItemRepository = $eloquentRequestedDocItemRepository;
        $this->internalApiClient = $internalApiClient;
    }

    /**
     * @param object $dto
     * @return bool
     * @throws GuzzleException
     * @throws EndpointNotDefinedException
     * @throws UserNotFoundException
     * @throws RequestedDocumentNotFoundException
     */
    public function execute($dto = null)
    {
        $user = $this->getUser($dto->user_id);
        $requestedDocument = $this->getRequestedDocument($dto->request_id, $user->id);
        if ($requestedDocument->total_item !== $requestedDocument->total_uploaded) {
            throw new SubmitRequestedDocumentNotCompleteException();
        }

        $documentItemId = [];
        $totalAlreadySync = 0;
        $totalSync = 0;
        foreach ($requestedDocument->items as $documentItem) {
            if ($documentItem->is_submitted) {
                $totalAlreadySync++;
                continue;
            }

            $upload_date = Carbon::make($documentItem->created_at)->format('d-M-Y');
            $request = (object)[
                'request_no' => $requestedDocument->request_no,
                'document_id' => $documentItem->document_id,
                'path' => $documentItem->document_file->path,
                'origin' => $documentItem->document_file->origin,
                'uploaded_at' => strtoupper($upload_date),
            ];

            try {
                $submit = $this->internalApiClient->submitRequestedUploadDocument($request);
                $documentItemId[] = $documentItem->id;
                $totalSync++;
            } catch (SanfInternalApiException $exception) {
                report($exception);
            }
        }

        foreach ($documentItemId as $id) {
            $this->eloquentRequestedDocItemRepository->update($id, [
                'is_submitted' => true,
                'updated_at' => Carbon::now(),
            ]);
        }

        if ($requestedDocument->total_item !== ($totalSync + $totalAlreadySync)) {
            return false;
        }

        $this->eloquentRequestedDocRepository->update($requestedDocument->id, [
            'status' => RequestedDocumentStatusEnum::SUBMITTED,
            'updated_at' => Carbon::now(),
        ]);

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

    private function getRequestedDocument(string $request_id, string $user_id)
    {
        $requestedDocument = $this->eloquentRequestedDocRepository->findByRequestNo($request_id, $user_id);
        if (!$requestedDocument) {
            throw new RequestedDocumentNotFoundException();
        }

        return $requestedDocument;
    }
}
