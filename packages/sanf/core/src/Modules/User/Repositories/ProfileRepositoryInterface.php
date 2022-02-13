<?php

namespace Sanf\Core\Modules\User\Repositories;


use Sanf\Core\Modules\User\Entities\ProfileEntityInterface;

interface ProfileRepositoryInterface
{
    public function findById($customerId): ?ProfileEntityInterface;

    public function findPersonalProfileByEmail($email): ?ProfileEntityInterface;

    public function findByEmail($email): array;
}
