<?php

namespace Sanf\Core\Modules\User\Repositories;

interface UserOAuthRepositoryInterface
{
    public function create(array $data);

    public function findByProvider(string $provider, string $providerId);

    public function update(array $data, $id);
}
