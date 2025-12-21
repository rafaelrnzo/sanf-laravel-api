<?php

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Sanf\Core\Modules\Payment\Payloads\PaymentInstallmentPayload;
use Sanf\Core\Modules\Payment\UseCases\ValidatePaymentInstallmentUseCase;
use Sanf\Integration\Modules\SanfCore\Enums\InstallmentListFilterTypeEnum;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClientV2;

class ValidatePaymentInstallmentUseCaseTest extends PHPUnitTestCase
{
    public function testExecuteReturnsInvalidInstallmentsWhenOnlyNextMonthHasCombination()
    {
        $apiClient = $this->createMock(SanfCoreApiClientV2::class);

        $currentMonth = $this->makeInstallmentListResponse([
            [
                'no_kontrak' => '21KON00001',
                'jatuh_tempo' => '10-12-2025',
            ],
            [
                'no_kontrak' => '21KON11111',
                'jatuh_tempo' => '05-12-2025',
            ],
            [
                'no_kontrak' => '21KON98123',
                'jatuh_tempo' => '10-12-2025',
            ],
            [
                'no_kontrak' => '21KON98111',
                'jatuh_tempo' => '18-12-2025',
            ],
        ]);

        $nextMonth = $this->makeInstallmentListResponse([
            [
                'no_kontrak' => '21KON22222',
                'jatuh_tempo' => '15-01-2026',
            ],
            [
                'no_kontrak' => '21KON11111',
                'jatuh_tempo' => '20-01-2026',
            ],
            [
                'no_kontrak' => '21KON98123',
                'jatuh_tempo' => '21-01-2026',
            ],
            [
                'no_kontrak' => '21KON98111',
                'jatuh_tempo' => '18-01-2026',
            ],
        ]);

        $apiClient->expects($this->exactly(2))
            ->method('getInstallmentList')
            ->withConsecutive(
                [1, SanfCoreApiClientV2::DEFAULT_LIMIT, InstallmentListFilterTypeEnum::CURRENT_MONTH],
                [1, SanfCoreApiClientV2::DEFAULT_LIMIT, InstallmentListFilterTypeEnum::NEXT_MONTH],
            )
            ->willReturnOnConsecutiveCalls($currentMonth, $nextMonth);

        $useCase = new ValidatePaymentInstallmentUseCase($apiClient);

        $payload = [
            new PaymentInstallmentPayload([
                'contract_no' => '21KON98123',
                'due_date' => 1768953600,
            ]),
            new PaymentInstallmentPayload([
                'contract_no' => '21KON22222',
                'due_date' => 1768435200,
            ]),
            new PaymentInstallmentPayload([
                'contract_no' => '21KON11111',
                'due_date' => 1768867200,
            ]),
            new PaymentInstallmentPayload([
                'contract_no' => '21KON11111',
                'due_date' => 1764892800,
            ]),
            new PaymentInstallmentPayload([
                'contract_no' => '21KON00001',
                'due_date' => 1765324800,
            ]),
            new PaymentInstallmentPayload([
                'contract_no' => '21KON98111',
                'due_date' => 1766016000,
            ]),
        ];

        $result = $useCase->execute($payload);

        $this->assertSame(
            [
                [
                    'contract_no' => '21KON22222',
                    'due_date' => 1768435200,
                ],
                [
                    'contract_no' => '21KON11111',
                    'due_date' => 1768867200,
                ],
                [
                    'contract_no' => '21KON11111',
                    'due_date' => 1764892800,
                ],
                [
                    'contract_no' => '21KON00001',
                    'due_date' => 1765324800,
                ],
                [
                    'contract_no' => '21KON98111',
                    'due_date' => 1766016000,
                ],
            ],
            array_map(fn($p) => $p->toArray(), $result->validInstallments),
            'Valid Insallments'
        );

        $this->assertSame(
            [
                [
                    'contract_no' => '21KON98123',
                    'due_date' => 1768953600,
                ],
            ],
            array_map(fn($p) => $p->toArray(), $result->invalidInstallments),
            'Invalid Installments'
        );
    }

    private function makeInstallmentListResponse(array $installments): object
    {
        $entities = array_map(
            fn(array $data) => (object) $data,
            $installments
        );

        return (object) ['data' => $entities];
    }
}
