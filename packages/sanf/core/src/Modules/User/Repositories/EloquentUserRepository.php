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

    public function findById($id)
    {
        return $this->model->newQuery()->find($id);
    }

    public function findByEmail($email)
    {
        return $this->model->newQuery()->where('username', $email)->first();
    }
}
