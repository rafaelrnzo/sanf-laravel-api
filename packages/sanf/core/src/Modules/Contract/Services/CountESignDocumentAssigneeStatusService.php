<?php

namespace Sanf\Core\Modules\Contract\Services;

use Carbon\Carbon;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dtos\CountESignDocumentAssigneeStatusRequestDto;
use Sanf\Core\Modules\Contract\Enums\ESignContractStatusEnum;
use Sanf\Core\Modules\Contract\Repositories\ESignRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

final class CountESignDocumentAssigneeStatusService implements ApplicationServiceInterface
{
    protected UserRepositoryInterface $userRepository;
    protected ESignRepositoryInterface $eSignRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        ESignRepositoryInterface $eSignRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->eSignRepository = $eSignRepository;
    }

    /**
     * @param CountESignDocumentAssigneeStatusRequestDto $dto
     * @throws UserNotFoundException
     * @return array{status_id: mixed, total: mixed[]}
     */
    public function execute($dto = null)
    {
        $user = $this->userRepository->findById($dto->userId);

        if (!$user) {
            throw new UserNotFoundException();
        }

        $documentAssignees = $this->eSignRepository->countDocumentAssigneeStatusByUser(
            $user->id,
            Carbon::now()
        );

        return array_map(function ($statusId) use ($documentAssignees) {
            $documentAssignee = $documentAssignees->firstWhere('status_id', $statusId);
            $statusEnum = ESignContractStatusEnum::from($statusId);

            return [
                'status_id' => $statusId,
                'status_name' => $statusEnum->toText(),
                'total' => optional($documentAssignee)->total ?? 0,
            ];
        }, ESignContractStatusEnum::ALL);
    }
}
