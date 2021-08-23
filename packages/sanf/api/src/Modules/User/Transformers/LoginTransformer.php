<?php


namespace Sanf\Api\Modules\User\Transformers;


use League\Fractal\TransformerAbstract;
use Sanf\Core\Modules\User\ProfileType;

class LoginTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'profile' => [
                'id' => $item->id,
                'xid' => (string)$item->xid,
                'full_name' => ($this->decideProfileType($item) === ProfileType::PERSONAL) ? $item->full_name : $item->company_name,
                'email' => $item->username,
                'type_name' => (new ProfileType($this->decideProfileType($item)))->getTranslation(),
                'type_id' => $this->decideProfileType($item)
            ]
        ];
    }

    protected function decideProfileType($item)
    {
        return $item->profile_type ?? ProfileType::PERSONAL;
    }
}
