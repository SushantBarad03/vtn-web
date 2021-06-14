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
            {"title": "Video Name",'width':'18%', data:"video_name"},
            {"title": "Video Title",'width':'7%', data:"video_title"},
            {"title": "Views",'width':'5%', data:"views"},
            {"title": "Likes",'width':'5%', data:"likes"},
            {"title": "Shares",'width':'5%', data:"shares"},
            {"title": "Save",'width':'5%', data:"save"},
            {"title": "Report",'width':'5%', data:"report"},
            {"title": "Location",'width':'5%', data:"location"},
            {"title": "Status",'width':'5%', data:"status"},
            {"title": "Date",'width':'5%', data:"date"},
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
            url: "{{ route('video.listing') }}", 
             data: {
                  parent_id: function() {
                      return "{{ $parent_id }}";
                  }
              }
            },
      });
  });

// Status Change
$(document).on('click','#status',function(){
    var status = $(this).prop('checked') == true ? 1 : 0; 
    var apps_id = $(this).val();
    $.ajax({
        type: "POST",
        url: "{{ route('video.status') }}",
        data: { "_token": "{{ csrf_token() }}" , id: apps_id, status:status },
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

