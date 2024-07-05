<?php

namespace Sanf\Dashboard\Modules\User\Repositories;

use Carbon\Carbon;
use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Dashboard\Modules\User\Models\UserAuthModel;

class UserEloquentRepository extends AbstractEloquentRepository
{
    protected UserAuthModel $userAuthModel;

    public function __construct(UserAuthModel $userAuthModel)
    {
        $this->userAuthModel = $userAuthModel;
    }

    public function findUserAuthByBowheerId(string $bowheerId)
    {
        $userAuthModel = $this->userAuthModel
            ->newQuery()
            ->with([
                'bindingAccount' => function ($query) use ($bowheerId) {
                    return $query->where('BowheerId', '=', $bowheerId);
                },
                'fcmTokens' => function ($query) {
                    return $query->where('expiresAt', '>=', Carbon::now());
                },
            ])
            ->has('bindingAccount')
            ->first();

        return $this->stripEloquentModel($userAuthModel);
    }

    public function create(array $requestData)
    {
        $authData = $requestData['auth'];
        $auth = $this->userAuthModel->newQuery()->create($authData);

        $customerBindingData = $requestData['binding'];
        $customerBindingData['userAuthId'] = $auth->id;
        $auth->bindingAccount()->newQuery()->create($customerBindingData);

        $profileData = $requestData['profile'];
        $profileData['userAuthId'] = $auth->id;
        $auth->userProfile()->newQuery()->create($profileData);

        $userRoleData = $requestData['role'];
        $userRoleData['userId'] = $auth->id;
        $auth->userRole()->newQuery()->create($userRoleData);

        return $this->stripEloquentModel($auth);
    }
}
