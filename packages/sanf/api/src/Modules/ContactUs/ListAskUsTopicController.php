<?php


namespace Sanf\Api\Modules\ContactUs;


use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestController;
use Sanf\Core\Modules\ContactUs\ListAskUsTopicService;

class ListAskUsTopicController extends RestController
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
        if (isset($inputs['limit'])) $property += ['limit' => (int)$inputs['limit']];
        if (isset($inputs['offset'])) $property += ['offset' => (int)$inputs['offset']];

        // set up dto;
        $dto = new ListAskUsTopicRequestDto($property);

        // run service;
        $result = $this->service->execute($dto);

        // sent response;
        return fractal($result->list, ListAskUsTopicTransformer::class);
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
