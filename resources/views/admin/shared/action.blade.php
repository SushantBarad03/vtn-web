@php	
	$view = $view ?? true;	
	$edit = $edit ?? true;
	$statusshow = $statusshow ?? false;	
	$delete = $delete ?? true;
	$user = $user ?? true;
	$user_view = $user_view ?? true;
	$statusApproved = $statusApproved ?? true;
	$statusDecline = $statusDecline ?? true;
	$status = $status ?? true;
@endphp

@if($view)
	<a href="{{ route($routeName.'.show', $id) }}" title="View"><button type="button" class="btn btn-primary btn-custom waves-effect waves-light">Videos</button></a>
@endif

@if($user)
	<a href="{{ route('report.show', $id) }}" title="View"><button type="button" class="btn btn-primary btn-custom waves-effect waves-light">Users</button></a>
@endif

@if($user_view)
	<a href="{{ route('complainant_data.show', $id) }}" title="View"><button type="button" class="btn btn-primary btn-custom waves-effect waves-light">Users</button></a>
@endif

@if($edit)
	<a href="{{ route($routeName.'.edit',$id) }}" title="Edit"><button type="button" class="btn btn-icon waves-effect waves-light btn-warning"><i class="fa fa-edit"></i></button></a>
@endif

@if($statusshow)
	<label class="switch"><input type="checkbox" {{ $status == '1' ? 'checked' : '' }} value="{{ $id }}" id="status" style="size: small"><span class="slider round"></span></label>
@endif

@if($delete)
	<a title="Delete" href="{{ route($routeName.'.destroy', $id) }}" class="act-delete"><button type="button" class="btn btn-icon waves-effect waves-light btn-danger"><i class="fa fa-trash"></i></button></a>
@endif

@if($statusApproved)  
  @if($status == '1')
  	<span class="label label-success show{{$id}}">Approved</span>
  @else
  	<button type="button" class="btn btn-primary show{{$id}}" value="{{ $id }}" name="approved" id="status">Approve</button>
  @endif
@endif

@if($statusDecline)	
	@if($status == '0')
		<span class="label label-danger show_decline{{$id}}">Decline</span>
	@else
		<button type="button" class="btn btn-warning show_decline{{$id}}" value="{{ $id }}" name="decline" id="status">Decline</button>	
	@endif
@endif