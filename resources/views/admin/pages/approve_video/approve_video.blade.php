@extends('admin.layout.app')
@push('content')
<div class="row">
  <div class="col-sm-12">
      <div class="card-box table-responsive">
          <div class="m-t-0 m-b-10 row">
              <div class="col-sm-4">
                  <h4 class="m-t-0 header-title"><b>Videos Listing</b></h4>
              </div>
              <div class="col-sm-8 text-right">                
            </div><br><br>
          </div>
             <table id="table_DT" class="table table-striped table-bordered">
            </table>
      </div>
    </div>
</div>
            
@endpush
@push('js')
<script type="text/javascript">
  $(function(){
    var table = $('#table_DT');
      oTable = table.DataTable({
        processing: true,
        serverSide: true,
        language: {
          lengthMenu: "_MENU_ entries",
          paginate: {
            previous: '<i class="fa fa-angle-left" ></i>',
            next: '<i class="fa fa-angle-right" ></i>'
          }
        },
        columns: [
          {"title": "Id",'width':'5%', data:"id"},
          {"title": "Video Name",'width':'85%', data:"video_name"},
          {"title": "Approved", "width":"5%", data:"statusApproved"},
          {"title": "Decline", "width":"5%", data:"statusDecline"},
          {"title": "Action",'width':'5%',"data":"action", searchble: false, sortable:false },
        ],
        responsive: false,
        order: [
          [0, 'desc']
        ],
        lengthMenu: [
          [5, 10, 20,],
          [5, 10, 20,]
        ],
        pageLength: 5,
        ajax: {
          url: "{{ route('approve_video.listing') }}",             
          },
      });
  });

// Status Change
$(document).on('click','#status',function(){
  var status = $(this).prop('checked') == true ? 1 : 0;
	var user_id = $(this).val();
  var lable = $(this).html();

  if(lable == 'Decline'){
    $(".show_decline"+user_id).closest('button').remove();
    $(".show_decline"+user_id).addClass("label-danger");
    //$(".show_decline"+user_id).removeClass("btn-warning");
  }else{
    $(".show"+user_id).closest('button').remove();
    $(".show"+user_id).addClass("btn btn-primary");
    // $(".show"+user_id).removeClass("btn btn-primary");
  }

  var apps_id = $(this).val();
  $.ajax({
    type: "POST",
    url: "{{ route('approve_video.status') }}",
    data: { "_token": "{{ csrf_token() }}" ,id: user_id,lable, status:status },
    success:function(result){
      if(result['status'] == 'true'){
        Swal.fire({
          position: 'center',
          icon: 'success',
          title: result['message'],
          showConfirmButton: false,
          timer: 2000
        });
      }else{
        Swal.fire({
          position: 'center',
          icon: 'error',
          title: result['message'],
          showConfirmButton: false,
          timer: 2000
        });
      }
    }
  });
});

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