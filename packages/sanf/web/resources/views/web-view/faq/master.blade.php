<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'FAQ')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href='https://fonts.googleapis.com/css?family=DM Sans' rel='stylesheet'>
    <link href="https://fonts.cdnfonts.com/css/gilroy-bold" rel="stylesheet">
    <style>
        .btn-custom {
            color: #fff;
            background-color: #03257E;
            border-color: #03257E;
            border-radius: 0.75rem;
        }

        .btn-custom:hover, .btn-custom:active, .btn-custom:focus {
            color: #fff;
            background-color: #021958;
            border-color: #021958;
        }

        .border-custom {
            border-color: #03257E;
        }

        body {
            font-family: 'Gilroy-Medium', sans-serif;
            font-style: normal;
            font-weight: 600;
            color: #4E568C;
        }

        div.header {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            padding: 1rem;
            gap: 0.3rem;

            position: absolute;
            width: 100%;
            height: 143px;
            left: 0px;
            top: 0px;

            background: linear-gradient(180deg, #03257E 0%, #1C00C5 100%);
            border-radius: 0px 0px 15px 15px;
        }

        div.header .welcome-text {
            font-family: 'DM Sans', sans-serif;
            font-style: normal;
            font-weight: 400;
            font-size: 14px;
            display: flex;
            align-items: center;
            color: #FFFFFF;

        }

        div.header .question-text {
            font-family: 'DM Sans', sans-serif;
            font-style: normal;
            font-weight: 700;
            font-size: 16px;
            display: flex;
            align-items: center;
            color: #FFFFFF;
        }

        div.container {
            margin-top: 160px;
        }

        h1.container-title {
            font-family: 'Gilroy-Medium', sans-serif;
            font-style: normal;
            font-weight: 700;
            font-size: 16px;
            line-height: 24px;
            color: #03103C;
        }

        input[type=search] {
            font-family: 'Gilroy-Medium', sans-serif;
            border-top-left-radius: 0.625rem;
            border-bottom-left-radius: 0.625rem;
            padding-top: 0.625rem;
            padding-bottom: 0.625rem;
        }

        .btn-search {
            background-color: #fff;
            border-top-right-radius: 0.625rem;
            border-bottom-right-radius: 0.625rem;
        }
    </style>
</head>
<body class="pb-3">

@yield('content')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
</body>
</html>
