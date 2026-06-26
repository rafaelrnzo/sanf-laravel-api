<?php

namespace Tests\Units\Survey;

use Illuminate\Support\Facades\Queue;
use Sanf\Core\Modules\Survey\Dtos\AddSurveySubmissionRequestDto;
use Sanf\Core\Modules\Survey\Jobs\ProcessSurveySubmissionJob;
use Sanf\Core\Modules\Survey\Services\AddSurveySubmissionService;

class AddSurveySubmissionServiceTest extends \TestCase
{
    public function testItQueuesPostProcessingWithoutPersistingInTheRequest(): void
    {
        Queue::fake();

        $dto = new AddSurveySubmissionRequestDto([
            'profileXid' => 'PROFILE123',
            'branchId' => 'BR001',
            'contractNo' => 'CN001',
            'picName' => null,
            'customerName' => null,
            'projectName' => null,
            'projectId' => 'PROJECT001',
            'segment' => null,
            'surveyDate' => '2026-06-23',
            'items' => [[
                'code' => 'FRONT_VIEW',
                'title' => 'Tampak Depan',
                'description' => 'Desc',
                'image_files' => ['photo.jpg'],
                'captured_at' => '2026-06-23 10:00:00',
                'gps_lat' => -6.123456,
                'gps_lng' => 106.123456,
                'gps_address' => null,
            ]],
            'xid' => null,
        ]);

        $this->assertTrue((new AddSurveySubmissionService())->execute($dto));

        Queue::assertPushed(ProcessSurveySubmissionJob::class, 1);
    }
}
