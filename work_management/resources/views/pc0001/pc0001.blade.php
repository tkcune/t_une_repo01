<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('config.system_name') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>

    <!-- JS -->
    @yield('js')
    <script src="{{ asset('js/pamt01/pamt01.js') }}" defer></script>
    <script src="{{ asset('js/pamt01/pamt02.js') }}" defer></script>
    
    
    <!-- ツリー表示のjsプログラム -->
    <!-- <script src="{{ asset('js/pamt01/ptcmcb.js') }}" type="module"></script> -->
    <!-- <script src="{{ asset('js/pamt01/ptcmta.js') }}" type="module"></script> -->


    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Table Sort-->
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"　></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.0/js/jquery.tablesorter.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.0/css/theme.default.min.css">
    
    <script>
    $(document).ready(function() {
        $('#bs-table').tablesorter({
            headers: {
               5: { sorter: false }
            }
        });
    });
    $(document).ready(function() {
        $('#ji-table').tablesorter({
            headers: {
               6: { sorter: false },
               7: { sorter: false }
            }
        });
    });
    </script>

    @yield('script')
</head>

<body class="p-3 mb-2 bg-light">
<div class="header container-md">
    @component('components.phcm01')
    @endcomponent

    <div class="container-md">
        <div class="row">
            
        @component('components.ptcm02')
        @endcomponent
    
        @yield('content')
            
        </div>
    </div>
</div>    
</body>
</html>