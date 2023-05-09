<?php

namespace Sanf\Core\Modules\RequestedDocument\Services;

use Carbon\Carbon;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\RequestedDocument\Repositories\RequestedDocumentRepositoryInterface;
use Sanf\Core\Modules\RequestedDocument\Specifications\RequestedDocumentSpecificationInterface;

class BrowseRequestedDocumentFromDbService implements ApplicationServiceInterface
{

    private RequestedDocumentRepositoryInterface $requestedDocumentEloquentRepository;
    private RequestedDocumentSpecificationInterface $requestedDocumentEloquentSpecification;

    public function __construct(
        RequestedDocumentRepositoryInterface $eloquentRepository,
        RequestedDocumentSpecificationInterface $eloquentSpecification
    ) {
        $this->requestedDocumentEloquentRepository = $eloquentRepository;
        $this->requestedDocumentEloquentSpecification = $eloquentSpecification;
    }

    public function execute($dto = null)
    {
        $records = $this->requestedDocumentEloquentRepository->query(
            $this->requestedDocumentEloquentSpecification->paginate(
                $dto->user_id,
                $dto->status,
                $dto->document_type,
                $dto->keyword,
                $dto->sort_by,
                $dto->skip,
                $dto->limit,
                null
            )
        );
        $totalRecord = $this->requestedDocumentEloquentRepository->count(
            $this->requestedDocumentEloquentSpecification->paginate(
                $dto->user_id,
                $dto->status,
                $dto->document_type,
                $dto->keyword,
                null,
                null,
                null,
                null
            )
        );

        return (object)[
            'data' => $this->mapRequestedDocs($records),
            'total' => $totalRecord,
        ];
    }

    private function mapRequestedDocs($records): array
    {
        return array_map(function ($data) {
            return (object)[
                'request_no' => $data->request_no,
                'request_at' => Carbon::parse($data->request_at),
                'document_no' => $data->document_no,
                'document_type' => $data->type,
                'documents' => $this->mapRequestedItemDocs($data->items),
                'total_document' => $data->total_item,
                'total_uploaded' => $data->total_uploaded,
            ];
        }, $records);
    }

    private function mapRequestedItemDocs($response): array
    {
        return array_map(function ($data) {
            return (object)[
                'id' => $data->document_id,
                'title' => $data->document_name,
                'is_uploaded' => !is_null($data->document_file)
            ];
        }, $response);
    }
}
