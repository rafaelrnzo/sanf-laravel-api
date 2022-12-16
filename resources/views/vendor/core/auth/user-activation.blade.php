@extends('core::layouts.master')

@section('content')
    <div class="wrapper">
        <h2>{{ __('Buat Password Baru') }}</h2>

        @isset($success)
            <div class="alert success">
                <p>{{ $success }}</p>
            </div>
        @endisset

        @isset($error)
            <div class="alert error">
                <p>{{ $error }}</p>
            </div>
        @endisset

        <form method="post" name="form" action="{{route('user.activate-from-web')}}">
            <input type="hidden" name="token" value="{{ $token ?? '' }}">

            <div class="form-group">
                <input
                    type="text"
                    readonly="readonly"
                    class="form-control login-field custom-rounded"
                    value="{{$email}}">
            </div>

            <div class="form-group">
                <input
                    type="password"
                    class="form-control login-field custom-rounded"
                    name="password"
                    placeholder="password"
                    aria-label="Password Baru"
                    autocomplete="off"
                    minlength="8"
                    required>
            </div>

            <div class="form-group">
                <input
                    type="password"
                    class="form-control login-field custom-rounded"
                    name="password_confirmation"
                    placeholder="password"
                    aria-label="Ketik Ulang Password Baru"
                    autocomplete="off"
                    minlength="8"
                    required>
            </div>

            <button type="submit" class="btn btn-block btn-lg btn-main custom-rounded">Reset Password</button>
        </form>
    </div>
@endsection
