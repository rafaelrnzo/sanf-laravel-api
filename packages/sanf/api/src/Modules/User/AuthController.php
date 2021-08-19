<?php


namespace Sanf\Api\Modules\User;


use Illuminate\Http\Request;
use NbsPhp\Core\Services\RegisterByEmailServiceInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Core\Modules\User\Services\RegisterInternalByEmailService;
use Sanf\Integration\InternalApiClient;

class AuthController extends \NbsPhp\Core\Controllers\AuthController
{
    protected $userRepository;
    protected $internalApiClient;

    public function __construct(AuthModel $userRepository, InternalApiClient $internalApiClient)
    {
        $this->userRepository = $userRepository;
        $this->internalApiClient = $internalApiClient;
        parent::__construct();
    }

    public function register(Request $request, RegisterByEmailServiceInterface $service)
    {
        return parent::register($request, new RegisterInternalByEmailService($service, $this->userRepository, $this->internalApiClient));
    }
}
