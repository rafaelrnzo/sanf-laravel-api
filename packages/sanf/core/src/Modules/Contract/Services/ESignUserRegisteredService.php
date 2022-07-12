<?php

namespace Sanf\Core\Modules\Contract\Services;

use Carbon\Carbon;
use Firebase\Auth\Token\Exception\InvalidToken;
use Illuminate\Database\QueryException;
use Kreait\Firebase\Exception\MessagingException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use NbsPhp\Notification\Repositories\UserNotificationRepositoryInterface;
use NbsPhp\Notification\Services\PushNotificationServiceInterface;
use Sanf\Core\Modules\Contract\Enums\UserRegistrationStatusEnum;
use Sanf\Core\Modules\Contract\Repositories\ESignRepositoryInterface;
use Sanf\Core\Modules\Notification\Exceptions\NotificationInvalidException;
use Sanf\Core\Modules\Notification\NotificationTypeEnum;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Integration\InternalApiClient;

final class ESignUserRegisteredService implements ApplicationServiceInterface
{
    protected ESignRepositoryInterface $eSignRepository;
    protected UserRepositoryInterface $userRepository;
    protected UserNotificationRepositoryInterface $userNotificationRepository;
    protected PushNotificationServiceInterface $pushNotificationService;
    protected InternalApiClient $client;

    public function __construct(
        ESignRepositoryInterface $eSignRepository,
        UserRepositoryInterface $userRepository,
        UserNotificationRepositoryInterface $userNotificationRepository,
        PushNotificationServiceInterface $pushNotificationService,
        InternalApiClient $client
    ) {
        $this->eSignRepository = $eSignRepository;
        $this->userNotificationRepository = $userNotificationRepository;
        $this->userRepository = $userRepository;
        $this->pushNotificationService = $pushNotificationService;
        $this->client = $client;
    }

    /**
     * @param null $dto
     * @return object
     */
    public function execute($dto = null): object
    {
        // get user base on email
        $eSignUser = $this->eSignRepository->findUserByEmail($dto->email);
        if (!$eSignUser) {
            throw new UserNotFoundException();
        }

        $user = $this->userRepository->findByEmail($dto->email);
        if (!$user) {
            throw new UserNotFoundException();
        }

        // update status into complete state
        $eSignUser = $this->eSignRepository->updateUser($eSignUser->id, [
            'status_id' => UserRegistrationStatusEnum::COMPLETE,
            'updated_at' => Carbon::now(),
        ]);

        // update core
        // TODO create self service of send notification using event service
        $this->client->updateESignUserStatus($dto->email);

        // send notification
        // TODO create self service of send notification using event service
        $fcmTokens = $this->userNotificationRepository->getFcmTokens($user->id);
        $data = [
            'xid' => nano_id(),
            'title' => __('Pendaftaran Tanda Tangan Digital Berhasil'),
            'subtitle' => __('Sukses Pendaftaran e-Sign'),
            'body' => __('Data yang anda kirimkan berhasil di verifikasi. Saat ini Anda sudah mendapatkan akses untuk melakukan tanda tangan digital di halaman Ttd Kontrak'),
            'type' => (string) NotificationTypeEnum::INFO,
            'screen' => 'esign_registration_complete',
            'published_at' => Carbon::now(),
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
        ];

        try {
            $this->userNotificationRepository->create([
                'xid' => $data['xid'],
                'type' => (int) $data['type'],
                'user_id' => $user->id,
                'data' => $data,
            ]);
        } catch (QueryException $exception) {
            if ($exception->getCode() == '23505') {
                throw new NotificationInvalidException('ID not unique');
            }
            throw $exception;
        }
        foreach ($fcmTokens as $fcmToken) {
            try {
                $this->pushNotificationService->sendToDevice($fcmToken, $data);
            } catch (InvalidToken $exception) {
                $this->userNotificationRepository->deleteFcmToken($fcmToken);
                report($exception);
            } catch (MessagingException $exception) {
                $this->userNotificationRepository->deleteFcmToken($fcmToken);
                report($exception);
            }
        }

        return $eSignUser;
    }
}
