<?php

namespace Sanf\Core;

use Closure;
use Illuminate\Support\Facades\Auth;
use NbsPhp\Core\Exceptions\ForbiddenException;
use Sanf\Core\Modules\User\Exceptions\ProfileNotFoundException;
use Sanf\Core\Modules\User\Repositories\ProfileRepositoryInterface;

class CheckPicMiddleware
{
    protected ProfileRepositoryInterface $profileRepository;

    /**
     * CheckPicMiddleware constructor.
     * @param $profileRepository
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
     */
    public function handle($request, Closure $next)
    {
        $customerId = $this->extractCustomerId($request);
        $profile = $this->profileRepository->findById($customerId);
        if (is_null($profile)) {
            throw new ProfileNotFoundException('Profile Not Found to Validate');
        }
        $isPic = $profile->getEmail() == Auth::user()->username && $profile->getIsPic();
        if (!$isPic) {
            throw new ForbiddenException('Non PIC not Authorized');
        }
        return $next($request);
    }

    public function extractCustomerId($request)
    {
        return $request->route('xid') ?? $request->input('profile_xid');
    }
}
