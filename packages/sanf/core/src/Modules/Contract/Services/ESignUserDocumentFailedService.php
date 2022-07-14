<?php

namespace Sanf\Core\Modules\Contract\Services;

use Carbon\Carbon;
use Firebase\Auth\Token\Exception\InvalidToken;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Exception\MessagingException;
use League\Flysystem\FileNotFoundException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use NbsPhp\Notification\Repositories\UserNotificationRepositoryInterface;
use NbsPhp\Notification\Services\PushNotificationServiceInterface;
use Sanf\Core\Modules\Contract\Enums\ESignContractStatusEnum;
use Sanf\Core\Modules\Contract\Exceptions\ESignDocumentNotFoundException;
use Sanf\Core\Modules\Contract\Repositories\ESignRepositoryInterface;
use Sanf\Core\Modules\Contract\Specifications\ESignDocumentSpecificationFactoryInterface;
use Sanf\Core\Modules\Notification\Exceptions\NotificationInvalidException;
use Sanf\Core\Modules\Notification\NotificationTypeEnum;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Integration\Enums\TekenAjaApiResponseErrorCodeEnum;
use Sanf\Integration\Exceptions\TekenAjaDocumentException;
use Sanf\Integration\Exceptions\TekenAjaExternalApiException;
use Sanf\Integration\InternalApiClient;
use Sanf\Integration\TekenAjaInternalApiClient;
use Throwable;

final class ESignUserDocumentFailedService implements ApplicationServiceInterface
{
    protected ESignRepositoryInterface $eSignRepository;
    protected UserRepositoryInterface $userRepository;

    public function __construct(
        ESignRepositoryInterface $eSignRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->eSignRepository = $eSignRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param null $dto
     * @return object
     * @throws ESignDocumentNotFoundException
     * @throws UserNotFoundException
     */
    public function execute($dto = null): object
    {
        // get user base on email
        $eSignUser = $this->eSignRepository->findUserByEmail($dto->email);
        if (!$eSignUser) {
            throw new UserNotFoundException();
        }

        $user = $this->userRepository->findById($eSignUser->user_id);
        if (!$user) {
            throw new UserNotFoundException();
        }

        // update e-sign document status
        $document = $this->eSignRepository->findDocumentByDocId($dto->documentId);
        if (!$document) {
            throw new ESignDocumentNotFoundException();
        }

        $this->eSignRepository->updateDocument($document->id, [
            'version' => $document->version + 1,
            'status_id' => ESignContractStatusEnum::FAILED,
            'updated_at' => Carbon::now(),
            'modified_by' => [
                'source_by' => 'TekenAja',
                'user_id' => $user->id,
                'username' => $user->username,
                'full_name' => $user->full_name,
                'xid' => $user->xid,
                'personal_xid' => $user->personal_xid,
            ],
        ]);

        return $document;
    }
}
