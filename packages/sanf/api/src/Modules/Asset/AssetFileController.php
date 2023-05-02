<?php


namespace Sanf\Api\Modules\Asset;


use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Modules\Asset\AssetUploadRequestDto;
use Sanf\Core\Modules\Asset\UploadAssetService;

class AssetFileController extends RestApiController
{
    public function upload(Request $request, UploadAssetService $service)
    {
        // validate request;
        $inputs = $this->validating($request);

        // set upload file dto;
        $dto = new AssetUploadRequestDto([
            'file' => $inputs['file'],
            'type' => (int)$inputs['asset_type']
        ]);

        // run service;
        $result = $service->execute($dto);

        // sent response;
        return fractal($result, PrivateAssetFileSimpleTransformer::class);
    }

    private function validating(Request $request)
    {
        $types = [
            '1' => 'image/png,image/jpeg,image/jpg,image/svg',
            '2' => 'image/png,image/jpeg,image/jpg',
            '3' => 'image/png,image/jpeg,image/jpg,image/tiff,application/pdf',
        ];
        $maxSizes = [
            '1' => 5000,
            '2' => 1000,
            '3' => 15000,
        ];
        $keys = array_keys($types);
        $string = implode(',', $keys);

        $rules = [
            'file' => [
                'required',
                'file',
                "mimetypes:{$types[$request->get('asset_type')]}",
                "max:{$maxSizes[$request->get('asset_type')]}"
            ],
            'asset_type' => [
                'required',
                "in:{$string}",
            ]
        ];

        return $this->validate($request, $rules);
    }
}
