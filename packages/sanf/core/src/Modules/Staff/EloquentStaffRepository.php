<?php

namespace Sanf\Core\Modules\Staff;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use NbsPhp\Core\SpecificationInterface;

class EloquentStaffRepository extends AbstractEloquentRepository implements StaffRepositoryInterface
{
    protected $model;

    public function __construct(UserCompanyModel $model)
    {
        $this->model = $model;
    }

    public function getByCompanyXid($companyXid)
    {
        $model = $this->model->newQuery()
            ->with('user.status')
            ->where('company_xid', $companyXid)
            ->get();

        return $this->stripEloquentModel($model);
    }

    public function getByUserId($userId)
    {
        $model = $this->model->newQuery()
            ->where('user_id', $userId)
            ->get();

        return $this->stripEloquentModel($model);
    }

    public function findByCompanyXidAndUserId($companyXid, $userId)
    {
        $model = $this->model->newQuery()
            ->with('user.status')
            ->where('company_xid', $companyXid)
            ->where('user_id', $userId)
            ->first();

        return $this->stripEloquentModel($model);
    }

    public function add($fields)
    {
        $model = $this->model->newQuery()->forceCreate($fields);

        return $this->stripEloquentModel($model);
    }

    public function remove(SpecificationInterface $specification)
    {
        return $specification->buildQuery($this->model)->delete();
    }

    public function removeById($id)
    {
        return $this->stripEloquentModel($this->model->newQuery()->where('id', $id)->delete());
    }
}
