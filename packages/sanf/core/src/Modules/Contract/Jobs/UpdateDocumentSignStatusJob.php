<?php

namespace Sanf\Core\Modules\Contract\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Sanf\Core\Modules\Contract\Enums\ESignContractStatusEnum;
use Sanf\Core\Modules\Contract\Exceptions\ESignDocumentNotFoundException;
use Sanf\Core\Modules\Contract\Repositories\EloquentESignDocumentRepository;
use Sanf\Core\Modules\Contract\Services\ESignDocumentSignCheckService;

class UpdateDocumentSignStatusJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected object $request;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    public function handle(
        ESignDocumentSignCheckService $eSignDocumentSignCheckService,
        EloquentESignDocumentRepository $eSignRepository
    ) {
        $dto = $this->request;

        $eSignDocument = $eSignRepository->findDocumentByDocId($dto->documentId);
        if (is_null($eSignDocument) === true) {
            throw new ESignDocumentNotFoundException();
        }

        if ($eSignDocument->status_id !== ESignContractStatusEnum::COMPLETED) {
            DB::transaction(function () use ($dto, $eSignDocumentSignCheckService) {
                $eSignDocumentSignCheckService->execute($dto);
            });
        }
    }
}
