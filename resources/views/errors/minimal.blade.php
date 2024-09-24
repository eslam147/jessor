<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
        body {
            overflow: hidden;
            height: 100vh;

        }

        .error-page {
            text-align: center;
            background: #fff;
            border-top: 1px solid #eee;
        }

        .error-page .error-inner {
            display: inline-block;
        }

        .error-page .error-inner h1 {
            font-size: 140px;
            text-shadow: 3px 5px 2px #3333;
            color: #006DFE;
            font-weight: 700;
        }

        .error-page .error-inner h1 span {
            display: block;
            font-size: 25px;
            color: #333;
            font-weight: 600;
            text-shadow: none;
        }

        .error-page .error-inner p {
            padding: 20px 15px;
        }

        .error-page .search-form {
            width: 100%;
            position: relative;
        }

        .error-page .search-form input {
            width: 400px;
            height: 50px;
            padding: 0px 78px 0 30px;
            border: none;
            background: #f6f6f6;
            border-radius: 5px;
            display: inline-block;
            margin-right: 10px;
            font-weight: 400;
            font-size: 14px;
        }

        .error-page .search-form input:hover {
            padding-left: 35px;
        }

        .error-page .search-form .btn {
            width: 80px;
            height: 50px;
            border-radius: 5px;
            cursor: pointer;
            background: #006DFE;
            display: inline-block;
            position: relative;
            top: -2px;
        }

        .error-page .search-form .btn i {
            font-size: 16px;
        }

        .error-page .search-form .btn:hover {
            background: #333;
        }
    </style>
</head>

<body>
    <section class="error-page h-100 section">
        <div class="container h-100">
            <div class="align-items-center h-100 justify-content-center row">
                <div class="col-12">
                    <div class="error-inner">
                        <div>
                            @if (View::hasSection('lottie'))
                                <lottie-player src="https://lottie.host/@yield('lottie')" background="##FFFFFF"
                                    speed="1" style="width: 635px; height: 370px" loop autoplay direction="1"
                                    mode="normal"></lottie-player>
                            @endif
                        </div>
                        @yield('message')
                    </div>
                </div>
            </div>
        </div>
    </section>
    @if (View::hasSection('lottie'))
        @once
            <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
        @endonce
    @endif
</body>
</html>
