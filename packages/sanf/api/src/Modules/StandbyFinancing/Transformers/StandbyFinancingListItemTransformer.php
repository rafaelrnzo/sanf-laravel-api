<?php

namespace Sanf\Api\Modules\StandbyFinancing\Transformers;

use Carbon\CarbonImmutable;
use League\Fractal\TransformerAbstract;
use Sanf\Core\Modules\StandbyFinancing\Models\StandbyFinancingApplicationModel;

final class StandbyFinancingListItemTransformer extends TransformerAbstract
{
    private int $currentRowNumber;

    public function __construct(int $startRowNumber = 1)
    {
        $this->currentRowNumber = $startRowNumber;
    }

    public function transform(StandbyFinancingApplicationModel $application): array
    {
        $rn = (string) $this->currentRowNumber;
        $this->currentRowNumber++;

        return [
            'rn' => $rn,
            'recap_id_b2b' => $application->recap_id_b2b,
            'period_start' => $this->dateTime($application->period_start),
            'period_end' => $this->dateTime($application->period_end),
            'date_recap' => $this->dateTime($application->created_at),
            'total_invoice' => (string) $application->total_invoice,
            'total_amount' => $this->amount($application->total_amount),
            'state_code' => (string) $application->state_code,
        ];
    }

    private function dateTime($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        return CarbonImmutable::parse($value)->format('Y-m-d H:i:s');
    }

    private function amount($value): string
    {
        $amount = (float) $value;

        if (floor($amount) === $amount) {
            return (string) (int) $amount;
        }

        return (string) $amount;
    }
}
