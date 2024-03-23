<?php

namespace Sanf\Api\Modules\ContactUs;

use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Modules\ContactUs\ListAskUsTopicService;

class ListAskUsTopicController extends RestApiController
{
    protected $service;

    public function __construct(ListAskUsTopicService $service)
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

        // set up dto;
        $dto = new ListAskUsTopicRequestDto($property);

        // run service;
        $result = $this->service->execute($dto);

        $collection = collect($result->list);
        foreach ($collection as $key => $item) {
            if ($item->id == 4) { //TODO CONST
                $collection->prepend($collection->pull($key));
            }
        }

        // sent response;
        return fractal($collection, ListAskUsTopicTransformer::class);
    }

    private function validating(Request $request)
    {
        $rules = [
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
