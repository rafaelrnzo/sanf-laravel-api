<?php

namespace Sanf\Web\Modules\Setting\Controllers;

use NbsPhp\Core\Controllers\RestApiController;
use Sanf\Core\Modules\Setting\Services\GetStaticContentService;

class StaticContentController extends RestApiController
{
    public function getRead(string $xid, GetStaticContentService $service)
    {
        $result = $service->execute((object) ['xid' => $xid]);

        return view('web::web-view.static-content', ['content' => $result]);
    }
}
