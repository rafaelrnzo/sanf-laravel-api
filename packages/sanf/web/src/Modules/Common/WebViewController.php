<?php


namespace Sanf\Web\Modules\Common;


use NbsPhp\Core\Controllers\RestController;

class WebViewController extends RestController
{
    public function aboutUs()
    {
        return view('web::web-view.about-us');
    }
}
