<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Sanf\Core\Modules\User\Entities\ProfileEntityInterface;
use Sanf\Core\Modules\User\Exceptions\ProfileNotFoundException;
use Sanf\Core\Modules\User\Repositories\ProfileRepositoryInterface;
use Sanf\Core\Middleware\ProfileOwnershipGuardMiddleware;

class ProfileOwnershipGuardMiddlewareTest extends PHPUnitTestCase
{
    private ProfileRepositoryInterface $mockProfileRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockProfileRepository = $this->createMock(ProfileRepositoryInterface::class);
    }

    public function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    private function setRouteXid(Request $request, $xid): void
    {
        $request->setRouteResolver(function () use ($xid) {
            return new class($xid) {
                private $xid;
                public function __construct($xid) { $this->xid = $xid; }
                public function parameter($name, $default = null) {
                    return $name === 'xid' ? $this->xid : $default;
                }
            };
        });
    }

    public function testPassesThroughWhenNoCustomerIdFound()
    {
        Auth::shouldReceive('user')->never();

        $request = Request::create('/profile', 'GET');
        $nextCalled = false;
        $next = function ($req) use (&$nextCalled) {
            $nextCalled = true;
            return response('ok');
        };

        $middleware = new ProfileOwnershipGuardMiddleware($this->mockProfileRepository);
        $middleware->handle($request, $next);

        $this->assertTrue($nextCalled);
    }

    public function testThrowsProfileNotFoundWhenProfileDoesNotExist()
    {
        Auth::shouldReceive('user')->never();

        $this->mockProfileRepository->expects($this->once())
            ->method('findById')
            ->with(999)
            ->willReturn(null);

        $request = Request::create('/profile', 'GET');
        $this->setRouteXid($request, 999);

        $next = function ($req) {
            return response('ok');
        };

        $middleware = new ProfileOwnershipGuardMiddleware($this->mockProfileRepository);

        $this->expectException(ProfileNotFoundException::class);
        $middleware->handle($request, $next);
    }

    public function testThrowsForbiddenExceptionWhenUserDoesNotOwnProfile()
    {
        $mockProfile = \Mockery::mock(ProfileEntityInterface::class);
        $mockProfile->shouldReceive('getEmail')->andReturn('owner@example.com');

        $this->mockProfileRepository->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($mockProfile);

        $mockUser = \Mockery::mock();
        $mockUser->username = 'intruder@example.com';

        Auth::shouldReceive('user')->once()->andReturn($mockUser);

        $request = Request::create('/profile', 'GET');
        $this->setRouteXid($request, 1);

        $next = function ($req) {
            return response('ok');
        };

        $middleware = new ProfileOwnershipGuardMiddleware($this->mockProfileRepository);

        $this->expectException(\NbsPhp\Core\Exceptions\ForbiddenException::class);
        $middleware->handle($request, $next);
    }

    public function testPassesThroughWhenUserOwnsProfileViaUsername()
    {
        $mockProfile = \Mockery::mock(ProfileEntityInterface::class);
        $mockProfile->shouldReceive('getEmail')->andReturn('owner@example.com');

        $this->mockProfileRepository->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($mockProfile);

        $mockUser = \Mockery::mock();
        $mockUser->username = 'owner@example.com';

        Auth::shouldReceive('user')->once()->andReturn($mockUser);

        $request = Request::create('/profile', 'GET');
        $this->setRouteXid($request, 1);

        $nextCalled = false;
        $next = function ($req) use (&$nextCalled) {
            $nextCalled = true;
            return response('ok');
        };

        $middleware = new ProfileOwnershipGuardMiddleware($this->mockProfileRepository);
        $middleware->handle($request, $next);

        $this->assertTrue($nextCalled);
    }

    public function testExtractsCustomerIdFromInputParameter()
    {
        $mockProfile = \Mockery::mock(ProfileEntityInterface::class);
        $mockProfile->shouldReceive('getEmail')->andReturn('user@example.com');

        $this->mockProfileRepository->expects($this->once())
            ->method('findById')
            ->with(123)
            ->willReturn($mockProfile);

        $mockUser = \Mockery::mock();
        $mockUser->username = 'user@example.com';

        Auth::shouldReceive('user')->once()->andReturn($mockUser);

        $request = Request::create('/profile', 'GET');
        $request->merge(['profile_xid' => 123]);
        $this->setRouteXid($request, null);

        $nextCalled = false;
        $next = function ($req) use (&$nextCalled) {
            $nextCalled = true;
            return response('ok');
        };

        $middleware = new ProfileOwnershipGuardMiddleware($this->mockProfileRepository);
        $middleware->handle($request, $next);

        $this->assertTrue($nextCalled);
    }

    public function testRouteParameterTakesPrecedenceOverInputParameter()
    {
        $mockProfile = \Mockery::mock(ProfileEntityInterface::class);
        $mockProfile->shouldReceive('getEmail')->andReturn('user@example.com');

        $this->mockProfileRepository->expects($this->once())
            ->method('findById')
            ->with(100)
            ->willReturn($mockProfile);

        $mockUser = \Mockery::mock();
        $mockUser->username = 'user@example.com';

        Auth::shouldReceive('user')->once()->andReturn($mockUser);

        $request = Request::create('/profile', 'GET');
        $request->merge(['profile_xid' => 999]);
        $this->setRouteXid($request, 100);

        $nextCalled = false;
        $next = function ($req) use (&$nextCalled) {
            $nextCalled = true;
            return response('ok');
        };

        $middleware = new ProfileOwnershipGuardMiddleware($this->mockProfileRepository);
        $middleware->handle($request, $next);

        $this->assertTrue($nextCalled);
    }
}
