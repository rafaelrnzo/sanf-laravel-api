<?php

namespace Sanf\Core\Modules\Contract\Services;

use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Events\ESignDocumentDownloadEvent;
use Sanf\Core\Modules\Contract\Exceptions\ESignDocumentNotFoundException;
use Sanf\Core\Modules\Contract\Exceptions\ESignUserNotRegisteredException;
use Sanf\Core\Modules\Contract\Repositories\ESignRepositoryInterface;
use Sanf\Core\Modules\User\AuthModel;

final class SendESignDocumentViaEmailService implements ApplicationServiceInterface
{
    protected AuthModel $userRepository;
    protected ESignRepositoryInterface $eSignRepository;

    public function __construct(
        ESignRepositoryInterface $eSignRepository,
        AuthModel $userRepository
    ) {
        $this->eSignRepository = $eSignRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param null $dto
     * @return bool
     * @throws UserNotFoundException
     */
    public function execute($dto = null): bool
    {
        $user = $this->userRepository->newQuery()->find($dto->userId);
        if (!$user) {
            throw new UserNotFoundException();
        }

        $userTekenAja = $this->eSignRepository->findUserByEmail($dto->email);
        if (!$userTekenAja) {
            throw new ESignUserNotRegisteredException();
        }

        $document = $this->eSignRepository->findDocumentByDocId($dto->documentId);
        if (!$document) {
            throw new ESignDocumentNotFoundException();
        }
        $document->full_name = $user->full_name;
        event(new ESignDocumentDownloadEvent($dto->email, $document));

        return true;
    }
}
