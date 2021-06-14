function confirmDelete (e) {
    Swal.fire({
        title: 'Are you sure?',
        text: "Are you sure you want to delete this record?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
    }).then((result) => {
        if (result.value) {
            $formId = $(e).data("form-id");
            $("#"+$formId).submit();
            return true;
        } else {
            return false;
        }
    })
}

$(document).on('click', '.act-delete', function(e) {    
        e.preventDefault();
        var action = $(this).attr('href');
        Swal.fire({
            title: 'Are you sure?',
            text: "Are you sure you want to delete this record?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
            
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: action,
                    type: 'DELETE',
                    dataType: 'json',
                    beforeSend: addOverlay,
                    data: {
                        _token: $('meta[name="csrf_token"]').attr('content')
                    },
                    success: function(result) {
                        if ($.trim(result['category_id']) && $.trim(result['type']) == 'category'){  
                            window.location.href = result['category_id'];
                        } 
                        if (oTable == undefined || oTable == null) {
                            window.location.href = window.location.href;
                        } else {
                            Swal.fire({
                              position: 'center',
                              icon: 'error',
                              title: result['message'],
                              showConfirmButton: false,
                              timer: 1000
                          })
                            // swal("success","It was succesfully deleted!");
                            if (typeof oTable.draw !== "undefined") {
                                oTable.draw();
                            } else if (typeof oTable.fnDraw !== "undefined") {
                                oTable.fnDraw();
                            }
                        }
                    },
                    complete: removeOverlay
                });
                return true;
            } else {
                return false;
            }
        })
    });

function addOverlay() { $('<div id="overlayDocument"></div>').appendTo(document.body); }

function removeOverlay() { $('#overlayDocument').remove(); }

