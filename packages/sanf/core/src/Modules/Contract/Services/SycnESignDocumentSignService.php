<?php

namespace Sanf\Core\Modules\Contract\Services;

use Illuminate\Contracts\Container\BindingResolutionException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Enums\ESignContractStatusEnum;
use Sanf\Core\Modules\Contract\Repositories\ESignRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

final class SycnESignDocumentSignService implements ApplicationServiceInterface
{
    protected UserRepositoryInterface $userRepository;
    protected ESignRepositoryInterface $eSignRepository;
    protected SanfCoreApiClient $client;
    protected array $users;

    public function __construct(
        ESignRepositoryInterface $eSignRepository,
        UserRepositoryInterface $userRepository,
        SanfCoreApiClient $client
    ) {
        $this->eSignRepository = $eSignRepository;
        $this->userRepository = $userRepository;
        $this->client = $client;
    }

    /**
     * @param null $dto
     * @return bool
     * @throws UserNotFoundException
     * @throws BindingResolutionException
     */
    public function execute($dto = null): bool
    {
        foreach ($dto->data as $data) {
            $assigneeDocument = $this->eSignRepository->findDocumentAssigneeByDocId($data->userId, $data->documentId);
            if (!$assigneeDocument) {
                $this->eSignRepository->createDocumentAssignee([
                    'xid' => nano_id(),
                    'document_id' => $data->documentId,
                    'reference_no' => $data->referenceNo,
                    'user_id' => $data->userId,
                    'email' => $data->email,
                    'status_id' => ESignContractStatusEnum::ASSIGNEE,
                ]);
            } elseif ($assigneeDocument->reference_no !== $data->referenceNo) {
                $this->eSignRepository->updateDocumentAssignee($assigneeDocument->id, [
                    'reference_no' => $data->referenceNo,
                ]);
            }

            $document = $this->eSignRepository->findDocumentByDocId($data->documentId);
            if (!$document) {
                $user = $this->getUser($data->userId);

                $this->eSignRepository->createDocument([
                    'xid' => nano_id(),
                    'document_id' => $data->documentId,
                    'reference_no' => $data->referenceNo,
                    'document_name' => $data->documentName ?? null,
                    'expired_at' => $data->expiredAt ?? null,
                    'status_id' => ESignContractStatusEnum::SUBMITTED,
                    'version' => 1,
                    'modified_by' => [
                        'user_id' => $user->id,
                        'username' => $user->username,
                        'full_name' => $user->full_name,
                        'xid' => $user->xid,
                        'personal_xid' => $user->personal_xid,
                    ],
                ]);
            } elseif ($document->reference_no !== $data->referenceNo) {
                $user = $this->getUser($data->userId);

                $this->eSignRepository->updateDocument($document->id, [
                    'reference_no' => $data->referenceNo,
                    'version' => $document->version + 1,
                    'modified_by' => [
                        'user_id' => $user->id,
                        'username' => $user->username,
                        'full_name' => $user->full_name,
                        'xid' => $user->xid,
                        'personal_xid' => $user->personal_xid,
                    ],
                ]);
            }
        }

        return true;
    }

    private function getUser($userId)
    {
        if (isset($this->users[$userId])) {
            return $this->users[$userId];
        }

        $user = $this->userRepository->findById($userId);

        if (!$user) {
            throw new UserNotFoundException();
        }

        return $this->users[$userId] = $user;
    }
}
