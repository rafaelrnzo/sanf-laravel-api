<?php

namespace Sanf\External\Modules\Setting\Controllers;

use Illuminate\Support\Facades\Artisan;
use NbsPhp\Core\Controllers\RestApiController;

class MaintenanceModeByExternalController extends RestApiController
{
    public function postUp()
    {
        Artisan::call('maintenance:up');

        return $this->responseOk(['message' => 'The application alive after 5 seconds.']);
    }

    public function postDown()
    {
        Artisan::call('maintenance:down');

        return $this->responseOk(['message' => 'Maintenance mode will active after 5 seconds.']);
    }
}
