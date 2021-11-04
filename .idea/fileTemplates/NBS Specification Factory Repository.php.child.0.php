<?php

namespace ${VENDOR}\Core\Modules\\${MODULE}\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;

class Eloquent${NAME}Repository extends AbstractEloquentRepository implements ${NAME}RepositoryInterface
{
    protected \$model;

    public function __construct(${NAME}Model \$model)
    {
        \$this->model = \$model;
    }

    public function findById(\$id)
    {
        \$model = \$this->model->newQuery()->with(['user', 'status'])->find(\$id);
        return \$this->stripEloquentModel(\$model);
    }

    public function findByXid(\$xid)
    {
        \$model = \$this->model->newQuery()->where('xid', \$xid)->with(['user', 'status'])->first();
        return \$this->stripEloquentModel(\$model);
    }

    public function query(\$specification)
    {
        \$models = \$specification->buildQuery(\$this->model)->get();
        return \$this->stripEloquentModel(\$models);
    }

    public function add(\$fields)
    {
        \$model = \$this->model->newQuery()->forceCreate(\$fields);
        return \$this->stripEloquentModel(\$model);
    }

    public function update(\$fields, \$specification = null)
    {
        if (!is_null(\$specification)) {
            \$model = \$specification->buildQuery(\$this->model)->update(\$fields);
            return \$this->stripEloquentModel(\$model);
        }

        \$model = \$this->model->newQuery()->where('id', \$fields['id'])->update(\$fields);
        return \$this->stripEloquentModel(\$model);
    }

    public function remove(\$specification)
    {
        return \$specification->buildQuery(\$this->model)->delete();
    }

    public function removeById(\$id)
    {
        return \$this->model->newQuery()->where('id', \$id)->delete();
    }

    public function removeByXid(\$xid)
    {
        return \$this->model->newQuery()->where('xid', \$xid)->delete();
    }

    public function size(\$specification = null)
    {
        if (!is_null(\$specification)) {
            return \$specification->buildQuery(\$this->model)->count();
        }
        return \$this->model->newQuery()->select('id')->count();
    }
}
