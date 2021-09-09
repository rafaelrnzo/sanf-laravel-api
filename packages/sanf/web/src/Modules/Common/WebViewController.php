<?php


namespace Sanf\Web\Modules\Common;

use NbsPhp\Core\Controllers\RestApiController;

class WebViewController extends RestApiController
{
    public function aboutUs()
    {
        return view('web::web-view.about-us');
    }

    public function termsCondition()
    {
        return view('web::web-view.terms-and-condition');
    }

    public function privacyPolicy()
    {
        return view('web::web-view.privacy-policy');
    }

    public function approvalCommodity($status)
    {
        switch($status){
            case 'approve':
                $message = 'Permintaan telah disetujui';
                break;
            case 'reject':
                $message = 'Permintaan tidak disetujui';
                break;
            default:
                break;
        }

        return view('core::layouts.message', ['message' => $message]);
    }
}
