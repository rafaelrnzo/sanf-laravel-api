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
            'originName' => 'DESIGNOPS-JACAProductDesigner-220222-1531.pdf',
            'fileName' => 'tMZ1vq1fT53MNhbTCAgTlbcXh41qsdksLDK6KkRu.pdf',
            'path' => 'temp/tMZ1vq1fT53MNhbTCAgTlbcXh41qsdksLDK6KkRu.pdf',
            'url' => file_get_temp_url('temp/tMZ1vq1fT53MNhbTCAgTlbcXh41qsdksLDK6KkRu.pdf'),
        ]);

        return fractal($result, PrivateAssetFileSimpleTransformer::class);
    }

    public function sendPaymentAccelerationDocument(Guard $auth)
    {
        return $this->responseOk();
    }
}
