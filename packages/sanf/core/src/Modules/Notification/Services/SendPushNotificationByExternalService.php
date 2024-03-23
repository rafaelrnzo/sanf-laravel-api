<?php

namespace Sanf\Core\Modules\Notification\Services;

use Illuminate\Support\Facades\Log;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Notification\Dtos\AddPushNotificationByExternalRequestDto;
use Sanf\Core\Modules\Notification\Dtos\SendPushNotificationByExternalRequestDto;
use Sanf\Core\Modules\Notification\Events\NotifiedUserByExternalEvent;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Core\Modules\User\Specifications\UserAuthSpecificationFactoryInterface;

class SendPushNotificationByExternalService implements ApplicationServiceInterface
{
    protected UserRepositoryInterface $userRepository;
    protected UserAuthSpecificationFactoryInterface $userAuthSpecificationFactory;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserAuthSpecificationFactoryInterface $userAuthSpecificationFactory
    ) {
        $this->userRepository = $userRepository;
        $this->userAuthSpecificationFactory = $userAuthSpecificationFactory;
    }

    public function execute($dto = null)
    {
        /** @var SendPushNotificationByExternalRequestDto $dto */

        // Get target user's;
        switch ($dto->isNotifyAll) {
            case true:
                $users = $this->getAllUser();
                break;
            default:
                $users = $this->getSpecifiedUser($dto->email);
                break;
        }

        // Create event send push notification;
        $contents = [];
        foreach ($users as $user) {
            $contents[] = new AddPushNotificationByExternalRequestDto([
                'id' => nano_id(),
                'type' => $dto->type,
                'email' => $user->username,
                'customer_id' => (string) $user->xid ?? null,
                'title' => $dto->title,
                'subtitle' => $dto->subtitle,
                'screen' => $dto->screen,
                'body' => $dto->body,
                'published_at' => $dto->publishedAt,
            ]);
        }

        event(new NotifiedUserByExternalEvent($contents));
    }

    private function getAllUser(): array
    {
        return $this->userRepository->query(
            $this->userAuthSpecificationFactory->paginateUserActive(null, null, null, null)
        );
    }

    private function getSpecifiedUser(?array $emails): array
    {
        $users = [];
        $uniquenessEmail = [];
        foreach (array_unique($emails) as $email) {
            // Skip if already processed;
            $uniquenessId = [];
            if (in_array($email, $uniquenessId)) {
                continue;
            }

            // Append into uniqueness variable;
            $uniquenessId += [$email];

            // Get user;
            $user = $this->userRepository->findByEmail(strtolower($email));

            // Skip user if doesnt exist;
            if (!$user) {
                Log::warning("Email user: {$email} doesn't exist");
            } else {
                $users += [$user];
            }
        }

        return $users;
    }
}
