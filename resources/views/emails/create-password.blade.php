<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>SANF Email - Create Password</title>
    <style type="text/css">
        @import url("https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;700&display=swap");

        *,
        *:before,
        *:after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            outline: none;
        }

        html, body {
            height:100%;
            padding:0;
            margin:0;
            overflow: auto;
        }

        table {
            font-family: "Roboto", sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-font-smoothing: antialiased;
            font-smoothing: antialiased;
        }

        table td {
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: 21px;
            letter-spacing: 0;
            color: #616161;
        }

        table td.value {
            font-weight: 700;
        }

        .sf-container-email {
            width: 100%;
            height: 100%;
            padding: 50px 0 50px 0;
        }

        .sf-content-message-email {
            padding: 32px 24px;
            text-align: center;
        }

        .sf-content-message-email td {
            padding: 5px 0;
        }

        .sf-content-message-email td.img img {
            height: 450px;
            object-fit: cover;
            border-radius: 8px !important;
        }

        .sf-content-message-email td.title {
            color: #212121;
            font-size:18px;
            font-style: normal;
            font-weight: bold;
            padding-bottom: 16px;
        }

        .sf-content-message-email td.subtitle {
            font-size: 16px;
            font-weight: 400;
            font-style: normal;
            font-weight: normal;
            color: #616161;
            line-height: 27px;
        }

        .sf-content-message-email td.button {
            padding-top: 16px;
        }

        .sf-content-message-email td.button > button {
            width: 179px;
            height: 53px;
            color: #ffff;
            cursor: pointer;
            font-size: 18px;
            font-style: normal;
            background: #03257E;
            border: none;
            box-shadow: none;
        }

        .sf-content-message-email td.button > button:hover{
            background: #032b94;
        }

        .sf-content-message-email td.button > button:focus:active{
            background: #03257E;
        }

        .sf-content-message-email td.notes {
            font-size: 13px;
            font-weight: 400;
            font-style: normal;
            text-align: center;
            padding-top: 16px;
        }

        .sf-content-email.sf-header-logo-email {
            height: 102px;
            border-bottom: 1px solid #EEEEEE;
            text-align: center;
        }

        .sf-content-message-email td.message {
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: 21px;
            text-align: left;
            padding: 16px;
            background-color: #F8F8F8;
            -webkit-border-radius: 8px;
            -moz-border-radius: 8px;
            border-radius: 8px;
        }

        .sf-footer-email {
            display: flex;
            justify-content: center;
            background-color: #f2f2f2;
        }

        .sf-footer-email td {
            padding: 10px 0;
            color: #828282;
        }

        @media only screen and (max-width: 700px) {

            .sf-container-email {
                padding: 32px 0 32px 0;
            }

            .sf-content-email {
                width: 100% !important;
            }

            .sf-header-logo-email, td {
                padding: 0 !important;
            }

            .sf-button-email, .sf-sub-header-email td {
                padding: 16px !important;
            }

            .sf-content-message-email {
                padding: 16px 10px !important;
            }

            .sf-content-message-email td.img img {
                height: 245px !important;
                object-fit: cover !important;
            }

            .sf-button-email td {
                padding: 10px 0 0 0 !important;
            }

            .sf-content-regards-email td {
                padding: 30px 16px 30px 16px !important;
            }

            .sf-footer-email.title {
                padding: 16px 0 !important;
            }

            .sf-footer-email {
                margin-top: 16px
            }

            .sf-footer-email.website {
                padding: 16px 0 !important;
            }

            .sf-footer-email.website a {
                letter-spacing: 10px !important;
            }
        }
    </style>
</head>

<body style="padding: 0; margin: 0;">
    <table class="sf-container-email" border="0" bgcolor="#f2f2f2" cellpadding="0" cellspacing="0" width="100%"
       height="100%">
    <tr>
        <td align="center" valign="top">
            <!-- CONTAINER -->
            <table class="sf-content-email" border="0" cellpadding="0" cellspacing="0" width="600" bgcolor="#ffffff">
                <tr>
                    <td align="center" valign="top">
                        <!-- HEADER -->
                        <table class="sf-content-email sf-header-logo-email" border="0" cellpadding="0"
                               cellspacing="0" width="100%" bgcolor="#03257E">
                            <tr>
                                <td>
                                    <img
                                        src="{{asset('assets/svg/sanf-logo.svg')}}"
                                        alt="sanf logo"
                                        class="img-fluid">
                                </td>
                            </tr>
                        </table>
                        <!-- END HEADER -->

                        <!-- CONTENT MESSAGE EMAIL-->
                        <table class="sf-content-email sf-content-message-email" border="0" cellpadding="0"
                               cellspacing="0" width="100%">
                            <tr>
                                <td colspan="3" class="title">Registrasi berhasil! Silahkan aktivasi akun Anda</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="subtitle">
                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed sollicitudin arcu felis,
                                    a suscipit arcu fringilla at. Nunc ante dolor, gravida quis ante vel, eleifend porta nunc.
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" class="button">
                                    <button>Aktivasi Akun</button>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" class="notes">
                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed sollicitudin arcu felis,
                                    a suscipit arcu fringilla at. Nunc ante dolor, gravida quis ante vel, eleifend porta nunc.
                                </td>
                            </tr>
                        </table>
                        <!-- END CONTENT MESSAGE EMAIL -->

                        <!-- FOOTER EMAIL -->
                        <table class="sf-footer-email" border="0" cellpadding="0">
                            <tr colspan="3">
                                <td>© 2021 Surya Artha Nusantara Finance</td>
                            </tr>
                        </table>

                        <!-- END FOOTER EMAIL -->
                    </td>
                </tr>
            </table>
            <!-- END CONTAINER -->
        </td>
    </tr>
</table>
</body>
</html>
