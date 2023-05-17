<?php

namespace Sanf\Core\Modules\Contract\Services;

use Carbon\Carbon;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Enums\ESignContractStatusEnum;
use Sanf\Core\Modules\Contract\Exceptions\ESignDocumentNotFoundException;
use Sanf\Core\Modules\Contract\Repositories\ESignRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

final class ESignUserDocumentSignedService implements ApplicationServiceInterface
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

        $documentAssignee = $this->eSignRepository->findDocumentAssigneeByDocId($eSignUser->user_id, $dto->documentId);
        if (!$documentAssignee) {
            throw new ESignDocumentNotFoundException();
        }

        $this->eSignRepository->updateDocumentAssignee($documentAssignee->id, [
            'status_id' => ESignContractStatusEnum::DONE,
            'updated_at' => Carbon::now(),
        ]);

        $this->eSignRepository->updateDocument($document->id, [
            'version' => $document->version + 1,
            'status_id' => ESignContractStatusEnum::ON_PROGRESS,
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
        $document->email = $documentAssignee->email;
        $document->signs = [
            (object)[
                'email' => $documentAssignee->email,
                'document_sign_url' => $documentAssignee->document_sign_url
            ]
        ];

        return $document;
    }
}
