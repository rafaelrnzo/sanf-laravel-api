<?php

namespace Sanf\Api\Modules\Plafond\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Api\Modules\Asset\PrivateAssetFileSimpleTransformer;
use Sanf\Core\Modules\Asset\AssetUploadResultDto;

class PlafondDocumentController extends RestApiController
{
    public function downloadPaymentAccelarationDocument(Guard $auth)
    {
        $result = new AssetUploadResultDto([
            'originName' => 'FORM PERCEPATAN PEMBAYARAN.doc.pdf',
            'fileName' => 'f0R7HEzDXi6OL4INb55bPBRama4UjEYeusPoAUfG.pdf',
            'path' => 'temp/f0R7HEzDXi6OL4INb55bPBRama4UjEYeusPoAUfG.pdf',
            'url' => file_get_temp_url('temp/f0R7HEzDXi6OL4INb55bPBRama4UjEYeusPoAUfG.pdf'),
        ]);

        return fractal($result, PrivateAssetFileSimpleTransformer::class);
    }

    public function sendPaymentAccelarationDocument(Guard $auth)
    {
        return $this->responseOk();
    }

    public function printPaymentAccelarationDocument(
        Guard $auth,
        string $xid,
        string $plafond_xid,
        Request $request
    ) {
        $this->validate($request, [
            'company_name' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'document_no' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'document_date' => ['required', 'date_format:Y-m-d'],
            'first_signer.full_name' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'first_signer.position' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'second_signer.full_name' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'second_signer.position' => ['required', 'string', 'max:128', 'regex:/^[0-9a-zA-Z-_\/()@,.\h]+$/'],
            'save' => ['required', 'boolean'],
        ]);

        $result = new AssetUploadResultDto([
            'originName' => 'FORM PERCEPATAN PEMBAYARAN.doc.pdf',
            'fileName' => 'f0R7HEzDXi6OL4INb55bPBRama4UjEYeusPoAUfG.pdf',
            'path' => 'temp/f0R7HEzDXi6OL4INb55bPBRama4UjEYeusPoAUfG.pdf',
            'url' => file_get_temp_url('temp/f0R7HEzDXi6OL4INb55bPBRama4UjEYeusPoAUfG.pdf'),
        ]);

        return fractal($result, PrivateAssetFileSimpleTransformer::class);
    }
}
