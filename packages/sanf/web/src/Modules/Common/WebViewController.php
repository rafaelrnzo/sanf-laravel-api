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
                abort(404);
        }

        return view('core::layouts.message', ['message' => $message]);
    }

    public function approvalProject($status)
    {
        switch($status){
            case 'approve':
                $message = 'Permintaan telah disetujui';
                break;
            case 'reject':
                $message = 'Permintaan tidak disetujui';
                break;
            default:
                abort(404);
        }

        return view('core::layouts.message', ['message' => $message]);
    }

    public function faq()
    {
        return 'frequently ask question list';
    }

    public function faqPopular()
    {
        return 'frequently ask question for popular list';
    }

    public function faqByCategory()
    {
        return 'frequently ask question by category';
    }
}
