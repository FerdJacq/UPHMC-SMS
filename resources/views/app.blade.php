<!DOCTYPE html>
<html data-bs-theme="light" data-layout-mode="fluid" data-menu-color="light" data-topbar-color="light" data-layout-position="fixed" data-sidenav-size="default" class="menuitem-active">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        
        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ asset('codefox/img/favicon.ico') }}">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">


        @vite(['resources/js/app.js', "resources/js/pages/{$page['component']}.vue"])
        @inertiaHead
        @routes
        
    </head>
    <body class="loading">
        <link href="{{ asset('codefox/css/app.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('codefox/css/icons.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('css/custom.css') }}" rel="stylesheet" />
        @inertia
  </body>
</html>
