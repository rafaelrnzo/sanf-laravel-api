<?php


namespace Sanf\Api\Modules\Common;


use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestController;
use Sanf\Core\Modules\Common\UploadImageService;

class UploadImageController extends RestController
{

    protected $service;

    public function __construct(UploadImageService $service)
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
            'type' => (int)$inputs['type']
        ]);

        // run service;
        $result = $this->service->run($dto);

        // sent response;
        return fractal($result, UploadFileTransformer::class);
    }

    private function validating(Request $request)
    {
        $keys = array_keys([
            1 => 'temp'
        ]);
        $string = implode(',', $keys);

        $rules = [
            'file' => [
                'required',
                'image',
                'mimetypes:image/png,image/jpeg,image/jpg,image/svg',
                'max:5000'
            ],
            'type' => [
                'required',
                "in:{$string}",
                'max:16'
            ]
        ];

        return $this->validate($request, $rules);
    }
}