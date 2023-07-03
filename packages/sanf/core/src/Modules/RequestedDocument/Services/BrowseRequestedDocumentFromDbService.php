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
        $profileXid = (string)$dto->profile_xid;
        $status = ($dto->status) ? (int)$dto->status : null;
        $documentType = ($dto->document_type) ? (int)$dto->document_type : null;
        $keyword = ($dto->keyword) ? (string)$dto->keyword : null;
        $sortBy = ($dto->sort_by) ? (string)$dto->sort_by : null;
        $skip = ($dto->skip) ? (int)$dto->skip : null;
        $limit = ($dto->limit) ? (int)$dto->limit : null;

        $records = $this->requestedDocumentEloquentRepository->query(
            $this->requestedDocumentEloquentSpecification->paginate(
                $profileXid,
                $status,
                $documentType,
                $keyword,
                $sortBy,
                $skip,
                $limit,
                null
            )
        );
        $totalRecord = $this->requestedDocumentEloquentRepository->count(
            $this->requestedDocumentEloquentSpecification->paginate(
                $profileXid,
                $status,
                $documentType,
                $keyword,
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
                'id' => $data->id,
                'profile_xid' => $data->profile_xid,
                'request_no' => $data->request_no,
                'request_at' => Carbon::parse($data->request_at),
                'document_no' => $data->document_no,
                'document_type' => $data->type,
                'documents' => $this->mapRequestedItemDocs($data->items),
                'total_document' => $data->total_item,
                'total_uploaded' => $data->total_uploaded,
                'status' => $data->status,
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
