<?php

namespace Sanf\Api\Modules\Bank\Transformers;

use League\Fractal\TransformerAbstract;
use Sanf\Core\Modules\Bank\Entities\GuzzleUserBankAccountEntity;

class UserBankAccountTransformer extends TransformerAbstract
{
    public function transform($account)
    {
        /* @var GuzzleUserBankAccountEntity $account*/
        return [
            'xid' => $account->getBankId(),
            'account_name' => $account->getOwner(),
            'account_provider' => $account->getProvider(),
            'account_no' => $account->getAccountNo(),
            'is_default' => $account->getIsDefault(),
            'updated_at' => unix_timestamp($account->getUpdatedAt()),
        ];
    }
}
