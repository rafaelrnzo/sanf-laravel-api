<?php

namespace Sanf\Core\Modules\Financing\Services;

use Dompdf\Dompdf;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Financing\Dto\FinancingSimulationPdfDto;
use Sanf\Core\Modules\Financing\Enums\FinancingMethodEnum;

class FinancingSimulationPdfGeneratorService extends FinancingByUserService implements ApplicationServiceInterface
{
    /**
     * @param FinancingSimulationPdfDto $dto
     * @return mixed|string|null
     * @throws \Throwable
     */
    public function execute($dto = null)
    {
        $user = $this->findUserOrFail($dto->userId);

        $financingMethod = $this->financingMethodRepository->findById($dto->financingMethodId);

        /* @var FinancingSimulationPdfDto $dto */
        switch ($financingMethod->id) {
            case FinancingMethodEnum::SEWA_PEMBIAYAAN:
            case FinancingMethodEnum::JUAL_SEWA_BALIK:
                $bodyContent = [
                    'cara_pembiayaan' => $financingMethod->name,
                    'harga_unit' => 'Rp. ' . number_format($dto->unitAmount, 0, ',', '.'),
                    'security_deposit_(_uang_muka_)' => 'Rp. ' . number_format($dto->downPaymentAmount, 0, ',', '.'),
                    'total_pokok_hutang' => 'Rp. ' . number_format($dto->totalCreditAmount, 0, ',', '.'),
                    'tipe_angsuran_pertama' => $dto->firstInstallmentType,
                    'bunga_(_efektif_)' => $dto->interestPercentage . '%',
                    'tenor' => $dto->tenor . ' Bulan',
                    'total_biaya_-_biaya' => '',
                    'biaya_asuransi_tahun_pertama' => 'Rp. ' . number_format($dto->firstYearInsuranceAmount, 0, ',', '.'),
                    'biaya_admin' => 'Rp. ' . number_format($dto->adminFeeAmount, 0, ',', '.'),
                    'biaya_provisi' => 'Rp. ' . number_format($dto->provisionAmount, 0, ',', '.'),
                ];
                $summaryContent = [
                    'angsuran_pertama' => 'Rp. ' . number_format($dto->firstInstallmentAmount, 0, ',', '.'),
                    'total_angsuran_pertama' => 'Rp. ' . number_format($dto->totalFirstPaymentAmount, 0, ',', '.'),
                    'angsuran_perbulan' => 'Rp. ' . number_format($dto->installmentPerMonth, 0, ',', '.'),
                ];
                break;
            case FinancingMethodEnum::PEMBELIAN_ANGSURAN:
                $bodyContent = [
                    'cara_pembiayaan' => $financingMethod->name,
                    'harga_unit' => 'Rp. ' . number_format($dto->unitAmount, 0, ',', '.'),
                    'dp_(_uang_muka_)' => 'Rp. ' . number_format($dto->downPaymentAmount, 0, ',', '.'),
                    'total_pokok_hutang' => 'Rp. ' . number_format($dto->totalCreditAmount, 0, ',', '.'),
                    'tipe_angsuran_pertama' => $dto->firstInstallmentType,
                    'bunga_(_efektif_)' => $dto->interestPercentage . '%',
                    'tenor' => $dto->tenor . ' Bulan',
                    'total_biaya_-_biaya' => '',
                    'biaya_asuransi_tahun_pertama' => 'Rp. ' . number_format($dto->firstYearInsuranceAmount, 0, ',', '.'),
                    'biaya_admin' => 'Rp. ' . number_format($dto->adminFeeAmount, 0, ',', '.'),
                    'biaya_provisi' => 'Rp. ' . number_format($dto->provisionAmount, 0, ',', '.'),
                ];
                $summaryContent = [
                    'angsuran_pertama' => 'Rp. ' . number_format($dto->firstInstallmentAmount, 0, ',', '.'),
                    'total_angsuran_pertama' => 'Rp. ' . number_format($dto->totalFirstPaymentAmount, 0, ',', '.'),
                    'angsuran_perbulan' => 'Rp. ' . number_format($dto->installmentPerMonth, 0, ',', '.'),
                ];
                break;
            case FinancingMethodEnum::ANJAK_PIUTANG_PEMBERIAN:
            case FinancingMethodEnum::ANJAK_PIUTANG_TANPA_PEMBERIAN:
                $bodyContent = [
                    'cara_pembiayaan' => $financingMethod->name,
                    'total_invoice' => 'Rp. ' . number_format($dto->invoiceAmount, 0, ',', '.'),
                    'tenor_(_hari_)' => $dto->tenor,
                    'bunga_(_efektif_)' => $dto->interestPercentage . '%',
                    'nilai_diskonto' => 'Rp. ' . number_format($dto->diskontoAmount, 0, ',', '.'),
                    'nilai_retensi' => 'Rp. ' . number_format($dto->retentionAmount, 0, ',', '.'),
                ];
                $summaryContent = [
                    'nilai_pencairan_dana' => 'Rp. ' . number_format($dto->disbursementAmount, 0, ',', '.'),
                ];
                break;
            case FinancingMethodEnum::FASILITAS_MODAL_USAHA:
            default:
                $bodyContent = [
                    'cara_pembiayaan' => $financingMethod->name,
                    'total_pembiayaan' => 'Rp. ' . number_format($dto->financingAmount, 0, ',', '.'),
                    'bunga_(_efektif_)' => $dto->interestPercentage . '%',
                    'tenor' => $dto->tenor . ' Bulan',
                ];
                $summaryContent = [
                    'angsuran_perbulan' => 'Rp. ' . number_format($dto->installmentPerMonth, 0, ',', '.'),
                ];
                break;
        }

        $template = view('core::pdf/simulation', ['contents' => [
            'images' => [
                0 => $this->toBase64(asset('assets/png/sanf-logo-blue.png')),
                1 => $this->toBase64(asset('assets/png/sanf-tagline.png')),
            ],
            'openingSentence' => 'Selamat siang ' . $user->full_name . ', berikut kami lampirkan hasil perhitungan simulasi pengajuan pembiayaan anda',
            'email' => $user->username,
            'data' => $bodyContent,
            'summary' => $summaryContent,
            'methodId' => $financingMethod->id,
            'closingSentence' => 'Hasil perhitungan ini bersifat tidak mengikat',
        ]])->render();

        $pdf = new Dompdf();
        $pdf->loadHtml($template);
        $pdf->setPaper('A4', 'potrait');
        $pdf->render();

        return $pdf->output();
    }

    protected function toBase64($assetPath)
    {
        $type = pathinfo($assetPath, PATHINFO_EXTENSION);
        $data = file_get_contents($assetPath);

        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
}
