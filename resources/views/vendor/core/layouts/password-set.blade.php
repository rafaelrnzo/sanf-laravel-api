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
                    <table role="presentation" class="main">
                        <tr>
                            <td class="wrapper">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td>
                                            <p style="color: #232227" class="text-xl4"><b>Password Berhasil Dibuat</b></p>
                                        </td>
                                    </tr>
                                </table>

                                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td>
                                            <blockquote>
                                                <p class="text-lg">
                                                    Password baru Anda berhasil dibuat, silakan buka aplikasi <b style="color: #192F7C">SANFIND</b> melalui perangkat smartphone Anda kemudian Login menggunakan akun dan password baru Anda.
                                                </p>
                                            </blockquote>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <blockquote>
                                                <p class="text-lg">
                                                    Jika Anda tidak memiliki aplikasi <b style="color: #192F7C">SANFIND</b> atau sudah uninstall aplikasi silakan download terlebih dahulu melalui Play Store atau App Store.                                                </p>
                                            </blockquote>
                                        </td>
                                    </tr>
                                </table>

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
