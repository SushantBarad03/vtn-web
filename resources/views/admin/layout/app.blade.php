<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
        <meta name="author" content="Coderthemes">
        <meta name="csrf_token" content="{{ csrf_token() }}">

        <link rel="shortcut icon" href="{{ asset('admin_theme/assets/images/favicon_1.ico') }}">

        <title>Public Application</title>
        
        <link href="{{ asset('admin_theme/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('admin_theme/assets/css/core.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('admin_theme/assets/css/components.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('admin_theme/assets/css/icons.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('admin_theme/assets/css/pages.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('admin_theme/assets/css/responsive.css') }}" rel="stylesheet" type="text/css" />
        
        <link href="{{ asset('admin_theme/assets/css/custom.css') }}" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/summernote/dist/summernote.css') }}">
       
        <link href="{{ asset('admin_theme/assets/plugins/datatables/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css"/>        
        <link href="{{ asset('admin_theme/assets/plugins/datatables/responsive.bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>        
        <link href="{{ asset('admin_theme/assets/plugins/datatables/dataTables.bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
        <link href="{{asset('admin_theme/assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css')}}" rel="stylesheet">       

    @stack('css')

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="{{ asset('admin_theme/assets/js/modernizr.min.js') }}"></script>
        
    </head>
<body class="fixed-left">
       
       <div id="wrapper">
       <!-- Top Bar Start -->
            @include('admin.shared.header')
            <!-- Top Bar End -->

        <!-- ========== Left Sidebar Start ========== -->
            @include('admin.shared.sidebar')
        <!-- ========== Left Sidebar End ========== --> 

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->

        <div class="content-page">
        <div class="content">
        <div class="container">
            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    <h4 class="page-title">@yield('title')</h4>
                </div>
            </div>
            <div class="row">
            <div class="col-md-12">
                <div>
                <div >
                   <section>
                        <h4 class=""></h4>
                      @stack('content') 
                    </section>
                </div>
                </div>
            </div>
            </div>

        </div>
        </div>
        </div>
        <!-- ============================================================== -->
        <!-- End Right content here -->
        <!-- ============================================================== -->   

        <!-- Right Sidebar -->
            @include('admin.shared.footer')
        <!-- /Right-bar -->
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

        <script src="{{ asset('admin_theme/assets/plugins/peity/jquery.peity.min.js') }}"></script>

    <!-- jQuery  -->
        <script src="{{ asset('admin_theme/assets/plugins/waypoints/lib/jquery.waypoints.js') }}"></script>
        <script src="{{ asset('admin_theme/assets/plugins/counterup/jquery.counterup.min.js') }}"></script>
        <script src="{{ asset('admin_theme/assets/plugins/raphael/raphael-min.js') }}"></script>
        <script src="{{ asset('admin_theme/assets/plugins/jquery-knob/jquery.knob.js') }}"></script>

        <script src="{{ asset('admin_theme/assets/js/jquery.core.js') }}"></script>
        <script src="{{ asset('admin_theme/assets/js/jquery.app.js') }}"></script>
                

        <script src="{{ asset('admin_theme/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('admin_theme/assets/plugins/datatables/dataTables.bootstrap.js') }}"></script>
        <script src="{{ asset('admin_theme/assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>   

        <script src="{{ asset('admin_theme/assets/plugins/summernote/dist/summernote.min.js') }}"></script>

        <script src="{{ asset('admin_theme/assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js')}}"></script>
        <script src="{{ asset('admin_theme/assets/plugins/bootstrap-filestyle/src/bootstrap-filestyle.min.js') }}" type="text/javascript"></script>

        {{-- SweetAlert --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script> 

        {{-- Custom --}}
        <script src="{{ asset('admin_theme/assets/js/custom.js') }}"></script>       

@stack('js')

</body>
</html>