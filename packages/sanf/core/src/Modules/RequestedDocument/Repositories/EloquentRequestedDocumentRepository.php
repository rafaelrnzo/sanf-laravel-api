<?php

namespace Sanf\Core\Modules\RequestedDocument\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Modules\RequestedDocument\Models\RequestedDocumentModel;

class EloquentRequestedDocumentRepository extends AbstractEloquentRepository implements RequestedDocumentRepositoryInterface
{

    private RequestedDocumentModel $model;

    public function __construct(RequestedDocumentModel $model)
    {
        $this->model = $model;
    }

    public function query($specification)
    {
        $models = $specification->buildQuery($this->model)->get();
        return $this->stripEloquentModel($models);
    }

    public function count($specification): int
    {
        return $specification->buildQuery($this->model)->count();
    }

    public function create(array $request)
    {
        return $this->model->newQuery()->forceCreate($request);
    }

    public function update(int $id, array $request)
    {
        return $this->model->newQuery()
            ->where('id', '=', $id)
            ->update($request);
    }

    public function findByRequestNo(string $request_no, string $profile_xid)
    {
        return $this->model->newQuery()
            ->with([
                'items' => function ($query) {
                    return $query->whereNull('deleted_at');
                }
            ])
            ->whereNull('deleted_at')
            ->where('profile_xid', '=', $profile_xid)
            ->where('request_no', '=', $request_no)
            ->first();
    }

    public function incrementTotalUploaded(int $id)
    {
        return $this->model->newQuery()
            ->where('id', '=', $id)
            ->increment('total_uploaded');
    }
}
