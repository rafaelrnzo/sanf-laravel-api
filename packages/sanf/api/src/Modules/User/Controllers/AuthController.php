<?php


namespace Sanf\Api\Modules\User\Controllers;


use Illuminate\Http\Request;
use NbsPhp\Core\Database\IlluminateSession;
use NbsPhp\Core\Dto\DeviceInfoRequestDto;
use NbsPhp\Core\Dto\LoginRequestDto;
use NbsPhp\Core\Services\ActivateUserServiceInterface;
use NbsPhp\Core\Services\LoginWithEmailAndPasswordService;
use NbsPhp\Core\Services\ThrottleFailureService;
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
    protected $throttlingService;

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

    public function login(Request $request, LoginWithEmailAndPasswordService $service)
    {
        $input = $this->validateLogin($request);
        $dto = new LoginRequestDto([
            'username' => $input['username'],
            'password' => $input['password'],
            'device' => new DeviceInfoRequestDto([
                'deviceId' => $input['device']['device_id'],
                'devicePlatformId' => $input['device']['device_platform_id'],
                'notificationToken' => $input['device']['notification_token'],
                'notificationChannelId' => $input['device']['notification_channel_id'],
                'metadata' => $input['device']['metadata']
            ])
        ]);
        $throttleService = new ThrottleFailureService($request, $service);
        $user = $throttleService->execute($dto);

        return $this->responseOk(
            'Success',
            fractal($user, config('auth.transformers.login'))
        )->withHeaders([
            'X-Access-Token' => $user->accessToken,
            'X-Access-Expired-At' => $user->accessExpiredAt,
            'X-Refresh-Token' => $user->refreshToken,
            'X-Refresh-Expired-At' => $user->refreshExpiredAt,
        ]);
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
