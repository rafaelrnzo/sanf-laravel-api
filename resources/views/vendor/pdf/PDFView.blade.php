<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;700&display=swap");
        @import url("https://fonts.googleapis.com/css2?family=Gilroy:wght@100;300;400;700&display=swap");
        /*All the styling goes here*/

        body {
            /* background-color: #e6e6e6; */
            font-family: "Roboto", sans-serif;
            -webkit-font-smoothing: antialiased;
            font-size: 13px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        table {
            border-collapse: separate;
            min-width: 100%;
            width: 100%;
        }

        table td {
            font-family: "Roboto", sans-serif;
            font-size: 13px;
            vertical-align: top;
        }

        img {
            border: none;
            -ms-interpolation-mode: bicubic;
            width: 70%;
        }

        .body {
            color: #666666;
            /* background-color: #e6e6e6; */
            width: 100%;
            text-align: center;
            padding-top: 32px;
        }

        /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
        .container {
            display: block;
            margin: 0 auto !important;
            /* makes it centered */
            max-width: 600px;
            padding: 0 !important;
            width: 600px;
        }

        /* This should also be a block element, so that it will fill 100% of the .container */
        .content {
            box-sizing: border-box;
            display: block;
            max-width: 600px;
        }

        .main {
            background: #fff;
            width: 100%;
        }

        .header {
            padding: 20px;
            background: #f7fafd;
        }

        .wrapper {
            box-sizing: border-box;
            padding: 0 25px;
        }

        .content-block {
            padding-bottom: 10px;
            padding-top: 10px;
        }

        .footer {
            clear: both;
            width: 100%;
            margin: 10px 0 30px 0;
            text-align: center;
        }

        .footer td,
        .footer p,
        .footer span,
        .footer a {
            color: #aaaaaa;
            font-size: 12px;
        }

        .font-weight-normal {
            font-family: "Gilroy", sans-serif;
            font-style: normal;
            font-weight: 400;
            font-size: 14px;
            line-height: 150%;
            color: #232227;
        }

        .font-weight-bold {
            font-family: "Gilroy", sans-serif;
            font-style: normal;
            font-weight: 600;
            font-size: 14px;
            line-height: 150%;
            color: #232227;
        }

        .another-email-content {
            font-family: "Roboto", sans-serif;
        }

        .another-email-content td {
            padding: 8px 0;
        }
    </style>

</head>

<body>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
        <tr>
            <td class="container">
                <div class="header">
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td align="left" width="50%">
                                <img src="{{ $contents['images'][0] }}" alt="" />
                            </td>
                            <td align="right" width="50%">
                                <img src="{{ $contents['images'][1] }}" alt="" />
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="content">
                    <table role="presentation" class="main">
                        <tr>
                            <td height="15"></td>
                        </tr>
                        <tr>
                            <td align="left">
                                @php 
                                    setlocale(LC_ALL, "id_ID.UTF-8", "id_ID.UTF-8");
                                @endphp
                                {{ strftime("%A, %d %B %Y") }}
                            </td>
                        </tr>
                        <tr>
                            <td align="left">
                                <p>Selamat siang {{ $contents['name']}}, berikut kami lampirkan hasil perhitungan
                                    simulasi pengajuan pembiayaan anda
                                </p>
                            </td>
                        </tr>
                    </table>

                    <table class="another-email-content" border="0" cellpadding="0" cellspacing="0" width="100%" style="padding-bottom: 25px;">
                        @foreach($contents['data'] as $key => $value)
                            @php
                                $key = ucwords(str_replace('_', ' ', $key));
                                $key === 'Persen Dp' ? $key = 'Persen DP' : $key;
                            @endphp

                            <tr>
                                <td align="left" width="50%" class="font-weight-normal">{{ $key }}</td>
                                <td align="right" width="50%" class="font-weight-bold">{{ $value }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>

                <div class="footer">
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td align="center">Hasil perhitungan ini bersifat tidak mengikat</td>
                        </tr>
                        <tr>
                            <td align="center">&copy; {{ date('Y') }} Surya Artha Nusantara Finance</td>
                        </tr>
                    </table>
                </div>

            </td>
        </tr>
    </table>
</body>

</html>