<?php

namespace Sanf\Api\Modules\ContactUs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use NbsPhp\Core\Controllers\RestApiController;
use NbsPhp\Core\Response\JsonResponseMapper;
use Sanf\Core\Modules\ContactUs\AskUsSubmitService;

class AskUsSubmitController extends RestApiController
{
    protected $service;

    public function __construct(AskUsSubmitService $service)
    {
        parent::__construct();

        $this->service = $service;
    }

    public function process(Request $request, JsonResponseMapper $response)
    {
        // validate request;
        $property = $this->validating($request);

        if (isset($request['images'])) {
            $property += ['images' => $request['images']];
        }

        // set up dto;
        $dto = new AskUsSubmitRequestDto($property);

        // TODO USE DECORATOR
        $result = DB::transaction(function () use ($dto) {
            return $this->service->execute($dto);
        });

        if ($result) {
            $recipients = explode(',', config('sanf-mobile.mail_to.customer_service'));
            dispatch(new SendAskUsJob($result, $recipients));
        }

        return $response->successResponse(response()->json());
    }

    private function validating(Request $request)
    {
        $rules = [
            'topic_id' => ['required'],
            'title' => ['required', 'string', 'max:100'],
            'message' => ['required', 'string', 'max:500'],
            'name' => ['required', 'string', 'max:128'],
            'email' => ['required', 'email', 'max:255'],
            'phone_number' => ['required', 'string', 'min:11', 'max:20'],
            'contract_no' => ['nullable', 'string', 'max:50'],
            'contact_media' => ['required', 'string', 'max:20'],
            'contact_time' => ['required', 'string', 'max:20'],
            'images' => ['nullable'],
            'images.*' => ['string', 'max:64'],
        ];

        return $this->validate($request, $rules);
    }
}
