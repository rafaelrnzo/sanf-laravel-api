<?php

namespace Sanf\Core\Modules\Notification\Services;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Firebase\Auth\Token\Exception\InvalidToken;
use Illuminate\Database\QueryException;
use Kreait\Firebase\Exception\MessagingException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use NbsPhp\Notification\Repositories\UserNotificationRepositoryInterface;
use NbsPhp\Notification\Services\PushNotificationServiceInterface;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Modules\Notification\Dtos\AddPushNotificationByExternalRequestDto;
use Sanf\Core\Modules\Notification\Dtos\AddPushNotificationByExternalResponseDto;
use Sanf\Core\Modules\Notification\Exceptions\NotificationInvalidException;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Dashboard\Modules\Notification\Repositories\SanfindUserFcmNotificationEloquentRepository;

final class AddPushNotificationByExternalService implements ApplicationServiceInterface
{
    protected PushNotificationServiceInterface $pushNotificationService;
    protected UserRepositoryInterface $userRepository;
    protected UserNotificationRepositoryInterface $userNotificationRepository;
    protected SanfindUserFcmNotificationEloquentRepository $sanfindUserFcmNotificationRepository;

    /**
     * AddPushNotificationByExternalService constructor.
     * @param PushNotificationServiceInterface $pushNotificationService
     * @param UserRepositoryInterface $userRepository
     * @param UserNotificationRepositoryInterface $userNotificationRepository
     */
    public function __construct(
        PushNotificationServiceInterface $pushNotificationService,
        UserRepositoryInterface $userRepository,
        UserNotificationRepositoryInterface $userNotificationRepository,
        SanfindUserFcmNotificationEloquentRepository $sanfindUserFcmNotificationRepository
    ) {
        $this->pushNotificationService = $pushNotificationService;
        $this->userRepository = $userRepository;
        $this->userNotificationRepository = $userNotificationRepository;
        $this->sanfindUserFcmNotificationRepository = $sanfindUserFcmNotificationRepository;
    }

    /**
     * @param AddPushNotificationByExternalRequestDto $dto
     * @return AddPushNotificationByExternalResponseDto
     */
    public function execute($dto = null)
    {
        $user = SodiumEncryption::query()->transaction(function () use ($dto) {
            return $this->userRepository->findByEmail(strtolower($dto->email));
        });

        if (!$user) {
            throw new UserNotFoundException();
        }

        $fcmTokens = $this->userNotificationRepository->getFcmTokens($user->id);
        $data = [
            'xid' => $dto->id,
            'title' => $dto->title,
            'subtitle' => $dto->subtitle,
            'body' => $dto->body,
            'type' => (string) $dto->type->getValue(),
            'screen' => $dto->screen,
            'published_at' => Carbon::createFromTimestampUTC($dto->publishedAt),
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
        ];
        try {
            $userNotification = $this->userNotificationRepository->create([
                'xid' => $dto->id,
                'type' => $dto->type->getValue(),
                'user_id' => $user->id,
                'data' => $data,
            ]);
        } catch (QueryException $exception) {
            if ($exception->getCode() == '23505') {
                throw new NotificationInvalidException('ID not unique');
            }
            throw $exception;
        }

        if (
            !empty($dto->dashboardWebData['guard_type'])
            && !empty($dto->dashboardWebData['user_id'])
            && $dto->dashboardWebData['guard_type'] == 'sanfind_user') {
            $customerWebFcmToken = $this->sanfindUserFcmNotificationRepository->findLatest([
                'sanfind_userid' => $dto->dashboardWebData['user_id'],
            ]);

            if (!empty($customerWebFcmToken->token)) {
                $fcmTokens[] = $customerWebFcmToken->token;
                $data['link'] = $dto->dashboardWebData['link'] ?? null;
            }
        }

        foreach (array_unique($fcmTokens) as $fcmToken) {
            try {
                unset($data['subtitle']);
                $this->pushNotificationService->sendToDevice($fcmToken, $data);
            } catch (InvalidToken $exception) {
                $this->userNotificationRepository->deleteFcmToken($fcmToken);
                report($exception);
            } catch (MessagingException $exception) {
                $this->userNotificationRepository->deleteFcmToken($fcmToken);
                report($exception);
            }
        }

        return new AddPushNotificationByExternalResponseDto([
            'id' => $userNotification->id,
            'xid' => $userNotification->xid,
            'createdAt' => CarbonImmutable::make($userNotification->created_at),
            'updatedAt' => CarbonImmutable::make($userNotification->updated_at),
        ]);
    }
}
