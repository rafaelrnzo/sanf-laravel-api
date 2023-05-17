<?php

namespace Sanf\Core\Modules\Contract\Services;

use Carbon\Carbon;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Enums\ESignContractStatusEnum;
use Sanf\Core\Modules\Contract\Exceptions\ESignDocumentNotFoundException;
use Sanf\Core\Modules\Contract\Repositories\ESignRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

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
        // update e-sign document status
        $document = $this->eSignRepository->findDocumentByDocId($dto->documentId);
        if (!$document) {
            throw new ESignDocumentNotFoundException();
        }

        $userId = $document->modified_by->user_id ?? null;
        if ($userId) {
            $user = $this->userRepository->findById($userId);
            if (!$user) {
                throw new UserNotFoundException();
            }

            $eSignUser = $this->eSignRepository->findUserByEmail($user->username);
            if (!$eSignUser) {
                throw new UserNotFoundException();
            }
        }

        $this->eSignRepository->updateDocument($document->id, [
            'version' => $document->version + 1,
            'status_id' => ESignContractStatusEnum::FAILED,
            'updated_at' => Carbon::now(),
            'modified_by' => [
                'source_by' => 'TekenAja',
                'user_id' => ($userId) ? $user->id : null,
                'username' => ($userId) ? $user->username : null,
                'full_name' => ($userId) ? $user->full_name : null,
                'xid' => ($userId) ? $user->xid : null,
                'personal_xid' => ($userId) ? $user->personal_xid : null,
            ],
        ]);

        return $document;
    }
}
