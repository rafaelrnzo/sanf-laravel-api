<?php


namespace Sanf\Core\Modules\Prepayment\Services;

use Dompdf\Dompdf;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Enums\CurrencyTypeEnum;
use Sanf\Core\Modules\Prepayment\Dtos\GetPdfPrepaymentSimulationRequestDto;


final class GetPdfPrepaymentSimulationService extends PrepaymentSubmissionByUserService implements ApplicationServiceInterface
{
    /**
     * @param GetPdfPrepaymentSimulationRequestDto $dto
     * @return mixed|string|null
     * @throws \Throwable
     */
    public function execute($dto = null)
    {
        $user = $this->findUserOrFail($dto->userId);
        $currencySymbol = (new CurrencyTypeEnum($dto->currencyType))->getSymbol();

        $lineItems = [
            'nomor_kontrak' => $dto->contractNo,
            'tanggal_pelunasan_dipercepat' => $dto->prepaymentDate->formatLocalized('%A, %d %B %Y'),
            '<hr style="border: 1px solid rgba(3, 37, 126, 0.08); margin: 5px 0;">', // let key as number for separator
        ];

        foreach ($dto->items as $item) {
            $lineItems[$item->description] = $currencySymbol . ' ' . number_format($item->amount, 0, ',', '.');
        }
        $lineItems[] = '<hr style="border: 1px solid rgba(3, 37, 126, 0.08); margin: 5px 0;">';
        $lineItems['total_pelunasan_dipercepat'] = $currencySymbol . ' ' . number_format($dto->totalPrepayment, 0, ',', '.');

        $template = view('vendor/pdf/PDFView', ['contents' => [
            'images' => [
                0 => $this->toBase64('assets/png/sanf-logo-blue.png'),
                1 => $this->toBase64('assets/png/sanf-tagline.png'),
            ],
            'openingSentence' => 'Selamat siang ' . $user->full_name . ', berikut kami lampirkan hasil perhitungan simulasi prepayment untuk pembiayaan Anda:',
            'email' => $user->username,
            'data' => $lineItems,
            'closingSentence' => 'Hasil perhitungan simulasi pelunasan dipercepat ini bersifat tidak mengikat.'
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
