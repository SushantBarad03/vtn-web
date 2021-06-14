@extends('admin.layout.app')
@push('url_title') Home @endpush
@push('content')                     

<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-lg-3">
                <div class="widget-bg-color-icon card-box fadeInDown animated">
                    <div class="bg-icon bg-icon-info pull-left">
                        <i class="fa fa-user"></i>
                    </div>
                    <div class="text-right">
                        <h3 class="text-dark"><b class="counter">{{ $data['UserCount'] ?? 0 }}</b></h3>
                        <p class="text-muted">Users</p>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="widget-bg-color-icon card-box fadeInDown animated">
                    <div class="bg-icon bg-icon-info pull-left">                        
                        <i class="fa fa-video-camera"></i>
                    </div>
                    <div class="text-right">
                        <h3 class="text-dark"><b class="counter">{{ $data['VideoCount'] ?? 0 }}</b></h3>
                        <p class="text-muted">Videos</p>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>   
      
        
        </div>
                    
    <!-- end row -->
    </div> <!-- container -->
</div> <!-- content -->

@endpush

@push('js')
<script type="text/javascript">
    @if(Session::get('success') != '')
        Swal.fire({
            position: 'center',
            icon: 'success',
            title: "{{Session::get('success')}}",
            showConfirmButton: false,
            timer: 2000
        });
    @endif
</script>
@endpush