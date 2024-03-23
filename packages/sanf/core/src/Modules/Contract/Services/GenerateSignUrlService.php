<?php

namespace Sanf\Core\Modules\Contract\Services;

use Carbon\Carbon;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Exceptions\ESignDocumentNotFoundException;
use Sanf\Core\Modules\Contract\Repositories\ESignRepositoryInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Integration\Enums\TekenAjaApiResponseErrorCodeEnum;
use Sanf\Integration\Exceptions\TekenAjaExternalApiException;
use Sanf\Integration\Exceptions\TekenAjaInvalidParameterRegistrationException;
use Sanf\Integration\Modules\TekenAja\TekenAjaApiClient;

final class GenerateSignUrlService implements ApplicationServiceInterface
{
    protected AuthModel $userRepository;
    protected ESignRepositoryInterface $eSignRepository;
    protected TekenAjaApiClient $client;

    public function __construct(
        AuthModel $userRepository,
        TekenAjaApiClient $client,
        ESignRepositoryInterface $eSignRepository
    ) {
        $this->userRepository = $userRepository;
        $this->client = $client;
        $this->eSignRepository = $eSignRepository;
    }

    /**
     * @param null $dto
     * @return object
     * @throws TekenAjaExternalApiException
     * @throws TekenAjaInvalidParameterRegistrationException
     * @throws UserNotFoundException
     */
    public function execute($dto = null): object
    {
        $user = $this->userRepository->newQuery()->find($dto->userId);
        if (!$user) {
            throw new UserNotFoundException();
        }

        $result = $this->client->generateSignUrl($dto->documentId, $dto->email);

        if ($result['code']) {
            $this->errorHandle($result['code'], $result['message']);
        }

        $assigneeDocument = $this->eSignRepository->findDocumentAssigneeByDocId($user->id, $dto->documentId);
        if (!$assigneeDocument) {
            throw new ESignDocumentNotFoundException();
        }
        $this->eSignRepository->updateDocumentAssignee($assigneeDocument->id, [
            'document_sign_url' => $result['data']['url'] ?? null,
            'updated_at' => Carbon::now(),
        ]);

        return (object) [
            'url' => $result['data']['url'] ?? null,
            'createdAt' => Carbon::now(),
        ];
    }

    private function errorHandle(string $code, $messages)
    {
        switch ($code) {
            case TekenAjaApiResponseErrorCodeEnum::INVALID_PARAMETER:
                $response = array_map(function ($item) {
                    return $item[0];
                }, $messages);
                throw new TekenAjaInvalidParameterRegistrationException(implode('|', $response));
                break;
            case TekenAjaApiResponseErrorCodeEnum::SYSTEM_FAILURE:
            default:
                throw new TekenAjaExternalApiException($messages);
        }
    }
}
