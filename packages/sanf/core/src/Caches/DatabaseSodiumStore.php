<?php

namespace Sanf\Core\Caches;

use Closure;
use Exception;
use Illuminate\Cache\DatabaseStore;
use Sanf\Core\Encryptions\SodiumEncryption;

class DatabaseSodiumStore extends DatabaseStore
{
    protected $sodiumQuery;

    /**
     * Create a new database store.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param array $configCache
     * @return void
     */
    public function __construct($app, $configCache)
    {
        $connection = $app['db']->connection($configCache['connection'] ?? null);

        $prefix = $configCache['prefix'] ?? $app['config']['cache.prefix'];

        parent::__construct($connection, $configCache['table'], $prefix);

        $this->sodiumQuery = SodiumEncryption::query();
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string|array  $key
     * @return mixed
     */
    public function get($key)
    {
        $prefixed = $this->prefix . $key;

        $cache = $this->connection->transaction(function () use ($prefixed) {
            $this->sodiumQuery->hideLogStatement();

            $hashedKey = SodiumEncryption::hash($prefixed);
            return $this->table()->where('key_hash', '=', $hashedKey)->first();
        });

        // If we have a cache record we will check the expiration time against current
        // time on the system and see if the record has expired. If it has, we will
        // remove the records from the database table so it isn't returned again.
        if (is_null($cache)) {
            return;
        }

        $cache = is_array($cache) ? (object) $cache : $cache;

        // If this cache expiration date is past the current time, we will remove this
        // item from the cache. Then we will return a null value since the cache is
        // expired. We will use "Carbon" to make this comparison with the column.
        if ($this->currentTime() >= $cache->expiration) {
            $this->forget($key);

            return;
        }

        return $this->unserialize($cache->value);
    }

    /**
     * Store an item in the cache for a given number of seconds.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  int  $seconds
     * @return bool
     */
    public function put($key, $value, $seconds)
    {
        $encryptor = SodiumEncryption::encryptor();

        $prefixedKey = $this->prefix . $key;

        $key = $encryptor->encrypt($prefixedKey);

        $key_hash = $encryptor->hash($prefixedKey);

        $value = $this->serialize($value);

        $expiration = $this->getTime() + $seconds;

        $nonce = $encryptor->nonce()->getNonceHex();

        try {
            return $this->table()
                ->insert(compact('key', 'key_hash', 'value', 'expiration', 'nonce'));
        } catch (Exception $e) {
            $result = $this->connection->transaction(function () use ($prefixedKey, $value, $expiration) {
                $this->sodiumQuery->hideLogStatement();

                $hashedKey = SodiumEncryption::hash($prefixedKey);
                return $this->table()
                    ->where('key_hash', $hashedKey)
                    ->update(compact('value', 'expiration'));
            });

            return $result > 0;
        }
    }

    /**
     * Increment or decrement an item in the cache.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  Closure  $callback
     * @return int|bool
     */
    protected function incrementOrDecrement($key, $value, Closure $callback)
    {
        return $this->connection->transaction(function () use ($key, $value, $callback) {
            $this->sodiumQuery->hideLogStatement();

            $prefixed = $this->prefix . $key;
            $hashedKey = SodiumEncryption::hash($prefixed);

            $cache = $this->table()
                        ->where('key_hash', $hashedKey)
                        ->lockForUpdate()
                        ->first();

            // If there is no value in the cache, we will return false here. Otherwise the
            // value will be decrypted and we will proceed with this function to either
            // increment or decrement this value based on the given action callbacks.
            if (is_null($cache)) {
                return false;
            }

            $cache = is_array($cache) ? (object) $cache : $cache;

            $current = $this->unserialize($cache->value);

            // Here we'll call this callback function that was given to the function which
            // is used to either increment or decrement the function. We use a callback
            // so we do not have to recreate all this logic in each of the functions.
            $new = $callback((int) $current, $value);

            if (!is_numeric($current)) {
                return false;
            }

            // Here we will update the values in the table. We will also encrypt the value
            // since database cache values are encrypted by default with secure storage
            // that can't be easily read. We will return the new value after storing.
            $this->table()->where('key_hash', $hashedKey)->update([
                'value' => $this->serialize($new),
            ]);

            return $new;
        });
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string  $key
     * @return bool
     */
    public function forget($key)
    {
        $this->connection->transaction(function () use ($key) {
            $this->sodiumQuery->hideLogStatement();

            $prefixed = $this->prefix . $key;
            $hashedKey = SodiumEncryption::hash($prefixed);
            
            $this->table()->where('key_hash', '=', $hashedKey)->delete();
        });

        return true;
    }
}
