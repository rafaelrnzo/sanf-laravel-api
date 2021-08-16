@component('mail::layout')
    {{--START HEADER--}}
        @slot('header')
            @component('mail::header-v2', [
                'url' => config('mail.url'),
                'logo' => [$leftLogo, $rightLogo]
            ])
            @endcomponent
        @endslot
    {{--END HEADER--}}

    {{--START BANNER--}}
        @isset($banner)
            @slot('banner')
                @component('mail::banner')
                    {{ $banner }}
                @endcomponent
            @endslot
        @endisset
    {{--END BANNER--}}

    {{--START BODY--}}
    <table role="presentation" class="main">
        <tr>
            <td class="wrapper">
                <table class="another-email-content" border="0" cellpadding="0"
                        cellspacing="0" width="100%">
                    <tr>
                        <td align="left" width="110px" class="data-title">Topik</td>
                        <td align="left" class="data-value">{{ $topic }}</td>
                    </tr>
                    <tr>
                        <td align="left" class="data-title">Judul</td>
                        <td align="left" class="data-value">{{ $title }}</td>
                    </tr>
                    <tr>
                        <td align="left" class="data-title">Pesan</td>
                        <td align="left" class="data-value">{{ $message }}</td>
                    </tr>
                </table>
                <table class="another-email-content" border="0" cellpadding="0"
                    cellspacing="0" width="100%" style="padding-bottom: 25px;">
                    <tr>
                        <td colspan="2" align="left" class="title">Informasi Tambahan</td>
                    </tr>
                    <tr>
                        <td colspan="2" align="left" class="data-title">Nama</td>
                    </tr>
                    <tr>
                        <td colspan="2" align="left" class="data-value">{{ $name }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" align="left" class="data-title">Email Bisnis (PIC)</td>
                    </tr>
                    <tr>
                        <td colspan="2" align="left" class="data-value">{{ $email }}</td>
                    </tr>
                    <tr>
                        <td align="left" class="data-title">No. Handphone (PIC)</td>
                        <td align="left" class="data-title">Nomor Kontak</td>
                    </tr>
                    <tr>
                        <td align="left" class="data-value">{{ $phone_number }}</td>
                        <td align="left" class="data-value">{{ $contract_no }}</td>
                    </tr>
                    <tr>
                        <td align="left" class="data-title">Cara Menghubungi</td>
                        <td align="left" class="data-title">Waktu Menghubungi</td>
                    </tr>
                    <tr>
                        <td align="left" class="data-value">{{ $contact_media }}</td>
                        <td align="left" class="data-value">{{ $contact_time }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    {{--END BODY--}}

    {{--START FOOTER--}}
    @slot('footer')
        @component('mail::footer', ['url' => config('mail.url'), 'social_account' => config('mail.social_account', [])])
        @endcomponent
    @endslot
    {{--END FOOTER--}}
@endcomponent
