$(function() {
    $('.delete').click(function(){
  const swalWithBootstrapButtons = Swal.mixin({
  customClass: {
    confirmButton: 'btn btn-success',
    cancelButton: 'btn btn-danger'
  },
  buttonsStyling: false
})

swalWithBootstrapButtons.fire({
  title: 'Are you sure want to delete this record?',
  icon: 'warning',
  showCancelButton: true,
  confirmButtonText: 'Yes',
  cancelButtonText: 'No',
  }).then((result) => {
  if (result.isConfirmed) {
     $.ajax({
        method: "DELETE",
        url: deleteURL + $(this).data("id")
    //   data: { id: $(this).data("id")}
    })
    .done(function(data) {
      window.location.reload();
    //   alert("SUCCESS");
    })
    .fail(function(response) {
      console.log(response);

  Swal.fire({
  icon: 'error',
  title: 'Oops...',
  text: 'Something went wrong!'
})
        });
           }
    })
  });
    //  console.log($('.delete'));
});