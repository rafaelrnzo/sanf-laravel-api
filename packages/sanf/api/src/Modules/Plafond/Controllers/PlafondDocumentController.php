<?php

namespace Sanf\Api\Modules\Plafond\Controllers;

use Illuminate\Contracts\Auth\Guard;
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
}
