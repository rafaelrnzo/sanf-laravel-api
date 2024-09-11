<?php

namespace Sanf\Core\Passwords;

use Carbon\Carbon;
use Illuminate\Auth\Passwords\DatabaseTokenRepository;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Encryptions\SodiumQuery;

class SodiumDatabaseTokenRepository extends DatabaseTokenRepository
{
    /**
     * Build the record payload for the table.
     *
     * @param  string  $email
     * @param  string  $token
     * @return array
     */
    protected function getPayload($email, $token)
    {
        $encryptor = SodiumEncryption::encryptor();

        return [
            'email' => $encryptor->encrypt($email),
            'token' => $this->hasher->make($token),
            'created_at' => new Carbon,
            'nonce' => $encryptor->nonce()->getNonceHex(),
        ];
    }

    /**
     * Delete all existing reset tokens from the database.
     *
     * @param  CanResetPasswordContract  $user
     * @return int
     */
    protected function deleteExisting(CanResetPasswordContract $user)
    {
        return SodiumEncryption::query()->transaction(
            function (SodiumQuery $sodiumQuery) use ($user) {
                return $this->getTable()
                    ->where(
                        $sodiumQuery->selectRaw('email'),
                        $user->getEmailForPasswordReset()
                    )
                    ->delete();
            }
        );
    }

    /**
     * Determine if a token record exists and is valid.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $token
     * @return bool
     */
    public function exists(CanResetPasswordContract $user, $token)
    {
        $record = SodiumEncryption::query()->transaction(
            function (SodiumQuery $sodiumQuery) use ($user) {
                return (array) $this->getTable()
                    ->where(
                        $sodiumQuery->selectRaw('email'),
                        $user->getEmailForPasswordReset()
                    )
                    ->first();
            }
        );

        return $record &&
               !$this->tokenExpired($record['created_at']) &&
                 $this->hasher->check($token, $record['token']);
    }

    /**
     * Determine if the given user recently created a password reset token.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return bool
     */
    public function recentlyCreatedToken(CanResetPasswordContract $user)
    {
        $record = SodiumEncryption::query()->transaction(
            function (SodiumQuery $sodiumQuery) use ($user) {
                return (array) $this->getTable()
                    ->where(
                        $sodiumQuery->selectRaw('email'),
                        $user->getEmailForPasswordReset()
                    )->first();
                }
        );

        return $record && $this->tokenRecentlyCreated($record['created_at']);
    }
}
