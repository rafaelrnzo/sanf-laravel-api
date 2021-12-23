<?php


namespace Sanf\Core\Modules\Financing\Services;

use Dompdf\Dompdf;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Financing\Dto\PdfFinancingSimulationRequestDto;


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
        $template = view('vendor/pdf/PDFView', ['contents' => [
            'images' => [
                0 => $this->toBase64('assets/png/sanf-logo-blue.png'),
                1 => $this->toBase64('assets/png/sanf-tagline.png'),
            ],
            'openingSentence' => 'Selamat siang '. $user->full_name. ', berikut kami lampirkan hasil perhitungan simulasi pengajuan pembiayaan anda',
            'email' => $user->username,
            'data' => [
                'cara_pembiayaan' => $dto->financing_method_name,
                'total_pembiayaan' => 'Rp. ' . number_format($dto->financing_amount, 0, ',', '.'),
                'uang_muka' => 'Rp. ' . number_format($dto->down_payment_amount, 0, ',', '.'),
                'persen_dp' => $dto->down_payment_percentage . '%',
                'tenor' => $dto->tenor_in_month . ' Bulan',
                'angsuran_perbulan' => 'Rp. ' . number_format($dto->installment_per_month, 3, ',', '.'),
                'suku_bunga' => $dto->interest_rate_percentage . '%'
            ],
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
