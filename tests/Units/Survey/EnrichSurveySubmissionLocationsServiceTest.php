<?php

namespace Tests\Units\Survey;

use Mockery;
use PHPUnit\Framework\TestCase;
use Sanf\Core\Modules\Location\Services\ReverseGeocodeService;
use Sanf\Core\Modules\Survey\Services\EnrichSurveySubmissionLocationsService;

class EnrichSurveySubmissionLocationsServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testItEnrichesMissingAddressFromCoordinates(): void
    {
        $geocoder = Mockery::mock(ReverseGeocodeService::class);
        $geocoder->shouldReceive('execute')
            ->once()
            ->with(-6.123456, 106.123456)
            ->andReturn('Jl. Jenderal Sudirman, Jakarta');

        $service = new EnrichSurveySubmissionLocationsService($geocoder);
        $items = $service->execute([$this->item(null)]);

        $this->assertSame('Jl. Jenderal Sudirman, Jakarta', $items[0]['gps_address']);
    }

    public function testItPreservesAddressSentByMobile(): void
    {
        $geocoder = Mockery::mock(ReverseGeocodeService::class);
        $geocoder->shouldNotReceive('execute');

        $service = new EnrichSurveySubmissionLocationsService($geocoder);
        $items = $service->execute([$this->item('Address from mobile')]);

        $this->assertSame('Address from mobile', $items[0]['gps_address']);
    }

    public function testItReusesAddressForIdenticalCoordinates(): void
    {
        $geocoder = Mockery::mock(ReverseGeocodeService::class);
        $geocoder->shouldReceive('execute')
            ->once()
            ->andReturn('Shared address');

        $service = new EnrichSurveySubmissionLocationsService($geocoder);
        $items = $service->execute([$this->item(null), $this->item(null)]);

        $this->assertSame('Shared address', $items[0]['gps_address']);
        $this->assertSame('Shared address', $items[1]['gps_address']);
    }

    private function item(?string $address): array
    {
        return [
            'code' => 'FRONT_VIEW',
            'title' => 'Tampak Depan',
            'description' => 'Desc',
            'image_files' => ['photo.jpg'],
            'gps_lat' => -6.123456,
            'gps_lng' => 106.123456,
            'gps_address' => $address,
        ];
    }
}
