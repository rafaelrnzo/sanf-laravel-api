<?php

namespace Sanf\Dashboard\Modules\User\Repositories;

use Carbon\Carbon;
use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Dashboard\Modules\User\Models\UserAuthEncryptedModel;

class UserEncryptedEloquentRepository extends AbstractEloquentRepository
{
    protected UserAuthEncryptedModel $userAuthModel;
    protected array $userAuthEncryptedFields;
    protected array $userAuthEncryptedJsonFields;
    protected array $customerBindingEncryptedFields;
    protected array $customerBindingEncryptedJsonFields;
    protected array $userProfileEncryptedFields;
    protected array $userProfileEncryptedJsonFields;
    protected array $roleUserEncryptedJsonFields;

    public function __construct(UserAuthEncryptedModel $userAuthModel)
    {
        $this->userAuthModel = $userAuthModel;

        $this->userAuthEncryptedFields = [
            'username',
            'fullName',
        ];
        $this->userAuthEncryptedJsonFields = [
            'createdBy',
            'modifiedBy',
        ];

        $this->customerBindingEncryptedFields = [
            'BowheerEmail',
            'BowheerName',
        ];
        $this->customerBindingEncryptedJsonFields = [
            'createdBy',
            'modifiedBy',
        ];

        $this->userProfileEncryptedFields = [
            'fullName',
            'email',
            'phoneNumber',
        ];
        $this->userProfileEncryptedJsonFields = [
            'createdBy',
            'modifiedBy',
        ];

        $this->roleUserEncryptedJsonFields = [
            'createdBy',
            'modifiedBy',
        ];
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
        $authData = SodiumEncryption::encryptor()->encryptMultipleData(
            $authData,
            $this->userAuthEncryptedFields,
            $this->userAuthEncryptedJsonFields
        );
        $auth = $this->userAuthModel->newQuery()->create($authData);

        $customerBindingData = $requestData['binding'];
        $customerBindingData['userAuthId'] = $auth->id;
        $customerBindingData = SodiumEncryption::encryptor()->encryptMultipleData(
            $customerBindingData,
            $this->customerBindingEncryptedFields,
            $this->customerBindingEncryptedJsonFields
        );
        $auth->bindingAccount()->newQuery()->create($customerBindingData);

        $profileData = $requestData['profile'];
        $profileData['userAuthId'] = $auth->id;
        $profileData = SodiumEncryption::encryptor()->encryptMultipleData(
            $profileData,
            $this->userProfileEncryptedFields,
            $this->userProfileEncryptedJsonFields
        );
        $auth->userProfile()->newQuery()->create($profileData);

        $userRoleData = $requestData['role'];
        $userRoleData['userId'] = $auth->id;
        $userRoleData = SodiumEncryption::encryptor()->encryptMultipleData(
            $userRoleData,
            [],
            $this->roleUserEncryptedJsonFields
        );
        $auth->userRole()->newQuery()->create($userRoleData);

        return $this->stripEloquentModel($auth->fresh());
    }
}
