<?php

namespace Sanf\Core\Modules\RequestedDocument\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Modules\RequestedDocument\Models\RequestedDocumentItemModel;

class EloquentRequestedDocumentItemRepository extends AbstractEloquentRepository implements
    RequestedDocumentItemRepositoryInterface
{

    private RequestedDocumentItemModel $model;

    public function __construct(RequestedDocumentItemModel $model)
    {
        $this->model = $model;
    }

    public function getByDocumentNoAndId(string $user_id, string $request_no, string $document_id)
    {
        return $this->model->newQuery()
            ->select('requested_document_item.*')
            ->join('requested_document', function ($query) {
                return $query->on('requested_document_item.requested_document_id', '=', 'requested_document.id')
                    ->whereNull('requested_document.deleted_at');
            })
            ->where('requested_document.user_id', $user_id)
            ->where('requested_document.request_no', $request_no)
            ->where('requested_document_item.document_id', $document_id)
            ->whereNull('requested_document_item.deleted_at')
            ->get();
    }
}
