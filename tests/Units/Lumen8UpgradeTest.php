<?php

namespace Tests\Units;

use TestCase;
use App\User;
use Illuminate\Support\Facades\Request;
use NbsPhp\Core\Jwt\JWTGuard;
use Illuminate\Contracts\Auth\UserProvider;

class Lumen8UpgradeTest extends TestCase
{
    /**
     * Test that User factory is correctly registered and works.
     */
    public function testUserFactoryWorks()
    {
        $user = User::factory()->make();
        $this->assertInstanceOf(User::class, $user);
        $this->assertNotEmpty($user->name);
        $this->assertNotEmpty($user->email);
    }

    /**
     * Test that login and register routes are now accessible without authentication.
     * Before the fix, these routes would return 401 because of the 'auth' middleware.
     */
    public function testAuthRoutesArePublic()
    {
        $this->post('/v1/users/log-in');
        $this->assertNotEquals(401, $this->response->getStatusCode());

        $this->post('/v1/users/register');
        $this->assertNotEquals(401, $this->response->getStatusCode());
    }

    /**
     * Test JWTGuard token parsing logic.
     */
    public function testJwtGuardParsing()
    {
        $mockProvider = $this->createMock(UserProvider::class);
        $mockJwt = $this->getMockBuilder(\NbsPhp\Core\Jwt\JWTHelper::class)
            ->disableOriginalConstructor()
            ->setMethods(['isHealthy', 'setToken', 'getToken'])
            ->getMock();

        $mockJwt->method('isHealthy')->willReturn(false);
        $mockJwt->expects($this->once())->method('setToken')->with('test-token');
        $mockJwt->method('getToken')->willReturn('test-token');

        $request = Request::create('/', 'GET');
        $request->headers->set('Authorization', 'Bearer test-token');

        $guard = new JWTGuard('api', $mockJwt, $mockProvider, $request);
    }

    /**
     * Test JWTGuard parsing with case-insensitive 'bearer' and whitespace.
     */
    public function testJwtGuardParsingRobustness()
    {
        $mockProvider = $this->createMock(UserProvider::class);
        $mockJwt = $this->getMockBuilder(\NbsPhp\Core\Jwt\JWTHelper::class)
            ->disableOriginalConstructor()
            ->setMethods(['isHealthy', 'setToken', 'getToken'])
            ->getMock();

        $mockJwt->method('isHealthy')->willReturn(false);
        $mockJwt->expects($this->once())->method('setToken')->with('robust-token');
        $mockJwt->method('getToken')->willReturn('robust-token');

        $request = Request::create('/', 'GET');
        $request->headers->set('Authorization', 'bearer  robust-token ');

        $guard = new JWTGuard('api', $mockJwt, $mockProvider, $request);
    }
}
