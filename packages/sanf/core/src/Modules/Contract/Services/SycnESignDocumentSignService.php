<?php

namespace Sanf\Core\Modules\Contract\Services;

use Carbon\Carbon;
use Illuminate\Contracts\Container\BindingResolutionException;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dto\AddESignDocumentSignDto;
use Sanf\Core\Modules\Contract\Enums\ESignContractStatusEnum;
use Sanf\Core\Modules\Contract\Exceptions\ESignDocumentHasSignException;
use Sanf\Core\Modules\Contract\Repositories\ESignRepositoryInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Integration\InternalApiClient;

final class SycnESignDocumentSignService implements ApplicationServiceInterface
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
                    'user_id' => $data->userId,
                    'email' => $data->email,
                ]);
            }

            $document = $this->eSignRepository->findDocumentByDocId($data->documentId);
            if (!$document) {
                $user = $this->userRepository->newQuery()->find($data->userId);
                if (!$user) {
                    throw new UserNotFoundException();
                }
                $this->eSignRepository->createDocument([
                    'xid' => nano_id(),
                    'document_id' => $data->documentId,
                    'document_name' => $data->documentName ?? null,
                    'expired_at' => Carbon::createFromTimestamp($data->expiredAt),
                    'status_id' => ESignContractStatusEnum::SUBMIT,
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
        }

        return true;
    }
}
