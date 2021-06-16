<?php


namespace Sanf\Api\Modules\ContactUs;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use NbsPhp\Core\Controllers\RestController;
use Sanf\Core\Modules\ContactUs\AskUsSubmitService;

class AskUsSubmitController extends RestController
{
    protected $service;

    public function __construct(AskUsSubmitService $service)
    {
        parent::__construct();

        $this->service = $service;
    }

    public function process(Request $request)
    {
        // validate request;
        $property = $this->validating($request);
        if (isset($inputs['images']))
            $property += ['images' => $inputs['images']];

        // set up dto;
        $dto = new AskUsSubmitRequestDto($property);

        // handle transaction;
        DB::transaction(function () use ($dto){
            // run service;
            $this->service->run($dto);
        });

        // sent response;
        return $this->responseOk();
    }


    private function validating(Request $request)
    {
        $rules = [
            'topic_id' => [
                'required'
            ],
            'title' => [
                'required', 'string',
                'min:3', 'max:255'
            ],
            'message' => [
                'required', 'string',
                'min:3', 'max:65000'
            ],
            'name' => [
                'required', 'string',
                'min:3', 'max:128'
            ],
            'msisdn' => [
                'required', 'string',
                'min:12', 'max:16'
            ],
            'contract_no' => [
                'required', 'string',
                'min:3', 'max:64'
            ],
            'contact_media' => [
                'required', 'string',
                'min:3', 'max:8'
            ],
            'contact_time' => [
                'required', 'string',
                'min:3', 'max:255'
            ],
            'images' => 'nullable',
            'images.*' => ['string', 'max:64']
        ];

        return $this->validate($request, $rules);
    }
}