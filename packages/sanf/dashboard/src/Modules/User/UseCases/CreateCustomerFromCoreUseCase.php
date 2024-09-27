<?php

namespace Sanf\Dashboard\Modules\User\UseCases;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Dashboard\Modules\Role\Repositories\RoleEncryptedEloquentRepository;
use Sanf\Dashboard\Modules\User\Events\AccountBindingCreatedByCoreNotificationEvent;
use Sanf\Dashboard\Modules\User\Exceptions\AccountExistException;
use Sanf\Dashboard\Modules\User\Repositories\CustomerBindingEncryptedEloquentRepository;
use Sanf\Dashboard\Modules\User\Repositories\UserEncryptedEloquentRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class CreateCustomerFromCoreUseCase implements ApplicationServiceInterface
{
    private $userDashboardRepository;
    private $roleDashboardRepository;
    private $accountBindingDashboardRepository;

    public function __construct(
        UserEncryptedEloquentRepository $userDashboardRepository,
        RoleEncryptedEloquentRepository $roleDashboardRepository,
        CustomerBindingEncryptedEloquentRepository $accountBindingDashboardRepository
    ) {
        $this->userDashboardRepository = $userDashboardRepository;
        $this->roleDashboardRepository = $roleDashboardRepository;
        $this->accountBindingDashboardRepository = $accountBindingDashboardRepository;
    }

    public function execute($dto = null)
    {
        $accountBinding = $this->accountBindingDashboardRepository->findByBowheerIdAndEmail($dto->id, $dto->email);
        if ($accountBinding) {
            throw new AccountExistException('Bowheer ID and Email was exist');
        }

        $entityId = config('web-partner.user.entity.customer_id');
        $now = strtotime('now');

        $role = $this->roleDashboardRepository->findByEntity($entityId);
        if (is_null($role)) {
            throw new BadRequestHttpException("Customer role's not identified");
        }

        $requestData = [
            'auth' => [
                'xid' => $this->nanoIdAlphaNumberic(),
                'statusId' => config('web-partner.user.status.pending_id'),
                'entityTypeId' => $entityId,
                'fullName' => $dto->name,
                'username' => $dto->email,
                'password' => $this->password(),
                'createdAt' => $now,
                'updatedAt' => $now,
            ],
            'profile' => [
                'xid' => $this->nanoIdAlphaNumberic(),
                'fullName' => $dto->name,
                'email' => $dto->email,
                'createdAt' => $now,
                'updatedAt' => $now,
            ],
            'role' => [
                'roleId' => $role->id,
                'entityTypeId' => $entityId,
                'createdById' => config('web-partner.admin_id'),
                'createdAt' => $now,
                'updatedAt' => $now,
            ],
            'binding' => [
                'BowheerId' => $dto->id,
                'BowheerEmail' => $dto->email,
                'BowheerName' => $dto->name,
                'BowheerCode' => $dto->code,
                'createdAt' => $now,
                'updatedAt' => $now,
            ],
        ];

        $userAuth = $this->userDashboardRepository->create($requestData);

        $expireInDays = config('web-partner.user.verification.expire_days');
        $mailContent = (object) [
            'fullName' => $dto->name,
            'email' => $userAuth->username,
            'expireInDays' => $expireInDays,
            'token' => base64_encode(json_encode([
                'xid' => $userAuth->xid,
                'token' => hash_hmac('sha256', $userAuth->username, $userAuth->id),
                'expires' => Carbon::now()->addDays($expireInDays)->getTimestamp(),
            ])),
        ];

        event(new AccountBindingCreatedByCoreNotificationEvent($mailContent));

        return $userAuth;
    }

    public static function nanoIdAlphaNumberic(int $size = 5)
    {
        /** @var Client $client */
        $client = app()->make('nanoid');

        return $client->formatedId('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', $size);
    }

    /**
     * Generate a random, secure password.
     *
     * @param  int  $length
     * @param  bool  $letters
     * @param  bool  $numbers
     * @param  bool  $symbols
     * @param  bool  $spaces
     * @return string
     */
    public static function password($length = 32, $letters = true, $numbers = true, $symbols = true, $spaces = false)
    {
        $password = new Collection();

        $options = (new Collection([
            'letters' => $letters === true ? [
                'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k',
                'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v',
                'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G',
                'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R',
                'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            ] : null,
            'numbers' => $numbers === true ? [
                '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
            ] : null,
            'symbols' => $symbols === true ? [
                '~', '!', '#', '$', '%', '^', '&', '*', '(', ')', '-',
                '_', '.', ',', '<', '>', '?', '/', '\\', '{', '}', '[',
                ']', '|', ':', ';',
            ] : null,
            'spaces' => $spaces === true ? [' '] : null,
        ]))->filter()->each(
            fn ($c) => $password->push($c[random_int(0, count($c) - 1)])
        )->flatten();

        $length = $length - $password->count();

        return $password->merge($options->pipe(
            fn ($c) => Collection::times($length, fn () => $c[random_int(0, $c->count() - 1)])
        ))->shuffle()->implode('');
    }
}
