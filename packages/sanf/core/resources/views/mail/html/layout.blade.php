<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="id">
<head>
    <meta name="viewport" content="width=device-width"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title></title>
</head>
<body>
<style type="text/css">
    @media only screen and (max-width: 620px) {
        table[class=body] h1 {
            font-size: 28px !important;
            margin-bottom: 10px !important;
        }

        table[class=body] .wrapper,
        table[class=body] .article {
            padding: 10px !important;
        }

        table[class=body] .content {
            padding: 0 !important;
        }

        table[class=body] .container {
            padding: 0 !important;
            width: 100% !important;
        }

        table[class=body] .main {
            border-left-width: 0 !important;
            border-radius: 0 !important;
            border-right-width: 0 !important;
        }

        table[class=body] .btn table {
            width: 100% !important;
        }

        table[class=body] .btn a {
            width: 100% !important;
        }

        table[class=body] .img-responsive {
            height: auto !important;
            max-width: 100% !important;
            width: auto !important;
        }
    }

    @media all {
        .ExternalClass {
            width: 100%;
        }

        .ExternalClass,
        .ExternalClass p,
        .ExternalClass span,
        .ExternalClass font,
        .ExternalClass td,
        .ExternalClass div {
            line-height: 100%;
        }

        .apple-link a {
            color: inherit !important;
            font-family: inherit !important;
            font-size: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
            text-decoration: none !important;
        }

        .btn-primary table td:hover {
            background-color: #052c3ccc !important;
        }

        .btn-primary a:hover {
            background-color: #052c3ccc !important;
        }

        .action-button:hover {
            background-color: #052c3ccc;
        }

        .footer .social-account a:hover {
            opacity: 0.5;
        }
    }
</style>
<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
    <tr>
        <td class="container">
            {{ $header ?? '' }}

            <div class="content">
                {{ $banner ?? '' }}

                {{ $slot ?? '' }}
            </div>

            {{ $footer ?? '' }}
        </td>
    </tr>
</table>
</body>
</html>
