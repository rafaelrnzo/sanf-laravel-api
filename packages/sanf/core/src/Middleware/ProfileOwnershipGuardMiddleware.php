<?php

namespace Sanf\Core\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use NbsPhp\Core\Exceptions\ForbiddenException;
use Sanf\Core\Modules\User\Exceptions\ProfileNotFoundException;
use Sanf\Core\Modules\User\Repositories\ProfileRepositoryInterface;

class ProfileOwnershipGuardMiddleware
{
    /**
     * @var ProfileRepositoryInterface
     */
    protected $profileRepository;

    /**
     * ProfileOwnershipGuardMiddleware constructor.
     * @param ProfileRepositoryInterface $profileRepository
     */
    public function __construct(ProfileRepositoryInterface $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     * @throws ForbiddenException
     * @throws ProfileNotFoundException
     */
    public function handle($request, Closure $next)
    {
        $customerId = $this->extractCustomerId($request);

        if (!$customerId) {
            return $next($request);
        }

        $profile = $this->profileRepository->findById($customerId);

        if (is_null($profile)) {
            throw new ProfileNotFoundException('Profile Not Found');
        }

        $user = Auth::user();

        $isOwner = $profile->getEmail() === ($user->username ?? $user->email);

        if (!$isOwner) {
            throw new ForbiddenException('Unauthorized access to profile data');
        }

        return $next($request);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    protected function extractCustomerId($request)
    {
        return $request->route('xid') ?? $request->input('profile_xid');
    }
}
