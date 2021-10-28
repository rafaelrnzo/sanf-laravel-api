<?php


namespace Sanf\Core\Modules\Financing\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;


class DownloadFinancingSimulationService implements ApplicationServiceInterface
{

    public function execute($dto = null)
    {
        return response()->download(public_path('assets/dummyDownload.pdf'));
    }

}
