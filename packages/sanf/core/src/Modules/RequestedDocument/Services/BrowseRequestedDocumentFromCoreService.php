<?php

namespace Sanf\Core\Modules\RequestedDocument\Services;

use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use NbsPhp\ApiWrapper\Api\Exceptions\EndpointNotDefinedException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\RequestedDocument\Dtos\ListRequestedDocumentDto;
use Sanf\Core\Modules\RequestedDocument\Enums\DocumentTypeEnum;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class BrowseRequestedDocumentFromCoreService implements ApplicationServiceInterface
{
    private SanfCoreApiClient $internalApiClient;

    public function __construct(SanfCoreApiClient $internalApiClient)
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
            switch ($dto->document_type) {
                case DocumentTypeEnum::CONTRACT:
                    $type = DocumentTypeEnum::CONTRACT_LABEL;
                    break;
                case DocumentTypeEnum::SUBMISSION:
                    $type = DocumentTypeEnum::SUBMISSION_LABEL;
                    break;
                case DocumentTypeEnum::PERSONAL:
                    $type = DocumentTypeEnum::PERSONAL_LABEL;
                    break;
            }

            $sortBy = $dto->sort_by;
            if ($sortBy === 'oldest') {
                $sortBy = 'latest';
            }

            $arguments = (object)[
                'profile_xid' => $dto->profile_xid,
                //'document_type' => $type ?? null, TODO please fix filter document type at sanf core api
                'skip' => $dto->skip,
                'limit' => $dto->limit,
                'order' => ucwords($sortBy),
                'keyword' => $dto->keyword,
            ];

            $response = $this->internalApiClient->browseRequestedDocuments($arguments);
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
