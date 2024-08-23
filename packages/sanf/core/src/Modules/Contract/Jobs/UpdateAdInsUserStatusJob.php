<?php

namespace Sanf\Core\Modules\Contract\Jobs;

use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Sanf\Core\Modules\Contract\Enums\ESignRegistrationStatusEnum;
use Sanf\Core\Modules\Contract\Events\ESignAdsInsRegisterMailEvent;
use Sanf\Core\Modules\Contract\Events\ESignAdsInsRegisterNotificationEvent;
use Sanf\Core\Modules\Contract\Repositories\EloquentESignDocumentRepository;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class UpdateAdInsUserStatusJob implements ShouldQueue
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
        EloquentESignDocumentRepository $eSignDocumentRepository,
        SanfCoreApiClient $sanfCoreClient
    ) {
        $dto = $this->request;

        DB::transaction(function () use ($dto, $eSignDocumentRepository, $sanfCoreClient) {
            try {
                $adInsUser = $eSignDocumentRepository->findUserByEmail($dto->email);
                if ($adInsUser) {
                    if ($adInsUser->status_id !== ESignRegistrationStatusEnum::COMPLETE) {

                        $sanfCoreClient->updateESignUserStatus($adInsUser->email);

                        $eSignDocumentRepository->updateUser($adInsUser->id, [
                            'status_id' => ESignRegistrationStatusEnum::COMPLETE,
                            'updated_at' => CarbonImmutable::now(),
                        ]);

                        $bodyEmail = (object) [
                            'email' => $adInsUser->email,
                            'name' => $adInsUser->full_name,
                        ];

                        event(new ESignAdsInsRegisterMailEvent($bodyEmail));
                        event(new ESignAdsInsRegisterNotificationEvent($adInsUser->user_id));
                    }
                }
            } catch (Exception $exception) {
                report($exception);
            }
        });
    }
}
