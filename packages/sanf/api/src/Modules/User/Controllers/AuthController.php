<?php


namespace Sanf\Api\Modules\User\Controllers;


use Illuminate\Http\Request;
use NbsPhp\Core\Database\IlluminateSession;
use NbsPhp\Core\Services\ActivateUserServiceInterface;
use NbsPhp\Core\Services\VerifyEmailServiceInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Core\Modules\User\Services\ActivateUserAndRegisterInternalService;
use Sanf\Core\Modules\User\Services\VerifyEmailAndRegisterInternalService;
use Sanf\Integration\InternalApiClient;

class AuthController extends \NbsPhp\Core\Controllers\AuthController
{
    protected $userRepository;
    protected $internalApiClient;
    protected $transactionalSession;

    public function __construct(
        AuthModel $userRepository,
        InternalApiClient $internalApiClient,
        IlluminateSession $transactionalSession)
    {
        $this->userRepository = $userRepository;
        $this->internalApiClient = $internalApiClient;
        $this->transactionalSession = $transactionalSession;
        parent::__construct();
    }

    public function userActivationByApp(Request $request, ActivateUserServiceInterface $service)
    {
        return parent::userActivationByApp($request,
            new ActivateUserAndRegisterInternalService(
                $service,
                $this->userRepository,
                $this->internalApiClient,
                $this->transactionalSession,
            ));
    }

    public function verifyEmailByApp(Request $request, VerifyEmailServiceInterface $service)
    {
        return parent::verifyEmailByApp($request,
            new VerifyEmailAndRegisterInternalService(
                $service,
                $this->userRepository,
                $this->internalApiClient,
                $this->transactionalSession,
            ));
    }

    public function verifyEmailPage(Request $request, VerifyEmailServiceInterface $service)
    {
        return parent::verifyEmailPage($request,
            new VerifyEmailAndRegisterInternalService(
                $service,
                $this->userRepository,
                $this->internalApiClient,
                $this->transactionalSession,
            ));
    }
}
