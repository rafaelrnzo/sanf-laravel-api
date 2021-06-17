<?php


namespace Sanf\Api\Modules\Common;


use NbsPhp\Core\Controllers\RestController;

class WebViewAboutUsController extends RestController
{

    public function process()
    {
        return view('api::web-view.about-us');
    }
}