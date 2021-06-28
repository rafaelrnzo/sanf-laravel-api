<?php


namespace Sanf\Api\Modules\Common;


use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestController;
use Sanf\Core\Modules\Common\UploadFileService;

class UploadFileController extends RestController
{

    protected $service;

    public function __construct(UploadFileService $service)
    {
        parent::__construct();

        $this->service = $service;
    }

    public function process(Request $request)
    {
        // validate request;
        $inputs = $this->validating($request);

        // set upload file dto;
        $dto = new UploadFileRequestDto([
            'file' => $inputs['file'],
            'type' => (int)$inputs['asset_type']
        ]);

        // run service;
        $result = $this->service->execute($dto);

        // sent response;
        return fractal($result, UploadFileTransformer::class);
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
