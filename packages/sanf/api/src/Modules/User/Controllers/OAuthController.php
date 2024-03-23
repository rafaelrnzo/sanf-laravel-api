<?php

namespace Sanf\Api\Modules\User\Controllers;

use Illuminate\Http\Request;
use NbsPhp\Core\Services\RegisterByAppleServiceInterface;
use NbsPhp\Core\Services\RegisterByGoogleServiceInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Core\Modules\User\Services\RegisterInternalByAppleService;
use Sanf\Core\Modules\User\Services\RegisterInternalByGoogleService;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class OAuthController extends \NbsPhp\Core\Controllers\OAuthController
{
    protected $userRepository;
    protected $internalApiClient;

    public function __construct(AuthModel $userRepository, SanfCoreApiClient $internalApiClient)
    {
        $this->userRepository = $userRepository;
        $this->internalApiClient = $internalApiClient;
        parent::__construct();
    }

    public function registerGoogle(Request $request, RegisterByGoogleServiceInterface $service)
    {
        return parent::registerGoogle($request, new RegisterInternalByGoogleService($service, $this->userRepository, $this->internalApiClient));
    }

    public function registerApple(Request $request, RegisterByAppleServiceInterface $service)
    {
        return parent::registerApple($request, new RegisterInternalByAppleService($service, $this->userRepository, $this->internalApiClient));
    }
}
