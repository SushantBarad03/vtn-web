<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
        <meta name="author" content="Coderthemes">

        <link rel="shortcut icon" href="{{ asset('admin_theme/assets/images/favicon_1.ico') }}">

        <title>Admin | Public App</title>

        <link href="{{ asset('admin_theme/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('admin_theme/assets/css/core.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('admin_theme/assets/css/components.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('admin_theme/assets/css/icons.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('admin_theme/assets/css/pages.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('admin_theme/assets/css/responsive.css') }}" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="assets/js/modernizr.min.js"></script>
        
    </head>
    <body>

        <div class="account-pages"></div>
        <div class="clearfix"></div>
        <div class="wrapper-page">
          <div class=" card-box">
            <div class="panel-heading"> 
                <h3 class="text-center"> Sign In to <strong class="text-custom">Public App</strong> </h3>
            </div> 


            <div class="panel-body">
            <form class="form-horizontal m-t-20" action="{{ route('admin.login') }}" method="post">
                @csrf
                <div class="form-group ">
                    <div class="col-xs-12">
                        <input type="text" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}">
                        @if($errors->has('email'))
                          <strong style="color:red;">{{$errors->first('email')}}</strong>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Password">
                        @if($errors->has('password'))
                          <strong style="color:red;">{{$errors->first('password')}}</strong>
                        @endif
                    </div>
                </div>

                 <div class="form-group text-center m-t-40">
                    <div class="col-xs-12">
                        <button class="btn btn-pink btn-block text-uppercase waves-effect waves-light" type="submit">Log In</button>
                    </div><br>
                    <p class="mb-1">
                        <a href="{{ route('admin.auth.password.request') }}">I forgot My Password </a>
                    </p>
                </div>
            </form> 
            
            </div>   
            </div>                              
        </div>
        
      <script>
            var resizefunc = [];
        </script>

        <!-- jQuery  -->
        <script src="{{ asset('admin_theme/assets/js/jquery.min.js') }}"></script>
        <script src="{{ asset('admin_theme/assets/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('admin_theme/assets/js/detect.js') }}"></script>
        <script src="{{ asset('admin_theme/assets/js/fastclick.js') }}"></script>
        <script src="{{ asset('admin_theme/assets/js/jquery.slimscroll.js') }}"></script>
        <script src="{{ asset('admin_theme/assets/js/jquery.blockUI.js') }}"></script>
        <script src="{{ asset('admin_theme/assets/js/waves.js') }}"></script>
        <script src="{{ asset('admin_theme/assets/js/wow.min.js') }}"></script>
        <script src="{{ asset('admin_theme/assets/js/jquery.nicescroll.js') }}"></script>
        <script src="{{ asset('admin_theme/assets/js/jquery.scrollTo.min.js') }}"></script>


        <script src="{{ asset('admin_theme/assets/js/jquery.core.js') }}"></script>
        <script src="{{ asset('admin_theme/assets/js/jquery.app.js') }}"></script>
  
  </body>
</html>