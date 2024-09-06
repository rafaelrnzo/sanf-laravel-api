<?php

namespace Sanf\Core\ValidationRules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Encryptions\SodiumQuery;
use Sanf\Core\Modules\Contract\Enums\ESignRegistrationStatusEnum;
use Sanf\Core\Modules\Contract\Models\UserTekenAjaEncryptedModel;

class UserTekenajaUniqueEmailWithCompleteStatusRule implements Rule
{
    protected $ignoreUserId;

    public function __construct($ignoreUserId = null)
    {
        $this->ignoreUserId = $ignoreUserId;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $exists = SodiumEncryption::query()->transaction(
            function (SodiumQuery $sodiumQuery) use ($value) {
                return UserTekenAjaEncryptedModel::query()
                    ->where($sodiumQuery->selectRaw('email'), $value)
                    ->where('status_id', ESignRegistrationStatusEnum::COMPLETE)
                    ->when($this->ignoreUserId, function (Builder $query, $id) {
                        $query->where('user_id', '!=', $id);
                    })
                    ->exists();
            }
        );

        return !$exists;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.unique');
    }
}
