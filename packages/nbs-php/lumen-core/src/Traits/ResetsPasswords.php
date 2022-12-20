<?php

namespace NbsPhp\Core\Traits;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use NbsPhp\Core\Exceptions\ResetPasswordFailedException;
use NbsPhp\Core\Jwt\JWTHelper;

trait ResetsPasswords
{
    /**
     * Get the post register / login redirect path.
     *
     * @return string
     */
    public function redirectPath()
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/home';
    }

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param Request $request
     * @param string|null $token
     * @return Factory|View
     */
    public function showResetForm(Request $request)
    {
        try {
            return view(config('auth.views.reset-password'))->with(
                ['token' => $request->token, 'error' => $request->session()->get('error')]
            );
        } catch (\Exception $exception) {
            report($exception);
            if ($request->expectsJson()) {
                throw $exception;
            }
            return view(config('auth.views.reset-password'))->with(
                ['token' => $request->token, 'error' => $exception->getMessage()]
            )->withErrors(['error' => $exception->getMessage()]);
        }
    }

    /**
     * Reset the given user's password.
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function reset(Request $request)
    {
        try {
            $this->validate($request, $this->rules(), $this->validationErrorMessages());

            //to handle validation from web pages, see reset-password.blade.php
            if ($request->has('password_confirmation')) {
                $this->validate($request, ['password' => 'confirmed']);
            }

            // Here we will attempt to reset the user's password. If it is successful we
            // will update the password on an actual user model and persist it to the
            // database. Otherwise we will parse the error and return the response.
            $response = $this->broker()->reset(
                $this->credentials($this->getRequest($request)), function ($user, $password) {
                $this->resetPassword($user, $password);
            }
            );

            // If the password was successfully reset, we will redirect the user back to
            // the application's home authenticated view. If there is an error we can
            // redirect them back to where they came from with their error message.
            return $response == Password::PASSWORD_RESET
                ? $this->sendResetResponse($request, $response)
                : $this->sendResetFailedResponse($request, $response);
        } catch (ValidationException $exception) {
            report($exception);
            if ($request->expectsJson()) {
                throw $exception;
            }

            return redirect_with_session()->route('password.request', ['token' => $request->token])
                ->with(['error' => extract_validation_message($exception)]);
        } catch (\Exception $exception) {
            report($exception);
            if ($request->expectsJson()) {
                throw $exception;
            }
            return redirect_with_session()->route('password.request', ['token' => $request->token])
                ->with(['error' => $exception->getMessage()]);
        }
    }

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return config('auth.input_validations.reset_password.rules');
    }

    /**
     * Get the password reset validation error messages.
     *
     * @return array
     */
    protected function validationErrorMessages()
    {
        return config('auth.input_validations.reset_password.messages');
    }

    /**
     * Get the password reset credentials from the request.
     *
     * @param Request $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only(
            'username', 'password', 'token'
        );
    }

    /**
     * Reset the given user's password.
     *
     * @param \Illuminate\Contracts\Auth\CanResetPassword $user
     * @param string $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $this->setUserPassword($user, $password);

        $user->setRememberToken(Str::random(60));

        $user->save();

        event(new PasswordReset($user));

        $this->guard()->login($user);
    }

    /**
     * Set the user's password.
     *
     * @param \Illuminate\Contracts\Auth\CanResetPassword $user
     * @param string $password
     * @return void
     */
    protected function setUserPassword($user, $password)
    {
        $user->password = bcrypt($password);
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param Request $request
     * @param string $response
     * @return RedirectResponse|JsonResponse
     */
    protected function sendResetResponse(Request $request, $response)
    {
        if ($request->expectsJson()) {
            return $this->responseOk();
        }
        return view(config('auth.views.password-set'));
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param Request $request
     * @param string $response
     * @return RedirectResponse|JsonResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        throw new ResetPasswordFailedException(trans($response));
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return PasswordBroker
     */
    public function broker()
    {
        return Password::broker();
    }

    /**
     * Get the guard to be used during password reset.
     *
     * @return StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }

    /**
     * replace request with jwt payload
     *
     * @return Request
     */
    public function getRequest(Request $request)
    {
        $decodedToken = $this->extractToken($request);
        $request['username'] = $decodedToken->email;
        $request['token'] = $decodedToken->token;
        return $request;
    }

    private function extractToken(Request $request)
    {
        //TODO COONFIGURABLE HEADER SOURCE NAME
        $jwtToken = $request->token ?? str_replace('Bearer ', '', $request->header('X-Reset-Password-Token'));
        $decodedToken = (new JWTHelper())->setToken($jwtToken)->getDecoded();
        if (is_null($decodedToken)) {
            throw new ResetPasswordFailedException(trans(Password::INVALID_TOKEN));
        }
        return $decodedToken;
    }
}
