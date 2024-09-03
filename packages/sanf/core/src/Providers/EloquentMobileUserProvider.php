<?php

namespace Sanf\Core\Providers;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Encryptions\SodiumQuery;
use Sanf\Core\Modules\User\Enums\EntityType;

class EloquentMobileUserProvider extends EloquentUserProvider
{
    /**
     * Retrieve a user by the given credentials.
     *
     * @param array $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {

        if (
            empty($credentials) ||
            (count($credentials) === 1 &&
                Str::contains($this->firstCredentialKey($credentials), 'password'))
        ) {
            return null;
        }

        // First we will add each credential element to the query as a where clause.
        // Then we can execute the query and, if we found a user, return it in a
        // Eloquent User "model" that will be utilized by the Guard instances.
        $user = SodiumEncryption::query()->transaction(
            function (SodiumQuery $sodiumQuery) use ($credentials) {
                $query = $this->newModelQuery();

                foreach ($credentials as $key => $value) {
                    if (Str::contains($key, 'password')) {
                        continue;
                    }

                    if ($key === 'username') {
                        $query->where($sodiumQuery->selectRaw($key), $value);
                        continue;
                    }

                    if (is_array($value) || $value instanceof Arrayable) {
                        $query->whereIn($key, $value);
                    } else {
                        $query->where($key, $value);
                    }
                }

                $query->whereIn('entity_type_id', [EntityType::PERSONAL]);

                return $query->first();
            }
        );

        return $user;
    }
}
