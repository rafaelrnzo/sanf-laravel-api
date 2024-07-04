<?php

namespace Sanf\Dashboard\Modules\User\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Dashboard\Modules\User\Models\CustomerBindingModel;

class CustomerBindingEloquentRepository extends AbstractEloquentRepository
{
    protected CustomerBindingModel $bindingModel;

    public function __construct(CustomerBindingModel $bindingModel)
    {
        $this->bindingModel = $bindingModel;
    }

    public function findByBowheerIdAndEmail(string $id, string $email)
    {
        $bindingModel = $this->bindingModel
            ->newQuery()
            ->where('BowheerId', '=', $id)
            ->where('BowheerEmail', '=', $email)
            ->first();

        return $this->stripEloquentModel($bindingModel);
    }
}
