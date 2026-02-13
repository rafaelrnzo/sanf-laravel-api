<?php

namespace Sanf\Core\Modules\Contract\Services;

use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Enums\ESignContractStatusEnum;
use Sanf\Core\Modules\Contract\Events\ESignDocumentSignCompleteNotificationEvent;
use Sanf\Core\Modules\Contract\Exceptions\ESignDocumentNotFoundException;
use Sanf\Core\Modules\Contract\Repositories\EloquentESignDocumentEncryptedRepository;
use Sanf\Core\Modules\Contract\Specifications\ESignDocumentSpecificationFactoryInterface;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class ESignDocumentSignCheckService implements ApplicationServiceInterface
{
    protected const UNSIGNED = 0;
    protected const SIGNED = 1;
    protected const FAILED = 2;
    protected const SIGN_IN = 3;

    protected AdInsESignDocumentSignCheckService $adInsDocumentSignCheckService;
    protected AdInsESignDownloadDocumentService $adInsDownloadDocumentService;
    protected EloquentESignDocumentEncryptedRepository $eSignRepository;
    protected ESignDocumentSpecificationFactoryInterface $eSignDocumentSpecificationFactory;
    protected SanfCoreApiClient $sanfCoreClient;

    public function __construct(
        AdInsESignDocumentSignCheckService $adInsDocumentSignCheckService,
        AdInsESignDownloadDocumentService $adInsDownloadDocumentService,
        EloquentESignDocumentEncryptedRepository $eSignRepository,
        ESignDocumentSpecificationFactoryInterface $eSignDocumentSpecificationFactory,
        SanfCoreApiClient $sanfCoreClient
    ) {
        $this->adInsDocumentSignCheckService = $adInsDocumentSignCheckService;
        $this->adInsDownloadDocumentService = $adInsDownloadDocumentService;
        $this->eSignRepository = $eSignRepository;
        $this->eSignDocumentSpecificationFactory = $eSignDocumentSpecificationFactory;
        $this->sanfCoreClient = $sanfCoreClient;
    }

    public function execute($dto = null)
    {
        $statusSigning = [];
        $eSignDocument = $this->eSignRepository->findDocumentByDocId($dto->documentId);
        if (is_null($eSignDocument) === true) {
            throw new ESignDocumentNotFoundException();
        }

        $eSignDocumentAssignment = $this->eSignRepository->findDocumentAssigneeByDocId($dto->userId, $dto->documentId);
        if (is_null($eSignDocumentAssignment) === true) {
            throw new ESignDocumentNotFoundException();
        }

        $adInsDocumentSignResult = $this->adInsDocumentSignCheckService->execute(
            (object) [
                'documentId' => $eSignDocument->document_id,
                'referenceNo' => $eSignDocument->reference_no,
                'email' => $eSignDocumentAssignment->email,
            ]
        );

        if (is_null($adInsDocumentSignResult->statusSigning) === true) {
            return $statusSigning;
        }

        $totalAssignment = count($adInsDocumentSignResult->statusSigning->signer);
        $totalSignedDocument = 0;

        $statusSigning = array_map(
            function ($signer) use (&$totalSignedDocument) {
                if ($signer->signStatus == self::SIGNED) {
                    $totalSignedDocument++;
                }

                return (object) $signer;
            },
            $adInsDocumentSignResult->statusSigning->signer
        );

        $assigneFilterByEmail = array_filter(
            $statusSigning,
            function ($assigne) use ($eSignDocumentAssignment) {
                return strtolower($assigne->email) == $eSignDocumentAssignment->email;
            }
        );

        if (count($assigneFilterByEmail) === 0) {
            return $statusSigning;
        }

        $assigneStatus = array_values($assigneFilterByEmail)[0];

        $assigneDocumentStatus = $this->assigneeStatusBySignStatus($assigneStatus->signStatus, $eSignDocumentAssignment->status_id);

        $this->eSignRepository->updateDocumentAssignee(
            $eSignDocumentAssignment->id,
            [
                'status_id' => $assigneDocumentStatus,
                'updated_at' => CarbonImmutable::now(),
            ]
        );

        $documentStatus = $eSignDocument->status_id;
        if ($totalSignedDocument > 0) {
            $documentStatus = ESignContractStatusEnum::ON_PROGRESS;
        }

        if ($totalAssignment === $totalSignedDocument) {
            $documentStatus = ESignContractStatusEnum::COMPLETED;
        }

        $this->eSignRepository->updateDocument(
            $eSignDocument->id,
            [
                'status_id' => $documentStatus,
                'updated_at' => CarbonImmutable::now(),
            ]
        );

        if ($documentStatus === ESignContractStatusEnum::COMPLETED) {
            $downloadResult = $this->adInsDownloadDocumentService->execute($dto);

            if (is_null($downloadResult->documentFileBase64) === true) {
                throw new ESignDocumentNotFoundException();
            }

            $documentBinary = base64_decode($downloadResult->documentFileBase64);

            $filename = $eSignDocument->document_name ?? $dto->documentId;
            $documentMetadata = $this->upload($documentBinary, $filename);

            $this->eSignRepository->updateDocument($eSignDocument->id, [
                'document_name' => $documentMetadata['file_name'],
                'document_file' => $documentMetadata,
                'updated_at' => CarbonImmutable::now(),
            ]);

            $assigmentsDocument = $this->eSignRepository->documentAssigneeQuery(
                $this->eSignDocumentSpecificationFactory->paginateDocumentAssigneeByDocId($eSignDocument->document_id, null, null)
            );

            foreach ($assigmentsDocument as $eSignDocumentAssignment) {
                event(new ESignDocumentSignCompleteNotificationEvent($eSignDocumentAssignment->user_id, $eSignDocument->document_name));
            }

            $this->updateDocumentCoreStatus($this->sanfCoreClient, $eSignDocument->document_id);

            $this->updateDocumentCoreFile($this->sanfCoreClient, $eSignDocument->document_id, $filename, $documentMetadata['path']);
        }

        return $statusSigning;
    }

    public function assigneeStatusBySignStatus($signStatus, $default = null)
    {
        switch ($signStatus) {
            case self::SIGNED:
                return ESignContractStatusEnum::DONE;

            case self::FAILED:
                return ESignContractStatusEnum::FAILED;

            default:
                return $default;
        }
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
