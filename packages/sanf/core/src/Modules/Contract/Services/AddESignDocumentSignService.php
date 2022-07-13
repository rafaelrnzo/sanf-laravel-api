<?php

namespace Sanf\Core\Modules\Contract\Services;

use Carbon\Carbon;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dto\AddESignDocumentSignDto;
use Sanf\Core\Modules\Contract\Enums\ESignContractStatusEnum;
use Sanf\Core\Modules\Contract\Exceptions\ESignDocumentHasSignException;
use Sanf\Core\Modules\Contract\Repositories\ESignRepositoryInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Integration\InternalApiClient;

final class AddESignDocumentSignService implements ApplicationServiceInterface
{
    protected AuthModel $userRepository;
    protected ESignRepositoryInterface $eSignRepository;
    protected InternalApiClient $client;

    public function __construct(
        ESignRepositoryInterface $eSignRepository,
        AuthModel $userRepository,
        InternalApiClient $client
    ) {
        $this->eSignRepository = $eSignRepository;
        $this->userRepository = $userRepository;
        $this->client = $client;
    }

    /**
     * @param null $dto
     * @return bool
     * @throws UserNotFoundException
     */
    public function execute($dto = null): bool
    {
        /** @var AddESignDocumentSignDto $dto */
        $user = $this->userRepository->newQuery()->find($dto->userId);
        if (!$user) {
            throw new UserNotFoundException();
        }

        // get e-sign document
        $document = $this->eSignRepository->findDocumentByDocId($dto->documentId);
        if ($document) {
            // update e-sign document
            $this->eSignRepository->updateDocument($document->id, [
                'version' => $document->version + 1,
                'modified_by' => [
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'full_name' => $user->full_name,
                    'xid' => $user->xid,
                    'personal_xid' => $user->personal_xid,
                ],
            ]);
        } else {
            // create new e-sign document
            $document = $this->eSignRepository->createDocument([
                'xid' => nano_id(),
                'document_id' => $dto->documentId,
                'document_name' => $dto->documentName ?? null,
                'expired_at' => Carbon::createFromTimestamp($dto->expiredAt),
                'status_id' => ESignContractStatusEnum::ON_PROGRESS,
                'version' => 1,
                'modified_by' => [
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'full_name' => $user->full_name,
                    'xid' => $user->xid,
                    'personal_xid' => $user->personal_xid,
                ],
            ]);
        }

        $assigneeDocument = $this->eSignRepository->findDocumentAssigneeByDocId($dto->userId, $document->document_id);
        if ($assigneeDocument) {
            throw new ESignDocumentHasSignException();
        }

        // create e-sign assignee document
        $this->eSignRepository->createDocumentAssignee([
            'xid' => nano_id(),
            'document_id' => $document->document_id,
            'user_id' => $dto->userId,
            'email' => $dto->email,
        ]);

        return true;
    }
}
