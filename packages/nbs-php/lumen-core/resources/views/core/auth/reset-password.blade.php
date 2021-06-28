@extends('core::layouts.master')

@section('content')
    <div class="wrapper">
        <h2>{{ __('Ganti Password') }}</h2>

        @isset($success)
            <div class="alert success">
                <p>{{ $success  }}</p>
            </div>
        @endisset

        @isset($error)
            <div class="alert error">
                <p>{{ $error  }}</p>
            </div>
        @endisset

        <form method="post" name="form">
            <div class="form-group">
                <input
                    type="password"
                    class="form-control login-field custom-rounded"
                    name="password"
                    placeholder="Password Baru"
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
                    placeholder="Konfirmasi Password Baru"
                    aria-label="Konfirmasi Password Baru"
                    autocomplete="off"
                    minlength="8"
                    required>
            </div>

            <button type="submit" class="btn btn-block btn-lg btn-main custom-rounded">Submit</button>
        </form>
    </div>
@endsection
