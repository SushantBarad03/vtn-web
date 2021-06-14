<!-- ========== Left Sidebar Start ========== -->
	<div class="left side-menu">
	<div class="sidebar-inner slimscrollleft">
	<!--- Divider -->
	<div id="sidebar-menu">
	    <ul>
	    <li class="text-muted menu-title" align="center"> {{ ucfirst(Auth::user()->name) }} </li>
	        <li class="has_sub">
	            <a href="{{ route('index') }}" class="{{ (request()->is('admin/home')) ? 'active' : '' }}"><i class="fa fa-home"></i> <span> Dashboard </span></a>
	        </li>

	        <li class="has_sub">
                <a href="{{ route('user.index') }}" class="waves-effect {{ (request()->is('admin/user*')) ? 'active' : '' }}"><i class="fa fa-users"></i> <span> Users </span></a>
            </li>
            <li class="has_sub">
                <a href="{{ route('report.index') }}" class="waves-effect {{ (request()->is('admin/report*')) ? 'active' : '' }}"><i class="fa fa-video-camera"></i> <span> Video Report </span></a>
            </li>
            <li class="has_sub">
                <a href="{{ route('complainant_data.index') }}" class="waves-effect {{ (request()->is('admin/complainant_data*')) ? 'active' : '' }}"><i class="fa fa-user"></i> <span> User Report </span></a>
            </li>
            <li class="has_sub">
                <a href="{{ route('approve_video.index') }}" class="waves-effect {{ (request()->is('admin/approve_video*')) ? 'active' : '' }}"><i class="fa fa-check"></i> <span> Approve Videos  </span></a>
            </li>

           <!--  <li class="has_sub">
                <a href="{{ route('video.index') }}" class="waves-effect {{ (request()->is('admin/video*')) ? 'active' : '' }}"><i class="fa fa-video-camera"></i> <span> Video </span></a>
            </li> -->
            
	    </ul>
	    <div class="clearfix"></div>
	</div>
		<div class="clearfix"></div>
	</div>
	</div>
<!-- ========== Left Sidebar End ========== -->