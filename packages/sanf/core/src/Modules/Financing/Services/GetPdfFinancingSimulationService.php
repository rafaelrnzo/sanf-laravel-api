<?php


namespace Sanf\Core\Modules\Financing\Services;

use Dompdf\Dompdf;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Financing\Dto\PdfFinancingSimulationRequestDto;
use Sanf\Core\Modules\Financing\Enums\FinancingMethodEnum;


class GetPdfFinancingSimulationService extends FinancingByUserService implements ApplicationServiceInterface
{
    /**
     * @param PdfFinancingSimulationRequestDto $dto
     * @return mixed|string|null
     * @throws \Throwable
     */
    public function execute($dto = null)
    {
        $user = $this->findUserOrFail($dto->user_id);
        $isAnjak = in_array($dto->financing_method_id, [
            FinancingMethodEnum::ANJAK_PIUTANG_PEMBERIAN,
            FinancingMethodEnum::ANJAK_PIUTANG_TANPA_PEMBERIAN,
        ]);
        if ($dto->financing_method_id == FinancingMethodEnum::FASILITAS_MODAL_USAHA) {
            $bodyContent = [
                'cara_pembiayaan' => $dto->financing_method_name,
                'nilai_pembiayaan' => 'Rp. ' . number_format($dto->financing_amount, 0, ',', '.'),
                'tenor' => $dto->tenor_in_month . ' Bulan',
                'suku_bunga' => $dto->interest_rate_percentage . '%',
                'angsuran_per_bulan' => 'Rp. ' . number_format($dto->installment_per_month, 0, ',', '.'),
            ];
        } else if ($isAnjak) {
            $bodyContent = [
                'cara_pembiayaan' => $dto->financing_method_name,
                'total_invoice' => 'Rp. ' . number_format($dto->financing_amount, 0, ',', '.'),
                '%_retensi' => $dto->down_payment_percentage . '%',
                'nilai_retensi' => 'Rp. ' . number_format($dto->down_payment_amount, 0, ',', '.'),
                'tenor' => $dto->tenor_in_month . ' Bulan',
                'nilai_anjak_piutang' => 'Rp. ' . number_format($dto->financing_amount - $dto->down_payment_amount, 0, ',', '.'),
                'diskonto' => $dto->interest_rate_percentage . '%',
                'nilai_angsuran' => 'Rp. ' . number_format($dto->installment_per_month, 0, ',', '.'),
            ];
        } else {
            $bodyContent = [
                'cara_pembiayaan' => $dto->financing_method_name,
                'harga_unit_pembiayaan' => 'Rp. ' . number_format($dto->financing_amount, 0, ',', '.'),
                '%_uang_muka_(%_dp)' => $dto->down_payment_percentage . '%',
                'uang_muka(dp)' => 'Rp. ' . number_format($dto->down_payment_amount, 0, ',', '.'),
                'tenor' => $dto->tenor_in_month . ' Bulan',
                'nilai_pembiayaan' => 'Rp. ' . number_format($dto->financing_amount - $dto->down_payment_amount, 0, ',', '.'),
                'suku_bunga' => $dto->interest_rate_percentage . '%',
                'angsuran_perbulan' => 'Rp. ' . number_format($dto->installment_per_month, 0, ',', '.'),
            ];
        }

        $template = view('vendor/pdf/PDFView', ['contents' => [
            'images' => [
                0 => $this->toBase64(asset('assets/png/sanf-logo-blue.png')),
                1 => $this->toBase64(asset('assets/png/sanf-tagline.png')),
            ],
            'openingSentence' => 'Selamat siang '. $user->full_name. ', berikut kami lampirkan hasil perhitungan simulasi pengajuan pembiayaan anda',
            'email' => $user->username,
            'data' => $bodyContent,
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
