<?php

namespace Sanf\Core\Modules\Survey\Services;

use Sanf\Core\Modules\Location\Services\ReverseGeocodeService;

class EnrichSurveySubmissionLocationsService
{
    private ReverseGeocodeService $reverseGeocodeService;

    public function __construct(ReverseGeocodeService $reverseGeocodeService)
    {
        $this->reverseGeocodeService = $reverseGeocodeService;
    }

    public function execute(array $items): array
    {
        $resolvedAddresses = [];

        foreach ($items as &$item) {
            if (!$this->needsAddressEnrichment($item)) {
                continue;
            }

            try {
                $coordinates = $item['gps_lat'] . ',' . $item['gps_lng'];

                if (!array_key_exists($coordinates, $resolvedAddresses)) {
                    $resolvedAddresses[$coordinates] = $this->reverseGeocodeService->execute(
                        (float) $item['gps_lat'],
                        (float) $item['gps_lng']
                    );
                }

                if ($resolvedAddresses[$coordinates] !== null) {
                    $item['gps_address'] = $resolvedAddresses[$coordinates];
                }
            } catch (\Throwable $exception) {
                report($exception);
            }
        }
        unset($item);

        return $items;
    }

    private function needsAddressEnrichment(array $item): bool
    {
        $addressMissing = !isset($item['gps_address']) || trim((string) $item['gps_address']) === '';
        $hasLatitude = array_key_exists('gps_lat', $item) && $item['gps_lat'] !== null;
        $hasLongitude = array_key_exists('gps_lng', $item) && $item['gps_lng'] !== null;

        return $addressMissing && $hasLatitude && $hasLongitude;
    }
}
