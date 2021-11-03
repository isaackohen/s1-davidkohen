<!DOCTYPE html>
<html>
    <head>
        <title>{{ \App\Settings::where('name', 'platform_name')->first()->value }}</title>
        <script src="https://kit.fontawesome.com/23f13eab24.js" crossorigin="anonymous"></script>


        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, height=device-height, minimum-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!--
        <meta property="og:image" content="{{ asset('https://i.imgur.com/HEAm2j7.png') }}" />
        <meta property="og:image:secure_url" content="{{ asset('https://i.imgur.com/omJ7On3.png') }}" />
        <meta property="og:image:type" content="image/svg+xml" />
        <meta property="og:image:width" content="295" />
        <meta property="og:image:height" content="295" />
        !-->
        @if(env('APP_DEBUG'))
            <meta http-equiv="Expires" content="Mon, 26 Jul 1997 05:00:00 GMT">
            <meta http-equiv="Pragma" content="no-cache">
        @endif

        <link rel="icon" href="{{ asset('/favicon.svg') }}">
        <link rel="manifest" href="/manifest.json">
		
		<script async src="https://www.googletagmanager.com/gtag/js?id={{ \App\Settings::where('name', 'gtag_key')->first()->value }}"></script>
		<script>
		  window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag('js', new Date());

		  gtag('config', '{{ \App\Settings::where('name', 'gtag_key')->first()->value }}');
		</script>
		
        <script type="text/javascript">
            window.Layout = {
                Frontend: '{!! base64_encode(file_get_contents(public_path('css/app.css'))) !!}'
            }
        </script>

        <script>
            window.Notifications = {
                vapidPublicKey: '{{ config('webpush.vapid.public_key') }}'
            };
        </script>
        
        <style>
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 99999;
            display: flex;
            flex-flow: row nowrap;
            justify-content: center;
            align-items: center;
            background: radial-gradient(circle at 110% 70%,#24262b,#1c1d21 45%);
            transition: all 0.2s ease-in-out;
        }
        </style>

    </head>
    <body>
        <div id="app">
        <div class="preloader">
            <img style="height: 50px; position: absolute; top: 0; bottom: 0; margin: auto; left: 0; right: 0;" src="/img/misc/loading.gif">
        </div>
            <layout></layout>
        </div>

        <script src="{{ asset(mix('/js/app.js')) }}"></script>

        @if(env('APP_DEBUG'))
            <script src="http://localhost:8098"></script>
        @endif
    </body>
</html>
