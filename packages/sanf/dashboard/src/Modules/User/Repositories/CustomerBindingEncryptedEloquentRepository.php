<?php

namespace Sanf\Dashboard\Modules\User\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Dashboard\Modules\User\Models\CustomerBindingEncryptedModel;

class CustomerBindingEncryptedEloquentRepository extends AbstractEloquentRepository
{
    protected CustomerBindingEncryptedModel $bindingModel;

    public function __construct(CustomerBindingEncryptedModel $bindingModel)
    {
        $this->bindingModel = $bindingModel;
    }

    public function findByBowheerIdAndEmail(string $id, string $email)
    {
        $sodiumQuery = SodiumEncryption::query();

        $bindingModel = $this->bindingModel
            ->newQuery()
            ->where('BowheerId', '=', $id)
            ->where($sodiumQuery->selectRaw('"BowheerEmail"'), '=', $email)
            ->first();

        return $this->stripEloquentModel($bindingModel);
    }
}
