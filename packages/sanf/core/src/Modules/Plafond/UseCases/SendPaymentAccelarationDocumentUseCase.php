<?php

namespace Sanf\Core\Modules\Plafond\UseCases;

use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Plafond\Jobs\SendEmailPaymentAccelarationDocumentJob;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;

final class SendPaymentAccelarationDocumentUseCase implements ApplicationServiceInterface
{
    private $userRepository;

    public function __construct(
        UserRepositoryInterface $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    public function execute($dto = null)
    {

        $user = $this->userRepository->findById($dto->userId);
        if (!$user) {
            throw new UserNotFoundException();
        }

        $payload = (object) [
            'company' => 'PIHAK PERTAMA (PT)',
            'bowheer' => 'PIHAK KEDUA (PT)',
            'plafondId' => 'PLAFOND NO',
        ];

        dispatch(new SendEmailPaymentAccelarationDocumentJob($user->username, $payload));

        return true;
    }
}
