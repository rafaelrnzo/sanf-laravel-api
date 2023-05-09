<?php

namespace Sanf\Core\Modules\RequestedDocument\Services;

use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\RequestedDocument\Dtos\ListRequestedDocumentDto;
use Sanf\Core\Modules\RequestedDocument\Enums\DocumentTypeEnum;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\InternalApiClient;

class BrowseRequestedDocumentFromCoreService implements ApplicationServiceInterface
{

    private InternalApiClient $internalApiClient;

    public function __construct(InternalApiClient $internalApiClient)
    {
        $this->internalApiClient = $internalApiClient;
    }

    /**
     * @param ListRequestedDocumentDto $dto
     * @return object
     * @throws GuzzleException
     * @throws EndpointNotDefinedException
     */
    public function execute($dto = null)
    {
        try {
            $response = $this->internalApiClient->browseRequestedDocuments($dto);
        } catch (SanfInternalApiDataNotFoundException $exception) {
            return (object)[
                'data' => [],
                'total' => 0,
            ];
        }

        $data = $this->mapRequestedDocs($response);

        if ($dto->document_type) {
            $data = array_filter($data, function ($item) use ($dto) {
                return $item->document_type == $dto->document_type;
            });
        }

        return (object)[
            'data' => $data,
            'total' => $response->total,
        ];
    }

    private function mapRequestedDocs($response): array
    {
        return array_map(function ($data) {
            switch ($data->TYPE_DOC) {
                case DocumentTypeEnum::CONTRACT_LABEL:
                    $documentType = DocumentTypeEnum::CONTRACT;
                    break;
                case DocumentTypeEnum::SUBMISSION_LABEL:
                    $documentType = DocumentTypeEnum::SUBMISSION;
                    break;
                case DocumentTypeEnum::PERSONAL_LABEL:
                    $documentType = DocumentTypeEnum::PERSONAL;
                    break;
            }

            return (object)[
                'request_no' => $data->NO_PERMINTAAN ?? null,
                'request_at' => $data->TGL_PERMINTAAN ? Carbon::make($data->TGL_PERMINTAAN) : null,
                'document_no' => $data->NO_TYPE_DOC ?? null,
                'document_type' => $documentType ?? null,
                'documents' => $data->ITEM ? $this->mapRequestedItemDocs($data->ITEM) : [],
                'total_document' => $data->ITEM ? count($data->ITEM) : 0
            ];
        }, $response->data);
    }

    private function mapRequestedItemDocs($response): array
    {
        return array_map(function ($data) {
            return (object)[
                'id' => $data->DOC_ID ?? null,
                'title' => $data->DOC_NAME ?? null,
                'is_uploaded' => false
            ];
        }, $response);
    }
}
