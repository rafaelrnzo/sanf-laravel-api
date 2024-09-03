<?php

namespace Sanf\Core\Modules\User\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Modules\User\AuthModel;

class EloquentUserRepository extends AbstractEloquentRepository implements UserRepositoryInterface
{
    protected $model;

    public function __construct(AuthModel $model)
    {
        $this->model = $model;
    }

    public function query($specification)
    {
        $records = $specification->buildQuery($this->model)->get();

        return $this->stripEloquentModel($records);
    }

    public function findById($id)
    {
        return $this->model->newQuery()->find($id);
    }

    public function findByEmail($email)
    {
        return $this->model->newQuery()->where('username', $email)->first();
    }

    public function existsByEmailAndStatusIds(string $email, array $statusIds): bool
    {
        return $this->model->newQuery()
            ->where('username', $email)
            ->whereIn('status_id', $statusIds)
            ->exists();
    }

    public function create(array $data) {
        return $this->model->newQuery()->forceCreate($data);
    }

    public function update(array $data, $id): bool
    {
        return $this->model->newQuery()
            ->whereId($id)
            ->update($data);
    }
}
