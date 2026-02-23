<?php

namespace Sanf\Core\Modules\Contract\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dtos\ESignDocumentCheckStatusRequestDto;
use Sanf\Core\Modules\Contract\Dtos\ESignDocumentCheckStatusResponseDto;
use Sanf\Core\Modules\Contract\Enums\ESignContractStatusEnum;
use Sanf\Core\Modules\Contract\Exceptions\ESignDocumentNotFoundException;
use Sanf\Core\Modules\Contract\Exceptions\ESignDocumentStatusCheckWindowExpiredException;
use Sanf\Core\Modules\Contract\Repositories\EloquentESignDocumentEncryptedRepository;
use Sanf\Core\Modules\Contract\Support\ESignHelper;

class ESignDocumentCheckStatusService implements ApplicationServiceInterface
{
    protected ESignDocumentSignCheckService $eSignDocumentSignCheckService;
    protected EloquentESignDocumentEncryptedRepository $eSignRepository;

    public function __construct(
        ESignDocumentSignCheckService $eSignDocumentSignCheckService,
        EloquentESignDocumentEncryptedRepository $eSignRepository
    )
    {
        $this->eSignDocumentSignCheckService = $eSignDocumentSignCheckService;
        $this->eSignRepository = $eSignRepository;
    }

    /**
     * @param ESignDocumentCheckStatusRequestDto $dto
     * @throws ESignDocumentNotFoundException
     * @return ESignDocumentCheckStatusResponseDto
     */
    public function execute($dto = null)
    {
        $eSignDocumentAssignment = $this->eSignRepository->findDocumentAssigneeByDocId($dto->userId, $dto->documentId);

        if (is_null($eSignDocumentAssignment) === true) {
            throw new ESignDocumentNotFoundException();
        }

        $currentStatusId = $eSignDocumentAssignment->status_id;

        $checked = false;
        $retryAvailableAt = null;
        $signedAt = null;

        $cacheKey = ESignHelper::checkSignStatusCacheKey($dto->userId, $dto->documentId);
        $cachedRetryAt = Cache::get($cacheKey);
        $now = Carbon::now();
        $intervalSeconds = ESignHelper::signStatusCheckInterval();

        if ($eSignDocumentAssignment->status_id !== ESignContractStatusEnum::COMPLETED) {
            if (is_int($cachedRetryAt) === true && $cachedRetryAt > $now->timestamp) {
                $retryAvailableAt = $cachedRetryAt;
            } else {
                $statusSignings = $this->eSignDocumentSignCheckService->execute($dto);
                $statusSigningCollection = collect($statusSignings);

                $assignee = $statusSigningCollection->first(fn ($statusSign) => strtolower($statusSign->email) == $eSignDocumentAssignment->email);

                if ($signDate = ($assignee->signDate ?? null)) {
                    $signedAt = Carbon::createFromFormat('Y-m-d H:i:s', $signDate, 'Asia/Jakarta')->timestamp;
                }

                if ($signedAt && ESignHelper::isStatusCheckWindowExpired(Carbon::now()->timestamp, $signedAt)) {
                    throw new ESignDocumentStatusCheckWindowExpiredException();
                }

                $currentStatusId = $this->eSignDocumentSignCheckService->assigneeStatusBySignStatus($assignee->signStatus, $currentStatusId);

                $checked = true;

                $retryAvailableAt = $now->timestamp + $intervalSeconds;
                Cache::put($cacheKey, $retryAvailableAt, $now->copy()->addSeconds($intervalSeconds));
            }
        } else {
            Cache::forget($cacheKey);
        }

        return new ESignDocumentCheckStatusResponseDto([
            'document_id' => $eSignDocumentAssignment->document_id,
            'previous_status_id' => $eSignDocumentAssignment->status_id,
            'current_status_id' => $currentStatusId,
            'checked' => $checked,
            'retry_available_at' => $retryAvailableAt,
            'signed_at' => $signedAt,
        ]);
    }
}
