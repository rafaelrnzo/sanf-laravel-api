<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="id">

<head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" href="{{ asset('assets/css/sanf-page.css') }}">
</head>

<body>

    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
        <tr>
            <td class="container">
                @component('core::layouts.content-header')
                @endcomponent

                @component('core::layouts.content-banner')
                @endcomponent

                <div class="content">
                    {{-- Verifikasi Akun Berhasil
                        Selamat akun SANF anda sudah aktif silahkan login melalui aplikasi SANF pada ponsel anda
                        Buka Aplikasi SANF --}}

                    <table role="presentation" class="main">
                        <tr>
                            <td class="wrapper">

                                @component('core::layouts.content-message')
                                @endcomponent
                            </td>
                        </tr>
                    </table>
                </div>
                {{--
                @component('core::layouts.content-footer')
                @endcomponent
                --}}
            </td>
        </tr>
    </table>
</body>

</html>