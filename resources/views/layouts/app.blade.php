<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>GTIS</title>

    <!-- Scripts -->
    <script type="text/javascript" src="{{ asset('js/jquery-1.11.2.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/site.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-treeview.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/tree.jquery.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/toastr.js') }}"></script>

    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}" >
    <link rel="stylesheet" href="{{ asset('css/site.css') }}"
    <link rel="stylesheet" href="{{ asset('css/bootstrap-treeview.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jqtree.css') }}">
    <link rel="stylesheet" href="{{ asset('css/toastr.css') }}">

    <style>
        body {
            font-family: 'Lato';
        }

        .fa-btn {
            margin-right: 6px;
        }
    </style>
</head>
<body>
    <div class="container">
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="{{ url('/') }}">
                        <img src="{{ asset('images/taxonomy.jpg') }}" style="width: 25px; display: inline-block">
                        <span style="margin-left: 10px; color: #16737B">Greek Taxon Information System</span>
                    </a>
                </div>
                <div id="bs-example-navbar-collapse-1" class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @if (Auth::guest())
                            <li><a href="{{ url('/login') }}">Login</a></li>
                            <li><a href="{{ url('/register') }}">Register</a></li>
                        @else
                            <li>
                                <a href="{{ url('/home') }}">Home</a>
                            </li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ Auth::user()->firstname." ".Auth::user()->lastname." " }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>
        <div style="margin: 0px 15px;">
            <div class="row">
                    @yield('content')
            </div>
        </div>
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div id="bs-example-navbar-collapse-1" class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <li>
                            <a class="navbar-brand" href="https://portal.lifewatchgreece.eu">
                                <img src="https://portal.lifewatchgreece.eu/images/lfw_logo.png" style="width: 22px; display: inline-block">
                                <span style="margin-left: 10px; color: #16737B; font-size: 14px">Lifewatch Greece Portal</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>

    <img src="{{ asset('/images/loading.gif') }}" style="display:none" id="loading-image" />
</body>
    @if(Session::has('toastr'))
        <? $toastr = Session::get('toastr') ?>
        <script type="text/javascript">
            switch('{{ $toastr[0] }}'){
                case 'info':
                    toastr.info('{{ $toastr[1] }}');
                    break;
                case 'success':
                    toastr.success('{{ $toastr[1] }}');
                    break;
                case 'warning':
                    toastr.warning('{{ $toastr[1] }}');
                    break;
                case 'error':
                    toastr.error('{{ $toastr[1] }}');
                    break;
            }
        </script>
    @endif
</html>
