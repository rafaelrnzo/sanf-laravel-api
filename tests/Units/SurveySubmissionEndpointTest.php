<?php

namespace Tests\Units;

use Mockery;
use Sanf\Core\Modules\Survey\Dtos\AddSurveySubmissionRequestDto;
use Sanf\Core\Modules\Survey\Services\AddSurveySubmissionService;
use TestCase;

class SurveySubmissionEndpointTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testAddSurveySubmissionAcceptsGpsFieldsInItems()
    {
        $this->withoutMiddleware();

        $service = Mockery::mock(AddSurveySubmissionService::class);
        $service
            ->shouldReceive('execute')
            ->once()
            ->with(Mockery::on(function ($dto) {
                if (!$dto instanceof AddSurveySubmissionRequestDto) {
                    return false;
                }

                $this->assertIsArray($dto->items);
                $this->assertCount(1, $dto->items);

                $item = $dto->items[0];

                $this->assertSame(-6.123456, $item['gps_lat']);
                $this->assertSame(106.123456, $item['gps_lng']);
                $this->assertSame('Jl. Jenderal Sudirman No.1, Jakarta Pusat', $item['gps_address']);

                return true;
            }));

        $this->app->instance(AddSurveySubmissionService::class, $service);

        $payload = [
            'profile_xid' => 'PROFILE123',
            'branch_id' => 'BR001',
            'contract_no' => 'CN001',
            'pic_name' => 'PIC',
            'customer_name' => 'Customer',
            'project_name' => 'Project',
            'segment' => 'Segment',
            'items' => [
                [
                    'code' => 'FRONT_VIEW',
                    'title' => 'Tampak Depan',
                    'description' => 'Desc',
                    'image_files' => ['uploaded-photo-1.jpg', 'uploaded-photo-2.jpg'],
                    'captured_at' => '2026-05-20 15:00:00',
                    'gps_lat' => -6.123456,
                    'gps_lng' => 106.123456,
                    'gps_address' => 'Jl. Jenderal Sudirman No.1, Jakarta Pusat',
                ],
            ],
        ];

        $this->post('/v1/users/survey-submissions', $payload);

        $this->seeStatusCode(200);
        $this->seeJsonStructure(['message', 'data']);
    }

    public function testAddSurveySubmissionRejectsInvalidGpsLat()
    {
        $this->withoutMiddleware();

        $service = Mockery::mock(AddSurveySubmissionService::class);
        $service->shouldNotReceive('execute');
        $this->app->instance(AddSurveySubmissionService::class, $service);

        $payload = [
            'profile_xid' => 'PROFILE123',
            'branch_id' => 'BR001',
            'contract_no' => 'CN001',
            'items' => [
                [
                    'code' => 'FRONT_VIEW',
                    'title' => 'Tampak Depan',
                    'description' => 'Desc',
                    'image_files' => ['uploaded-photo-1.jpg'],
                    'captured_at' => '2026-05-20 15:00:00',
                    'gps_lat' => 'not-a-number',
                ],
            ],
        ];

        $this->post('/v1/users/survey-submissions', $payload);

        $this->seeStatusCode(422);
    }
}

