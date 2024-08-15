<?php

namespace Sanf\Core\Modules\Contract\Services;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Enums\ESignContractStatusEnum;
use Sanf\Core\Modules\Contract\Exceptions\ESignDocumentNotFoundException;
use Sanf\Core\Modules\Contract\Repositories\EloquentESignDocumentRepository;

class ESignDocumentDownloadService implements ApplicationServiceInterface
{
    protected ESignDocumentSignCheckService $eSignDocumentSignCheckService;
    protected AdInsESignDownloadDocumentService $adInsDownloadDocumentService;
    protected EloquentESignDocumentRepository $eSignRepository;

    public function __construct(
        ESignDocumentSignCheckService $eSignDocumentSignCheckService,
        AdInsESignDownloadDocumentService $adInsDownloadDocumentService,
        EloquentESignDocumentRepository $eSignRepository
    ) {
        $this->eSignDocumentSignCheckService = $eSignDocumentSignCheckService;
        $this->adInsDownloadDocumentService = $adInsDownloadDocumentService;
        $this->eSignRepository = $eSignRepository;
    }

    public function execute($dto = null)
    {
        $eSignDocument = $this->eSignRepository->findDocumentByDocId($dto->documentId);
        if (is_null($eSignDocument) === true) {
            throw new ESignDocumentNotFoundException();
        }

        $downloadResult = $this->adInsDownloadDocumentService->execute($dto);
        if (is_null($downloadResult->documentFileBase64) === true) {
            throw new ESignDocumentNotFoundException();
        }

        $documentBinary = base64_decode($downloadResult->documentFileBase64);

        $documentMetadata = $this->upload($documentBinary, $dto->documentId);

        $this->eSignRepository->updateDocument($eSignDocument->id, [
            'document_name' => $documentMetadata['file_name'],
            'document_file' => $documentMetadata,
            'updated_at' => CarbonImmutable::now(),
        ]);

        if ($eSignDocument->status_id !== ESignContractStatusEnum::COMPLETED) {
            $eSignDocumentSignCheckResult = $this->eSignDocumentSignCheckService->execute($dto);
        }

        return $documentBinary;
    }

    protected function upload(string $documentBinary, string $documentName)
    {
        $adInsDocumentPath = config('image-path.document_adins');
        $filename = "{$documentName}.pdf";
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
}
