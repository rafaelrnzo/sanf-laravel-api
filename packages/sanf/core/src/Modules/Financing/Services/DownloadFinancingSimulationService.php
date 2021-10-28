<?php


namespace Sanf\Core\Modules\Financing\Services;

use Illuminate\Support\Facades\Storage;
use NbsPhp\Core\Services\ApplicationServiceInterface;


class DownloadFinancingSimulationService implements ApplicationServiceInterface
{

    public function execute($dto = null)
    {
        return Storage::get('dummyDownload.pdf');
    }

}
