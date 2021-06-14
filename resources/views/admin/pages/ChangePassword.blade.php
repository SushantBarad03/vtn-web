@extends('Admin.Layout.app')
@push('content')

<!-- Start Form content -->
<div class="content">
<div class="container">
<div class="row ">
  <div class="col-md-12">
  <div class="card-box">
      <h4 class="m-t-0 m-b-30 header-title"><b>Change Password </b></h4>

    <div class="row form-horizontal">
      <form class="form-horizontal" id="add_admin" role="form" method="POST" action="{{ route('change-profile') }}" enctype="multipart/form-data" data-parsley-validate novalidate>
      @csrf

    <div class="form-group">
      <div class="col-sm-6">
        <label for="name">First Name <span class="star_sign">*</span></label>
        <input type="text" name="name" class="form-control" placeholder="Enter First Name" value="{{ \Auth::user()->name }}">
         @if($errors->has('name'))
            <strong style="color: red;">{{ $errors->first('name') }}</strong>
            <br/>
          @endif                                       
      </div>

      <div class="col-sm-6">
        <label for="name">Email <span class="star_sign">*</span></label>     
        <input type="text" name="email" class="form-control" placeholder="Enter Email" value="{{ \Auth::user()->email }}">
         @if($errors->has('email'))
            <strong style="color: red;">{{ $errors->first('email') }}</strong>
            <br/>
          @endif                                       
      </div>    
    </div>

    <div class="form-group">
    <div class="col-sm-4">
      <label for="name">Old Password </label>      
      <input type="password" name="password" class="form-control" placeholder="Enter Password" value="{{ old('password')}}">
      
        @if($errors->has('password'))
          <strong style="color: red;">{{ $errors->first('password') }}</strong>
          <br/>
        @endif
       @if (Session::has('msg'))
          <strong style="color: red;">{{ Session::get('msg') }}</strong>
          <br/>
        @endif  
    </div>

    <div class="col-sm-4">
      <label for="name">New Password </label>      
      <input type="password" name="new_password" id="new_password" class="form-control" placeholder="Enter New Confirmation Password" value="">
      
      @if($errors->has('new_password'))
        <strong style="color: red;">{{ $errors->first('new_password') }}</strong>
        <br/>
      @endif                  
    </div>

    <div class="col-sm-4">
      <label for="name">Confirm Password </label>
      
      <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" placeholder="Enter New Confirmation Password" value=""> 
      

      @if($errors->has('new_password_confirmation'))
        <strong style="color: red">{{ $error->first('new_password_confirmation') }}</strong>
        <br/>
      @endif  
    </div>      
    </div>

    <div class="form-group clearfix">
    <div class="text-left">
      <div class="col-lg-2"></div>
      <div class="col-lg-10">
        <input type="hidden" name="profile_id" id="profile_id" value=""/>
        <button type="submit" class="btn btn-success waves-effect waves-light save" id="save">Save</button>
        <a href="{{ URL::route('index') }}"><button type="button" class="btn btn-warning">Cancel</button></a>                     
      </div>
    </div>
    </div>
    </div>

  </div>
  </div>
</form>
</div>
</div>
</div>
@endpush
