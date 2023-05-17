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
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;
use Sanf\Integration\Modules\TekenAja\TekenAjaApiClient;
use Throwable;

final class ESignUserDocumentCompleteService implements ApplicationServiceInterface
{
    protected ESignRepositoryInterface $eSignRepository;
    protected ESignDocumentSpecificationFactoryInterface $eSignDocumentAssigneeSpecificaton;
    protected UserRepositoryInterface $userRepository;
    protected UserNotificationRepositoryInterface $userNotificationRepository;
    protected PushNotificationServiceInterface $pushNotificationService;
    protected SanfCoreApiClient $client;
    protected TekenAjaApiClient $tekenAjaClient;

    public function __construct(
        ESignRepositoryInterface $eSignRepository,
        ESignDocumentSpecificationFactoryInterface $eSignDocumentAssigneeSpecificaton,
        UserRepositoryInterface $userRepository,
        UserNotificationRepositoryInterface $userNotificationRepository,
        PushNotificationServiceInterface $pushNotificationService,
        SanfCoreApiClient $client,
        TekenAjaApiClient $tekenAjaClient
    ) {
        $this->eSignRepository = $eSignRepository;
        $this->eSignDocumentAssigneeSpecificaton = $eSignDocumentAssigneeSpecificaton;
        $this->userNotificationRepository = $userNotificationRepository;
        $this->userRepository = $userRepository;
        $this->pushNotificationService = $pushNotificationService;
        $this->client = $client;
        $this->tekenAjaClient = $tekenAjaClient;
    }

    /**
     * @param null $dto
     * @return object
     * @throws NotificationInvalidException
     * @throws TekenAjaDocumentException
     * @throws TekenAjaExternalApiException
     * @throws UserNotFoundException
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function execute($dto = null): object
    {
        // get user base on email
        $eSignUser = $this->eSignRepository->findUserByEmail($dto->signers[0]['email']);
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

        $documentsAssignee = $this->eSignRepository->documentAssigneeQuery(
            $this->eSignDocumentAssigneeSpecificaton->paginateDocumentAssigneeByDocId($document->document_id, null)
        );

        // define user's sign
        $signs = [];
        foreach ($documentsAssignee as $documentAssignee) {
            $user = $this->userRepository->findById($documentAssignee->user_id);
            if (!$user) {
                throw new UserNotFoundException();
            }
            $signs[] = (object) [
                'full_name' => $user->full_name,
                'email' => $user->username,
            ];
        }

        // download e-sign file
        $result = $this->tekenAjaClient->download($document->document_id);
        if ($result['code']) {
            $this->errorHandle($result['code'], $result['message']);
        }

        // upload file;
        $path = config('image-path.document_tekenaja');
        $documentName = $document->document_name ?? $document->document_id;
        $slugDocumentName = str_slug(strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $documentName))));
        $formattedDocumentName = "final-{$slugDocumentName}.pdf";
        Storage::put($path . "{$formattedDocumentName}", file_get_contents($result['data']));

        // if image doesnt exist
        $exist = Storage::exists("{$path}{$formattedDocumentName}");
        throw_if(!$exist, new FileNotFoundException("{$path}{$formattedDocumentName}"));

        // update e-sign document assignee status
        foreach ($documentsAssignee as $documentAssignee) {
            $this->eSignRepository->updateDocumentAssignee($documentAssignee->id, [
                'status_id' => ESignContractStatusEnum::DONE,
                'updated_at' => Carbon::now(),
            ]);
        }

        // update e-sign document status
        $document = $this->eSignRepository->updateDocument($document->id, [
            'version' => $document->version + 1,
            'document_file' => [
                'file_name' => $formattedDocumentName,
                'directory' => $path,
                'path' => "{$path}{$formattedDocumentName}",
                'mime_type' => Storage::getMimeType("{$path}{$formattedDocumentName}")
            ],
            'status_id' => ESignContractStatusEnum::COMPLETED,
            'updated_at' => Carbon::now(),
            'modified_by' => [
                'source_by' => 'TekenAja',
                'user_id' => $user->id,
                'username' => $user->username,
                'full_name' => $user->full_name,
                'xid' => $user->xid,
                'personal_xid' => $user->personal_xid,
            ]
        ]);

        $document->signs = $signs;

        // update core
        // TODO create self service of send notification using event service
        $this->client->updateESignDocumentStatus($document->document_id);
        $this->client->updateESignDocumentFile($document->document_id, $formattedDocumentName, $document->document_file->path);

        $documentName = $document->document_name;
        // send notification
        // TODO create self service of send notification using event service
        $fcmTokens = [];
        $usersId = [];
        foreach ($dto->signers as $signer) {
            $eSignUser = $this->eSignRepository->findUserByEmail($signer['email']);
            if (!$eSignUser) {
                throw new UserNotFoundException();
            }

            $user = $this->userRepository->findById($eSignUser->user_id);
            if (!$user) {
                throw new UserNotFoundException();
            }

            $fcmTokens = array_merge($this->userNotificationRepository->getFcmTokens($user->id), $fcmTokens);
            $usersId[] = $user->id;
        }
        $data = [
            'xid' => nano_id(),
            'title' => __('Tanda Tangan Dokumen Kontrak Selesai'),
            'subtitle' => __('Selesai Tanda Tangan Kontrak'),
            'body' => __("Dokumen kontrak {$documentName} sudah selesai ditanda tangani oleh semua pihak, silahkan cek untuk lebih detil di halaman Ttd Kontrak,"),
            'type' => (string) NotificationTypeEnum::INFO,
            'screen' => 'contract_document_complete',
            'published_at' => Carbon::now(),
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
        ];

        foreach ($usersId as $userId) {
            try {
                $this->userNotificationRepository->create([
                    'xid' => $data['xid'],
                    'type' => (int) $data['type'],
                    'user_id' => $userId,
                    'data' => $data,
                ]);
            } catch (QueryException $exception) {
                if ($exception->getCode() == '23505') {
                    throw new NotificationInvalidException('ID not unique');
                }
                throw $exception;
            }
        }

        foreach (array_unique($fcmTokens) as $fcmToken) {
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

        return $document;
    }

    private function errorHandle(string $code, $messages)
    {
        switch ($code) {
            case TekenAjaApiResponseErrorCodeEnum::NOT_COMPLETE_SIGN:
            case TekenAjaApiResponseErrorCodeEnum::NOT_FOUND:
            case TekenAjaApiResponseErrorCodeEnum::ACCESS_UNAUTHORIZED:
                throw new TekenAjaDocumentException($code);
                break;
            case TekenAjaApiResponseErrorCodeEnum::SYSTEM_FAILURE:
            default:
                throw new TekenAjaExternalApiException($messages);
        }
    }
}
