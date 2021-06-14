<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf_token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon_1.ico') }}">
    <title>Public Application</title>

    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/core.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/components.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/pages.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/responsive.css') }}" rel="stylesheet" type="text/css" />

    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <script src="{{ asset('assets/js/modernizr.min.js') }}"></script>
</head>

<body>
<div class="account-pages"></div>
<div class="clearfix"></div>
<div class="wrapper-page">
<div class=" card-box">
    <div class="panel-heading">
        <h3 class="text-center"> Reset Password </h3>
    </div>

    @if(Session::has('message'))
    <p class="alert bg-success text-white" style="background: #2ECC71;">{{ Session::get('message') }}</p>
    @endif

    <div class="panel-body">
    <form action="{{ route('admin.auth.password.newpassword') }}" class="form-horizontal m-t-20" method="post">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">
    <label>Email</label>
    
    <div class="form-group ">
    <div class="col-xs-12">
        <input class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" id="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
    </div>
    </div>

    <label>Password</label>
    <div class="form-group">
    <div class="col-xs-12">
        <input class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" id="password" type="password" required="" placeholder="Password">
    </div>
        @if ($errors->has('password'))
        <span class="invalid-feedback" role="alert">
        <strong>{{ $errors->first('password') }}</strong>
        </span>
        @endif
    </div>

    <label>Confirm Password</label>
    <div class="form-group">
    <div class="col-xs-12">
        <input class="form-control{{ $errors->has('password-confirm') ? ' is-invalid' : '' }}" name="password_confirmation" id="password-confirm" type="password" required="" placeholder="Confirm Password">
    </div>
        @if ($errors->has('password-confirm'))
        <span class="invalid-feedback" role="alert">
        <strong>{{ $errors->first('password-confirm') }}</strong>
        </span>
        @endif
    </div>

    <div class="form-group text-center m-t-40">
    <div class="col-xs-12">
    <button class="btn btn-block text-uppercase waves-effect waves-light" type="submit" style="background-color: #5FBEAA; color: white;">Log In</button>
    </div>
    </div>
</form>

</div>
</div>
</div>

    <script>
    var resizefunc = [];
    </script>

    <!-- jQuery -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/detect.js') }}"></script>
    <script src="{{ asset('assets/js/fastclick.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.slimscroll.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.blockUI.js') }}"></script>
    <script src="{{ asset('assets/js/waves.js') }}"></script>
    <script src="{{ asset('assets/js/wow.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.nicescroll.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.scrollTo.min.js') }}"></script>


    <script src="{{ asset('assets/js/jquery.core.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.app.js') }}"></script>

    @stack('js')

</body>
</html>