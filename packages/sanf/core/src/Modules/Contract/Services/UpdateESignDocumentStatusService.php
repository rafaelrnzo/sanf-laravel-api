<?php

namespace Sanf\Core\Modules\Contract\Services;

use Carbon\Carbon;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dto\UpdateESignDocumentStatusDto;
use Sanf\Core\Modules\Contract\Enums\ESignContractStatusEnum;
use Sanf\Core\Modules\Contract\Exceptions\ESignDocumentNotFoundException;
use Sanf\Core\Modules\Contract\Repositories\ESignRepositoryInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Integration\InternalApiClient;

final class UpdateESignDocumentStatusService implements ApplicationServiceInterface
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
        /** @var UpdateESignDocumentStatusDto $dto */
        $user = $this->userRepository->newQuery()->find($dto->userId);
        if (!$user) {
            throw new UserNotFoundException();
        }

        // get e-sign document
        $document = $this->eSignRepository->findDocumentByDocId($dto->documentId);
        if (!$document) {
            throw new ESignDocumentNotFoundException();
        }

        $documentAssignee = $this->eSignRepository->findDocumentAssigneeByDocId($dto->userId, $document->document_id);
        if (!$documentAssignee) {
            throw new ESignDocumentNotFoundException();
        }

        $this->eSignRepository->updateDocumentAssignee($documentAssignee->id, [
            'status_id' => ESignContractStatusEnum::DONE,
            'updated_at' => Carbon::now(),
        ]);

        if ($document->status_id != ESignContractStatusEnum::COMPLETED) {
            $this->eSignRepository->updateDocument($document->id, [
                'version' => $document->version + 1,
                'status_id' => ESignContractStatusEnum::ON_PROGRESS,
                'modified_by' => [
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'full_name' => $user->full_name,
                    'xid' => $user->xid,
                    'personal_xid' => $user->personal_xid,
                ],
            ]);
        }

        return true;
    }
}
