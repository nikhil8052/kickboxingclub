function ajaxRequest(url="",method="GET",data={}){
     $.ajax({
          url: $('meta[name="base-url"]').attr('content'),
          headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
          },
     })
}