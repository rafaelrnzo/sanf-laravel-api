<?php

namespace Sanf\Core\Modules\Survey\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sanf\Core\Modules\Survey\Services\SubmitCoreSurveySubmissionService;

class SubmitCoreSurveySubmissionJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function handle(SubmitCoreSurveySubmissionService $service)
    {
        return $service->execute($this->data);
    }
}
