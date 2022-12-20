<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta
        name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"
    />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>{{ config('app.name') }}</title>

    <style>
        * {
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        .input-label {
            font-style: normal;
            font-weight: 700;
            font-size: 14px;
            line-height: 20px;
            margin-bottom: 8px;
            color: #232227;
        }

        .header {
            margin: -20px -20px 20px -20px;
            padding: 10px;
            background: #f7fafd;
        }

        :after,
        :before {
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        html {
            font-family: sans-serif;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            font-size: 10px;
            -webkit-tap-highlight-color: transparent;
        }

        .title {
            font-size: 16px;
            font-style: normal;
            font-weight: 500;
            line-height: 150%;
            /* identical to box height, or 24px */

            text-align: center;

            /* Text / Light */
            color: #999bac;
        }

        body {
            display: flex;
            height: 100vh;
            margin: 0;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-size: 14px;
            line-height: 1.42857143;
            color: #333;
            background-color: #eee;
            align-items: center;
        }

        body input {
            box-shadow: none !important;
            -webkit-box-shadow: none !important;
            -moz-box-shadow: none !important;
        }

        main {
            display: block;
        }

        h2 {
            text-align: center;
            font-size: 24px;
        }

        button,
        input {
            margin: 0;
            font: inherit;
            color: inherit;
        }

        button {
            overflow: visible;
        }

        button {
            text-transform: none;
        }

        button,
        html input[type="button"],
        input[type="reset"],
        input[type="submit"] {
            -webkit-appearance: button;
            cursor: pointer;
        }

        button[disabled],
        html input[disabled] {
            cursor: default;
        }

        button::-moz-focus-inner,
        input::-moz-focus-inner {
            padding: 0;
            border: 0;
        }

        input {
            line-height: normal;
        }

        input[type="checkbox"],
        input[type="radio"] {
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
            padding: 0;
        }

        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            height: auto;
        }

        input[type="search"] {
            -webkit-box-sizing: content-box;
            -moz-box-sizing: content-box;
            box-sizing: content-box;
            -webkit-appearance: textfield;
        }

        input[type="search"]::-webkit-search-cancel-button,
        input[type="search"]::-webkit-search-decoration {
            -webkit-appearance: none;
        }

        button,
        input {
            font-family: inherit;
            font-size: inherit;
            line-height: inherit;
        }

        [role="button"] {
            cursor: pointer;
        }

        input[type="search"] {
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        input[type="checkbox"],
        input[type="radio"] {
            margin: 4px 0 0;
            line-height: normal;
        }

        input[type="file"] {
            display: block;
        }

        input[type="range"] {
            display: block;
            width: 100%;
        }

        input[type="checkbox"]:focus,
        input[type="file"]:focus,
        input[type="radio"]:focus {
            outline: 5px auto -webkit-focus-ring-color;
            outline-offset: -2px;
        }

        .form-control {
            display: block;
            width: 100%;
            height: 34px;
            padding: 6px 12px;
            font-size: 14px;
            line-height: 1.42857143;
            color: #555;
            background-color: #fff;
            background-image: none;
            border: 1px solid #ccc;
            border-radius: 4px;
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
            -webkit-transition: border-color ease-in-out 0.15s,
            -webkit-box-shadow ease-in-out 0.15s;
            -o-transition: border-color ease-in-out 0.15s,
            box-shadow ease-in-out 0.15s;
            transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
        }

        .form-control:focus {
            border-color: #66afe9;
            outline: 0;
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075),
            0 0 8px rgba(102, 175, 233, 0.6);
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075),
            0 0 8px rgba(102, 175, 233, 0.6);
        }

        .form-control::-moz-placeholder {
            color: #999;
            opacity: 1;
        }

        .form-control:-ms-input-placeholder {
            color: #999;
        }

        .form-control::-webkit-input-placeholder {
            color: #999;
        }

        .form-control::-ms-expand {
            background-color: transparent;
            border: 0;
        }

        .form-control[disabled],
        .form-control[readonly] {
            background-color: #eee;
            opacity: 1;
        }

        .form-control[disabled] {
            cursor: not-allowed;
        }

        input[type="search"] {
            -webkit-appearance: none;
        }

        @media screen and (-webkit-min-device-pixel-ratio: 0) {
            input[type="date"].form-control,
            input[type="datetime-local"].form-control,
            input[type="month"].form-control,
            input[type="time"].form-control {
                line-height: 34px;
            }

            .input-group-lg input[type="date"],
            .input-group-lg input[type="datetime-local"],
            .input-group-lg input[type="month"],
            .input-group-lg input[type="time"],
            input[type="date"].input-lg,
            input[type="datetime-local"].input-lg,
            input[type="month"].input-lg,
            input[type="time"].input-lg {
                line-height: 46px;
            }
        }

        .form-group {
            margin-top: 24px;
        }

        input[type="checkbox"][disabled],
        input[type="radio"][disabled] {
            cursor: not-allowed;
        }

        .input-lg {
            height: 46px;
            padding: 10px 16px;
            font-size: 18px;
            line-height: 1.3333333;
            border-radius: 6px;
        }

        .form-group-lg .form-control {
            height: 46px;
            padding: 10px 16px;
            font-size: 18px;
            line-height: 1.3333333;
        }

        .btn {
            display: inline-block;
            padding: 6px 12px;
            margin-bottom: 0;
            font-size: 14px;
            font-weight: 400;
            line-height: 1.42857143;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            -ms-touch-action: manipulation;
            touch-action: manipulation;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            background-image: none;
            border: 1px solid transparent;
            border-radius: 4px;
        }

        .btn:active.focus,
        .btn:active:focus,
        .btn:focus {
            outline: 5px auto -webkit-focus-ring-color;
            outline-offset: -2px;
        }

        .btn:focus,
        .btn:hover {
            color: #333;
            text-decoration: none;
        }

        .btn:active {
            background-image: none;
            outline: 0;
            -webkit-box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
            box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
        }

        .btn[disabled] {
            cursor: not-allowed;
            -webkit-box-shadow: none;
            box-shadow: none;
            opacity: 0.65;
        }

        .btn-group-lg > .btn,
        .btn-lg {
            padding: 10px 16px;
            font-size: 18px;
            line-height: 1.3333333;
            border-radius: 6px;
        }

        .btn-block {
            display: block;
            width: 100%;
        }

        .btn-block + .btn-block {
            margin-top: 5px;
        }

        input[type="button"].btn-block,
        input[type="reset"].btn-block,
        input[type="submit"].btn-block {
            width: 100%;
        }

        .btn-group {
            position: relative;
            display: inline-block;
            vertical-align: middle;
        }

        .btn-group > .btn {
            position: relative;
            float: left;
        }

        .btn-group > .btn:active,
        .btn-group > .btn:focus,
        .btn-group > .btn:hover {
            z-index: 2;
        }

        .btn-group .btn + .btn,
        .btn-group .btn + .btn-group,
        .btn-group .btn-group + .btn,
        .btn-group .btn-group + .btn-group {
            margin-left: -1px;
        }

        .btn-group
        > .btn:not(:first-child):not(:last-child):not(.dropdown-toggle) {
            border-radius: 0;
        }

        .btn-group > .btn:first-child {
            margin-left: 0;
        }

        .btn-group > .btn:first-child:not(:last-child):not(.dropdown-toggle) {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .btn-group > .btn:last-child:not(:first-child) {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        .btn-group > .btn-group {
            float: left;
        }

        .btn-group > .btn-group:not(:first-child):not(:last-child) > .btn {
            border-radius: 0;
        }

        .btn-group > .btn-group:first-child:not(:last-child) > .btn:last-child {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .btn-group > .btn-group:last-child:not(:first-child) > .btn:first-child {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        [data-toggle="buttons"] > .btn input[type="checkbox"],
        [data-toggle="buttons"] > .btn input[type="radio"],
        [data-toggle="buttons"] > .btn-group > .btn input[type="checkbox"],
        [data-toggle="buttons"] > .btn-group > .btn input[type="radio"] {
            position: absolute;
            clip: rect(0, 0, 0, 0);
            pointer-events: none;
        }

        .input-group {
            position: relative;
            display: table;
            border-collapse: separate;
        }

        .input-group[class*="col-"] {
            float: none;
            padding-right: 0;
            padding-left: 0;
        }

        .input-group .form-control {
            position: relative;
            z-index: 2;
            float: left;
            width: 100%;
            margin-bottom: 0;
        }

        .input-group .form-control:focus {
            z-index: 3;
        }

        .input-group-lg > .form-control,
        .input-group-lg > .input-group-btn > .btn {
            height: 46px;
            padding: 10px 16px;
            font-size: 18px;
            line-height: 1.3333333;
            border-radius: 6px;
        }

        .input-group .form-control,
        .input-group-btn {
            display: table-cell;
        }

        .input-group .form-control:not(:first-child):not(:last-child),
        .input-group-btn:not(:first-child):not(:last-child) {
            border-radius: 0;
        }

        .input-group-btn {
            width: 1%;
            white-space: nowrap;
            vertical-align: middle;
        }

        .input-group .form-control:first-child,
        .input-group-btn:first-child > .btn,
        .input-group-btn:first-child > .btn-group > .btn,
        .input-group-btn:last-child > .btn-group:not(:last-child) > .btn,
        .input-group-btn:last-child
        > .btn:not(:last-child):not(.dropdown-toggle) {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .input-group .form-control:last-child,
        .input-group-btn:first-child > .btn-group:not(:first-child) > .btn,
        .input-group-btn:first-child > .btn:not(:first-child),
        .input-group-btn:last-child > .btn,
        .input-group-btn:last-child > .btn-group > .btn {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        .input-group-btn {
            position: relative;
            font-size: 0;
            white-space: nowrap;
        }

        .input-group-btn > .btn {
            position: relative;
        }

        .input-group-btn > .btn + .btn {
            margin-left: -1px;
        }

        .input-group-btn > .btn:active,
        .input-group-btn > .btn:focus,
        .input-group-btn > .btn:hover {
            z-index: 2;
        }

        .input-group-btn:first-child > .btn,
        .input-group-btn:first-child > .btn-group {
            margin-right: -1px;
        }

        .input-group-btn:last-child > .btn,
        .input-group-btn:last-child > .btn-group {
            z-index: 2;
            margin-left: -1px;
        }

        main {
            display: block;
        }

        button,
        input {
            margin: 0;
            font: inherit;
            color: inherit;
        }

        button {
            overflow: visible;
        }

        button {
            text-transform: none;
        }

        button,
        html input[type="button"],
        input[type="reset"],
        input[type="submit"] {
            -webkit-appearance: button;
            cursor: pointer;
        }

        button,
        input {
            font-family: inherit;
            font-size: inherit;
            line-height: inherit;
        }

        .btn-block {
            display: block;
            width: 100%;
        }

        .btn-block + .btn-block {
            margin-top: 5px;
        }

        button,
        input {
            outline: 0;
            border: 0;
        }

        .btn:active.focus,
        .btn:active:focus,
        .btn:focus,
        button,
        button:active,
        button:focus,
        input {
            outline: 0;
        }

        input[type="file"] {
            opacity: 0;
            position: absolute;
            top: 0;
            right: 0;
            min-width: 100%;
            min-height: 100%;
            outline: 0;
            cursor: inherit;
            font-size: 100px;
        }

        button:active.btn-follow-lg,
        button:focus.btn-follow-lg,
        button:hover.btn-follow-lg {
            background: #f40808;
            color: #fff !important;
            border: 1px solid #f40808;
        }

        button:active.btn-more-lg,
        button:focus.btn-more-lg,
        button:hover.btn-more-lg {
            background: #fff;
            color: #333;
            border: 1px solid #f8f8f8;
        }

        button:active.btn-follow,
        button:focus.btn-follow,
        button:hover.btn-follow {
            background: #3f3f3f;
            color: #fff !important;
        }

        .wrapper {
            padding: 20px;
            position: relative;
            border-radius: 3px;
            width: 500px;
            margin: 0 auto;
            background: #fff;
            box-shadow: 0 2px #ddd;
            -webkit-box-shadow: 0 2px #ddd;
            -moz-box-shadow: 0 2px #ddd;
        }

        .wrapper.message {
            text-align: center;
        }

        .alert {
            text-align: center;
        }

        .alert.success {
            color: #023e02;
        }

        .alert.error {
            color: #f40808;
        }

        .custom-rounded {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            padding: 14px 0 14px 12px;
            gap: 176px;

            background: #f4f6fa;
            border-radius: 10px;
            -webkit-border-radius: 10px;
            -moz-border-radius: 10px;
        }

        .btn-main,
        .btn-main:active,
        .btn-main:focus,
        .btn-main:hover {
            color: #fff;
            background: #03257e;
            border-radius: 12px;
        }

        .btn-main:hover {
            opacity: 0.9;
        }

        .btn-main:active,
        .btn-main:focus {
            background-image: none;
            outline: 0;
            -webkit-box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
            box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
            opacity: 0.9;
        }

        .form-control:focus {
            border-color: #f45302;
            outline: 0;
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075),
            0 0 8px #f45302;
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px #f45302;
        }

        .btn-lg {
            padding: 10px 30px;
        }

        .btn-rounded {
            padding: 12px 29px;
        }

        .content {
            padding: 4px;
        }

        .input-icons-container {
            position: relative;
        }

        .input-icons {
            position: absolute;
            right: 17px;
            top: 17px;
            bottom: 17px;
            opacity: 0.5;
            width: 20px;
            height: 15px;
        }

        @media screen and (max-width: 532px) {
            .wrapper {
                margin: auto 16px;
            }

            .content {
                padding: 0;
                margin: -4px;
            }

            .btn-rounded {
                width: 100%;
            }
        }
    </style>

    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0"
    />
</head>
<body>
    @yield('content')
</body>
</html>
