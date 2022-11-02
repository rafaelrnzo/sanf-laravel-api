<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ $content->heading }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <!--<meta name="viewport" content="width=device-width, Maximum-scale=1"> Untuk tidak bisa zoom-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
          integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"
            integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct"
            crossorigin="anonymous"></script>
    <style>
        * {
            -moz-box-sizing: border-box;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
        }

        .item {
            position: relative;
            overflow: hidden;
        }

        .item img {
            -webkit-transition: .5s ease;
            -moz-transition: .5s ease;
            transition: .5s ease;
        }

        .item:hover img {
            -webkit-transform: scale(1.05, 1.05);
            -moz-transform: scale(1.05, 1.05);
            transform: scale(1.05, 1.05);
            cursor: zoom-in;
        }

        .item:before {
            content: "";
            display: block;
            top: 0;
            left: 0;
            position: absolute;
            opacity: 0;
            -moz-transition: 0.5s ease;
            -webkit-transition: 0.5s ease;
            transition: 0.5s ease;
        }

        .item:hover:before {
            cursor: zoom-in;
        }

        body {
            font-family: Montserrat;
        }

        header img, footer img {
            cursor: zoom-in;
        }

        .overlay:hover,
        .overlay:focus {
            opacity: 0;
            filter: alpha(opacity=0);
        }

        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            z-index: 1;
            /* Sit on top */
            top: 0;
            left: 0;
            transform: translate(calc(50vw - 50%), calc(50vh - 50%));
            width: 100%;
            /* Full width */
            height: 100%;
            /* Full height */
            overflow: auto;
            /* Enable scroll if needed */
            background-color: rgb(0, 0, 0);
            /* Fallback color */
            background-color: rgba(61, 61, 61, 0.562);
            /* Black w/ opacity */
        }

        /* Modal Content (image) */
        .modal-content {
            margin: auto;
            width: 80%;
        }

        .modal-content:hover {
            cursor: zoom-out;
        }

        /* Add Animation */
        .modal-content,
        #caption {
            -webkit-animation-name: zoom;
            -webkit-animation-duration: 0.6s;
            animation-name: zoom;
            animation-duration: 0.6s;
        }

        .out {
            animation-name: zoom-out;
            animation-duration: 0.6s;
        }

        @-webkit-keyframes zoom {
            from {
                -webkit-transform: scale(1)
            }

            to {
                -webkit-transform: scale(2)
            }
        }

        @keyframes zoom {
            from {
                transform: scale(0.4)
            }

            to {
                transform: scale(1)
            }
        }

        @keyframes zoom-out {
            from {
                transform: scale(1)
            }

            to {
                transform: scale(0)
            }
        }

        /* The Close Button */
        .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
        }

        .close:hover,
        .close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }

        /* 100% Image Width on Smaller Screens */
        @media only screen and (max-width: 700px) {
            .modal-content {
                width: 100%;
            }
        }
    </style>
</head>

<body>
<header>
    @if(isset($content->imageHeader))
    <div class="w-100 item">
        <img alt="{{ $content->heading }}" class='img-fluid w-100' id="myImg" onclick="preview(this)"
             src="{{ $content->imageHeader }}">
    </div>
    @endif
    <div class="container text-center mt-4 mb-4">
        <h1 class="font-weight-bold">{{ $content->heading }}</h1>
        <h6>{{ $content->date }}</h6>
        <h5 class="font-weight-bold">{{ $content->subHeading }}</h5>

    </div>
</header>

<article class="container mb-4">{!! $content->body !!}</article>
<footer class="w-100 item">
    @if(isset($content->imageFooter))
        <img class="w-100 img-fluid" id="myImg" alt="{{ $content->heading }}" onclick="preview(this)"
             src="{{ $content->imageFooter }}">
    @endif
</footer>
<div id="myModal" class="modal" onclick="this.style.display='none'">
    <img class="modal-content" id="img01">
</div>


</body>

<script>
    function preview(element) {
        document.getElementById("img01").src = element.src;
        document.getElementById("myModal").style.display = "flex";
    }
</script>

</html>