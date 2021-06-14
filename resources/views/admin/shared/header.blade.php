<!-- Top Bar Start -->
<div class="topbar">

    <!-- LOGO -->
    <div class="topbar-left">
        <div class="text-center">
            <a href="{{ URL::route('index') }}" class="logo"><i class="icon-magnet icon-c-logo"></i><span>Public App</span></a>
        </div>
    </div>

<!-- Button mobile view to collapse sidebar menu -->
    <div class="navbar navbar-default" role="navigation">
    <div class="container">
    <div class="">
        <div class="pull-left">
            <button class="button-menu-mobile open-left">
                <i class="ion-navicon"></i>
            </button>
            <span class="clearfix"></span>
        </div>
        
        <ul class="nav navbar-nav navbar-right pull-right">
        <li class="dropdown hidden-xs">                                
            <ul class="dropdown-menu dropdown-menu-lg">
        </li>
        </ul>                  
     
	   <li class="dropdown">
	        <a href="" class="dropdown-toggle profile" data-toggle="dropdown" aria-expanded="true"><img src="{{ asset('admin_theme/assets/images/users/avatar-1.jpg') }}" alt="user-img" class="img-circle"> </a>
	        <ul class="dropdown-menu">
	            <li><a href="{{ route('profile') }}"><i class="ti-user m-r-5"></i> Profile</a></li>
	            <li><a href="{{ route('logout') }}"><i class="ti-power-off m-r-5"></i> Logout</a></li>           
	        </ul>
	    </li>
    
    </div><!--/.nav-collapse -->
    </div>
    </div>
</div>
<!-- Top Bar End -->