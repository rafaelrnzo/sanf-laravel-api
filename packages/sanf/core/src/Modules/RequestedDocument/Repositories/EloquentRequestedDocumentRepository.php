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

    public function findByRequestNo(string $request_no, int $user_id)
    {
        return $this->model->newQuery()
            ->whereNull('deleted_at')
            ->where('user_id', '=', $user_id)
            ->where('request_no', '=', $request_no)
            ->first();
    }
}
