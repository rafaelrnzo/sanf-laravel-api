<?php

namespace NbsPhp\Core\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use NbsPhp\Core\Mail\BaseMailV2;
use Sanf\Api\Modules\ContactUs\AskUsSubmitController;
use NbsPhp\Core\Response\RestResponseMapper;

class AskUsController extends RestResponseMapper
{
    protected $askQuestion;
    
    public function __construct(AskUsSubmitController $askQuestion)
    {
        $this->ask = $askQuestion;
    }

    public function postQuestion(Request $request)
    {
        $request['topic_id'] = intval($request['topic_id']);
        $result = $this->ask->process($request);
        
        if($result){
            $recipients = explode(',', env('MAIL_TO_ADMIN'));
            $this->sendToMail($result, $recipients);

            return $this->successResponse(response()->json(''));
        }

        return $this->errorResponse(response()->json(config('response-codes')));
    }

    private function sendToMail($data = [], $recipients = [])
    {
        $askUsEmail = (new BaseMailV2)
            ->subject('Kritik dan saran dari pengguna SANFXtra!')
            ->leftLogo(asset('assets/svg/sanf-logo-blue.svg'))
            ->rightLogo(asset('assets/svg/sanf-tagline.svg'))
            ->banner(asset('assets/svg/email-verification.svg'))
            ->writeInto($data);

        if(is_array($data['images'])){

            foreach($data['images'] as $val){
                $askUsEmail->attach(public_path($val['path']));
            }
        }

        $askUsEmail->from($data['email'], $data['name']);

        return Mail::to($recipients)->send($askUsEmail);
    }
}
