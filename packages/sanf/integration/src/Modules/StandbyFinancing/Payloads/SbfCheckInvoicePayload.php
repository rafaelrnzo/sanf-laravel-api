<?php

namespace Sanf\Integration\Modules\StandbyFinancing\Payloads;

use Spatie\DataTransferObject\DataTransferObject;

class SbfCheckInvoicePayload extends DataTransferObject
{
    public string $nomor_invoice;
    public int $total_invoice;
    public string $noplafond;

    public static function fromValidated(array $payload): self
    {
        return new self([
            'nomor_invoice' => (string) ($payload['nomor_invoice'] ?? ''),
            'total_invoice' => (int) ($payload['total_invoice'] ?? 0),
            'noplafond' => (string) ($payload['no_plafond'] ?? $payload['noplafond'] ?? ''),
        ]);
    }
}
