<?php

namespace Tests\Units\StandbyFinancing;

use PHPUnit\Framework\TestCase;
use Sanf\Core\Modules\StandbyFinancing\Exceptions\StandbyFinancingValidationException;
use Sanf\Core\Modules\StandbyFinancing\Models\StandbyFinancingApplicationModel;
use Sanf\Core\Modules\StandbyFinancing\Repositories\StandbyFinancingRepositoryInterface;
use Sanf\Core\Modules\StandbyFinancing\Services\StandbyFinancingBankAccountService;
use Sanf\Core\Modules\StandbyFinancing\Services\StandbyFinancingPlafondService;
use Sanf\Core\Modules\StandbyFinancing\Services\SubmitStandbyFinancingService;

class SubmitStandbyFinancingServiceTest extends TestCase
{
    public function testSubmitRejectsDuplicateInvoice(): void
    {
        $service = $this->makeService(['invoiceExists' => true]);

        $this->expectException(StandbyFinancingValidationException::class);
        $this->expectExceptionMessage('Invoice already exists');

        $service->submit('8624PROSM', $this->payload(), ['id' => 1, 'name' => 'user@example.test']);
    }

    public function testSubmitRejectsTenorOutsideSupplierRange(): void
    {
        $payload = $this->payload(['tenor' => 120]);
        $service = $this->makeService();

        $this->expectException(StandbyFinancingValidationException::class);
        $this->expectExceptionMessage('Tenor is outside');

        $service->submit('8624PROSM', $payload, ['id' => 1, 'name' => 'user@example.test']);
    }

    public function testSubmitRejectsAmountOverRemainingPlafond(): void
    {
        $service = $this->makeService(['plafond' => $this->plafond(['p_sisa' => '1000'])]);

        $this->expectException(StandbyFinancingValidationException::class);
        $this->expectExceptionMessage('exceeds remaining plafond');

        $service->submit('8624PROSM', $this->payload(), ['id' => 1, 'name' => 'user@example.test']);
    }

    public function testSubmitRejectsMissingRequiredDocument(): void
    {
        $payload = $this->payload(['dokuments' => [['doc_id' => '999', 'file_name' => 'invoice.pdf']]]);
        $service = $this->makeService();

        $this->expectException(StandbyFinancingValidationException::class);
        $this->expectExceptionMessage('Required document 002');

        $service->submit('8624PROSM', $payload, ['id' => 1, 'name' => 'user@example.test']);
    }

    public function testSubmitRejectsMoreThanOneSupplier(): void
    {
        $payload = $this->payload();
        $payload['supplier'][] = $payload['supplier'][0];
        $service = $this->makeService();

        $this->expectException(StandbyFinancingValidationException::class);
        $this->expectExceptionMessage('Exactly one supplier');

        $service->submit('8624PROSM', $payload, ['id' => 1, 'name' => 'user@example.test']);
    }

    public function testSubmitPersistsValidRequest(): void
    {
        $service = $this->makeService(['expectCreate' => true]);
        $result = $service->submit('8624PROSM', $this->payload(), ['id' => 1, 'name' => 'user@example.test']);

        $this->assertInstanceOf(StandbyFinancingApplicationModel::class, $result);
        $this->assertSame('SF26060001', $result->recap_id_b2b);
    }

    private function makeService(array $options = []): SubmitStandbyFinancingService
    {
        $plafond = $options['plafond'] ?? $this->plafond();
        $supplier = $plafond['supplier'][0];

        $plafondService = $this->getMockBuilder(StandbyFinancingPlafondService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['validateActiveSbf', 'findSupplier'])
            ->getMock();
        $plafondService->method('validateActiveSbf')->willReturn($plafond);
        $plafondService->method('findSupplier')->willReturn($supplier);

        $bankAccountService = $this->getMockBuilder(StandbyFinancingBankAccountService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['listForCustomer'])
            ->getMock();
        $bankAccountService->method('listForCustomer')->willReturn([
            [
                'cust_id' => '8624PROSM',
                'bank_id' => '0001101',
                'owner' => 'PT. SUKSES TUNGGAL MANDIRI',
                'provider' => 'BANK BCA',
                'account_number' => '883.059.1533',
                'is_default' => 'Y',
            ],
        ]);

        $repository = $this->createMock(StandbyFinancingRepositoryInterface::class);
        $repository->method('invoiceExistsInActiveRequest')->willReturn((bool) ($options['invoiceExists'] ?? false));
        $repository->method('sumLockedAmount')->willReturn((float) ($options['lockedAmount'] ?? 0));
        $repository->method('countApplicationsInMonth')->willReturn(0);

        if ($options['expectCreate'] ?? false) {
            $repository->expects($this->once())
                ->method('createSubmission')
                ->willReturnCallback(function ($application) {
                    $model = new StandbyFinancingApplicationModel();
                    $model->forceFill($application);

                    return $model;
                });
        }

        return new SubmitStandbyFinancingService($plafondService, $bankAccountService, $repository);
    }

    private function payload(array $overrides = []): array
    {
        return array_replace_recursive([
            'no_plafond' => '62505004136',
            'period_start' => '2026-05-03',
            'period_end' => '2026-05-29',
            'tenor' => 12,
            'supplier' => [
                [
                    'supplier_id' => '0000000073',
                    'total_invoice' => 2,
                    'total_amount' => 85000000,
                    'invoice_list' => [
                        [
                            'nomor_invoice' => 'INV/2026/05/0011',
                            'tanggal_invoice' => '2026-05-05',
                            'currency' => 'IDR',
                            'amount' => 45000000,
                        ],
                        [
                            'nomor_invoice' => 'INV/2026/05/0012',
                            'tanggal_invoice' => '2026-05-10',
                            'currency' => 'IDR',
                            'amount' => 40000000,
                        ],
                    ],
                ],
            ],
            'bank_account' => [
                'bank_id' => '0001101',
                'owner' => 'PT. SUKSES TUNGGAL MANDIRI',
                'provider' => 'BANK BCA',
                'account_number' => '883.059.1533',
                'total_amount' => '85000000',
            ],
            'dokuments' => [
                [
                    'doc_id' => '002',
                    'file_path' => 'uploads/sbf/2026/05/faktur-pajak.pdf',
                    'file_name' => 'faktur-pajak.pdf',
                ],
            ],
        ], $overrides);
    }

    private function plafond(array $overrides = []): array
    {
        return array_replace([
            'no_plafond' => '62505004136',
            'cust_id' => '8624PROSM',
            'p_code' => '004',
            'p_total' => '10000000000',
            'p_terpakai' => '0',
            'p_invoice_ongoing' => '0',
            'p_sisa' => '10000000000',
            'exp_date' => '2026-06-19',
            'supplier' => [
                [
                    'reg_no' => '3682600002',
                    'supplier_id' => '0000000073',
                    'nama_supplier' => 'RIZKI WK PRATAMA',
                    'masa_aktif' => '12',
                    'tenor_pencairan_min' => '1',
                    'tenor_pencairan_max' => '90',
                    'tipe_tenor' => 'Daily',
                    'curr_id' => 'IDR',
                ],
            ],
        ], $overrides);
    }
}
