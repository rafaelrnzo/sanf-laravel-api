<?php

namespace Sanf\Core\Modules\User\Repositories;

interface UserOAuthEncryptedRepositoryInterface
{
    public function create(array $data);

    public function findByProvider(string $provider, string $providerId);
}
