<?php


namespace Sanf\Web\Modules\Common;


use NbsPhp\Core\Controllers\RestController;

class WebViewController extends RestController
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
}
