<?php

namespace Sanf\Api\Modules\Product;

use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Modules\Product\ListProductService;
use Spatie\Fractalistic\ArraySerializer;

class ListProductController extends RestApiController
{
    protected $service;

    public function __construct(ListProductService $service)
    {
        parent::__construct();

        $this->service = $service;
    }

    public function process(Request $request)
    {
        // validate request;
        $inputs = $this->validating($request);

        // set property;
        $property = [];
        if (isset($inputs['limit'])) $property += ['limit' => (int) $inputs['limit']];
        if (isset($inputs['offset'])) $property += ['offset' => (int) $inputs['offset']];
        if (isset($inputs['id'])) $property += ['id' => (int) $inputs['id']];
        if (isset($inputs['title'])) $property += ['title' => $inputs['title']];

        // set up dto;
        $dto = new ListProductRequestDto($property);

        // run service;
        $result = $this->service->execute($dto);

        // sent response;
        return fractal($result->list, ListProductTransformer::class)->serializeWith(new ArraySerializer());

    }

    private function validating(Request $request)
    {
        $rules = [
            'id' => [
                'nullable',
                'max:20',
            ],
            'title' => [
                'nullable', 'string',
                'max:255',
            ],
            'limit' => [
                'nullable',
                'integer',
            ],
            'offset' => [
                'nullable',
                'integer',
            ],
        ];

        return $this->validate($request, $rules);
    }
}
