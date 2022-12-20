@extends('core::layouts.master')

@section('content')
    <div class="wrapper">
        <div class="header">
            <table
                role="presentation"
                border="0"
                cellpadding="0"
                cellspacing="0"
                width="100%"
            >
                <tr>
                    <td align="center">
                        <a href="#">
                            <img
                                src="{{ asset('assets/png/sanf-logo-blue.png') }}"
                                alt="SANFIND Logo"
                                style="margin: 25px auto"
                            />
                        </a>
                    </td>
                </tr>
            </table>
        </div>

        <h2 style="color: #232227" class="text-xl4"><b>{{ __('Buat Password Baru') }}</b></h2>

        <p class="title">Silakan memasukkan password baru untuk akun Anda</p>

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
                <div class="input-label">Email Bisnis</div>
                <input
                    type="text"
                    style="height: 54px"
                    readonly="readonly"
                    class="form-control login-field custom-rounded"
                    value="{{$email ?? ''}}">
            </div>

            <div class="form-group">
                <div class="input-label">Password</div>
                <div class="input-icons-container">
                    <i class="material-symbols-outlined input-icons"> visibility </i>
                    <input
                        type="password"
                        style="height: 54px"
                        class="form-control login-field custom-rounded"
                        name="password"
                        placeholder="password"
                        aria-label="Password Baru"
                        autocomplete="off"
                        minlength="8"
                        required>
                </div>
            </div>

            <div class="form-group">
                <div class="input-label">Ketik Ulang Password</div>
                <div class="input-icons-container">
                    <i class="material-symbols-outlined input-icons"> visibility </i>
                    <input
                        type="password"
                        style="height: 54px"
                        class="form-control login-field custom-rounded"
                        name="password_confirmation"
                        placeholder="password"
                        aria-label="Ketik Ulang Password Baru"
                        autocomplete="off"
                        minlength="8"
                        required>
                </div>
            </div>

            <button type="submit" style="margin-top: 24px; margin-bottom: 12px; float: right"
                    class="btn btn-main btn-rounded"
            >Reset Password
            </button>
        </form>
    </div>
@endsection
