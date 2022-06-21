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
            '1' => 'image/png,image/jpeg,image/jpg,image/svg'
        ];
        $keys = array_keys($types);
        $string = implode(',', $keys);

        $rules = [
            'file' => [
                'required',
                'image',
                "mimetypes:{$types[$request->get('asset_type')]}",
                'max:5000'
            ],
            'asset_type' => [
                'required',
                "in:{$string}",
                'max:16'
            ]
        ];

        return $this->validate($request, $rules);
    }
}
