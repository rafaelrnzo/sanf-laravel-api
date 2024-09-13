<?php

namespace Sanf\Core\Modules\Contract\Jobs;

use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileNotFoundException;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Modules\Contract\Enums\AdInsCallbackTypeEnum;
use Sanf\Core\Modules\Contract\Enums\ESignContractStatusEnum;
use Sanf\Core\Modules\Contract\Events\ESignDocumentSignCompleteNotificationEvent;
use Sanf\Core\Modules\Contract\Exceptions\ESignDocumentNotFoundException;
use Sanf\Core\Modules\Contract\Repositories\EloquentESignDocumentEncryptedRepository;
use Sanf\Core\Modules\Contract\Services\AdInsESignDownloadDocumentService;
use Sanf\Core\Modules\Contract\Specifications\ESignDocumentSpecificationFactoryInterface;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class UpdateDocumentSignStatusByCallbackJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected object $request;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    public function handle(
        EloquentESignDocumentEncryptedRepository $eSignDocumentRepository,
        ESignDocumentSpecificationFactoryInterface $eSignDocumentSpecificationFactory,
        AdInsESignDownloadDocumentService $adInsDownloadDocumentService,
        SanfCoreApiClient $sanfCoreClient
    ) {
        $dto = $this->request;

        SodiumEncryption::query()->transaction(function () use (
            $dto,
            $eSignDocumentRepository,
            $eSignDocumentSpecificationFactory,
            $adInsDownloadDocumentService,
            $sanfCoreClient
        ) {
            try {
                $eSignDocument = null;

                if (empty($dto->documentId) === false) {
                    $eSignDocument = $eSignDocumentRepository->findDocumentByDocId($dto->documentId);
                }

                if (empty($dto->refNo) === false) {
                    $eSignDocument = $eSignDocumentRepository->findDocumentByRefNo($dto->refNo);
                }

                if (is_null($eSignDocument) === true) {
                    throw new ESignDocumentNotFoundException();
                }

                if ($dto->callbackType === AdInsCallbackTypeEnum::SIGNING_COMPLETE) {
                    $assigmentsDocument = $eSignDocumentRepository->documentAssigneeQuery(
                        $eSignDocumentSpecificationFactory->paginateDocumentAssigneeByDocId($eSignDocument->document_id, null, null)
                    );

                    $assignmentFilterByEmail = array_filter($assigmentsDocument, function ($eSignDocumentAssignment) use ($dto) {
                        return $eSignDocumentAssignment->email === $dto->email;
                    });

                    if (count($assignmentFilterByEmail) === 0) {
                        throw new ESignDocumentNotFoundException();
                    }

                    $eSignDocumentAssignment = $assignmentFilterByEmail[0];
                    $eSignDocumentRepository->updateDocumentAssignee($eSignDocumentAssignment->id, [
                        'status_id' => ESignContractStatusEnum::DONE,
                        'updated_at' => CarbonImmutable::now(),
                    ]);

                    if ($eSignDocument->status_id == ESignContractStatusEnum::SUBMITTED) {
                        $eSignDocumentRepository->updateDocument($eSignDocument->id, [
                            'status_id' => ESignContractStatusEnum::ON_PROGRESS,
                            'updated_at' => CarbonImmutable::now(),
                        ]);
                    }
                }

                if (in_array($dto->callbackType, [
                    AdInsCallbackTypeEnum::DOCUMENT_SIGN_COMPLETE,
                    AdInsCallbackTypeEnum::ALL_DOCUMENT_SIGN_COMPLETE,
                ]) && $eSignDocument->status_id !== ESignContractStatusEnum::COMPLETED) {

                    $downloadResult = $adInsDownloadDocumentService->execute($dto);

                    if (is_null($downloadResult->documentFileBase64) === true) {
                        throw new ESignDocumentNotFoundException();
                    }

                    $documentBinary = base64_decode($downloadResult->documentFileBase64);

                    $filename = $eSignDocument->document_name ?? $dto->documentId;
                    $documentMetadata = $this->upload($documentBinary, $filename);

                    $eSignDocumentRepository->updateDocument($eSignDocument->id, [
                        'document_name' => $documentMetadata['file_name'],
                        'document_file' => $documentMetadata,
                        'status_id' => ESignContractStatusEnum::COMPLETED,
                        'updated_at' => CarbonImmutable::now(),
                    ]);

                    $assigmentsDocument = $eSignDocumentRepository->documentAssigneeQuery(
                        $eSignDocumentSpecificationFactory->paginateDocumentAssigneeByDocId($eSignDocument->document_id, null, null)
                    );

                    foreach ($assigmentsDocument as $eSignDocumentAssignment) {
                        event(new ESignDocumentSignCompleteNotificationEvent($eSignDocumentAssignment->user_id, $eSignDocument->document_name));
                    }

                    $this->updateDocumentCoreStatus($sanfCoreClient, $eSignDocument->document_id);

                    $this->updateDocumentCoreFile($sanfCoreClient, $eSignDocument->document_id, $filename, $documentMetadata['path']);
                }
            } catch (Exception $exception) {
                report($exception);
            }
        });
    }

    protected function upload(string $documentBinary, string $documentName)
    {
        $adInsDocumentPath = config('image-path.document_adins');
        $slugDocumentName = preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower(trim(pathinfo($documentName, PATHINFO_FILENAME))));
        $filename = "final-{$slugDocumentName}.pdf";
        $filePath = "{$adInsDocumentPath}{$filename}";

        Storage::disk('minio_post')->put($filePath, $documentBinary, 'public');

        $fileExist = Storage::disk('minio_post')->exists("$filePath");
        if (is_null($fileExist) === true) {
            throw new FileNotFoundException("{$adInsDocumentPath}");
        }

        $metadata = Storage::disk('minio_post')->getMetaData("$filePath");

        return [
            'file_name' => $filename,
            'directory' => $adInsDocumentPath,
            'path' => "$filePath",
            'mime_type' => $metadata['mimetype'],
            'size' => $metadata['size'],
        ];
    }

    protected function updateDocumentCoreStatus(SanfCoreApiClient $sanfCoreClient, string $documentId)
    {
        try {
            $sanfCoreClient->updateESignDocumentStatus($documentId);
        } catch (Exception $exception) {
            report($exception);
        }
    }

    protected function updateDocumentCoreFile(SanfCoreApiClient $sanfCoreClient, string $documentId, string $filename, string $path)
    {
        try {
            $sanfCoreClient->updateESignDocumentFile($documentId, $filename, $path);
        } catch (Exception $exception) {
            report($exception);
        }
    }
}
