<!DOCTYPE html>
<html lang="en">
    <head>
        <title>KitaMatch</title>

        <!-- CSS And JavaScript -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
        <link href="{{ asset('css/kitamatch.css') }}" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="{{ asset('js/app.js') }}"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.16/datatables.min.css"/>
        <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.16/datatables.min.js"></script>
    </head>

    <body class=".bg-light">
      <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark pt-0 pb-0">
        <a class="navbar-brand" href="{{url('/')}}">{{config('app.name')}}</a>       
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
          <ul class="navbar-nav mr-auto">
            @if(Auth::check() && Auth::user()->account_type == 5)
            <li class="nav-item">
              <a class="nav-link" href="{{url('/program/all')}}">Kitagruppen</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{url('/applicant/all')}}">Bewerber</a>
            </li>
            @endif
          </ul>
          <ul class="navbar-nav px-3">
            @if(Auth::check())
              <!-- <li class="nav-item text-nowrap">
                <a href="{{url('/logout')}}" class="nav-link">Logout</a>
              </li> -->
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink-4" data-toggle="dropdown"
                  aria-haspopup="true" aria-expanded="false">
                  <i class="fas fa-user"></i>Benutzer </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-info" aria-labelledby="navbarDropdownMenuLink-4">
                  <a class="dropdown-item" style="background:#e5e5e5" >{{Auth::user()->email}}</a>
                  <a class="dropdown-item" href="{{ route('password.change')}}">Passwort ändern</a>
                  <a class="dropdown-item" href="{{url('/logout')}}">Abmelden</a>
                </div>
              </li>
            @endif
          </ul>
          
        </div>
      </nav>

        <main role="main" class="container" style="padding-top: 100px;">

            @yield('content')

        </main>

        <br>
        <br>
        <br>

        <footer class="text-muted">
          <div class="container">
            <p class="float-right">2020 © Marktdesign, Zentrum für Europäische Wirtschaftsforschung</p>
            </p>
          </div>
        </footer>

    </body>
</html>
