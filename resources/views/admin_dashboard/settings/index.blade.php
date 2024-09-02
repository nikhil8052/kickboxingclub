@extends('admin_layout/master')
@section('content')

<style>
     .close{
          text-decoration: none;
     }
</style>

<div class="nk-content ">
           <!-- ---------------- Modal -------------------- -->
     <div class="modal fade" id="myModal">
          <div class="modal-dialog" >
               <div class="modal-content">
                    <div class="modal-header">
                         <h5 class="modal-title" id="modalLabel" >Add Trials Sold INCLUDE Filter</h5>
                         <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                              <em class="icon ni ni-cross"></em>
                         </a>
                    </div>
                    <div class="modal-body" id="modalBody">
                         <form id="trialForm"   method="POST">
                              @csrf
                              <div class="form-group">
                                   <label class="form-label" for="name">Name</label>
                                   <div class="form-control-wrap">
                                        <input type="text" class="form-control" id="name" name="name" value="">
                                   </div>
                                   <span class="text text-danger" id="error" style="display:none;">This field is required</span>
                              </div>
                              <div class="form-group">
                                   <button type="button" class="btn btn-dark add-trials-btn">Add</button>
                              </div>
                         </form>
                    </div>
               </div>
          </div>
     </div>
     <!--- -------------- End Model ------------------ -->
     <div class="container-fluid">
          <div class="nk-content-inner">
               <div class="nk-content-body">
                    <div class="nk-block">
                         <h6 class="nk-block-title page-title">Marianatek Credentials</h4>
                         <div class="card card-bordered">
                              <div class="card-inner col-md-6">
                                   <form id="credentialsForm" action="{{ url('admin-dashboard/update/credentials') }}" method="post">
                                        @csrf
                                        <div class="form-group">
                                             <label class="form-label" for="username">Username</label>
                                             <div class="form-control-wrap">
                                                  <input type="text" class="form-control" name="username" id="username" value="{{ $user->email ?? '' }}">
                                             </div>
                                            <span class="text text-danger" id="email-error" style="display:none;">This field is required</span>
                                        </div>
                                        <div class="form-group">
                                             <label class="form-label" for="password">Password</label>
                                             <div class="form-control-wrap">
                                                  <input type="password" class="form-control" name="password" id="password" placeholder="*******">
                                             </div>
                                            <span class="text text-danger" id="password-error" style="display:none;">This field is required</span>
                                        </div>
                                        <div class="form-group">
                                             <button type="button" class="btn btn-dark update-credentials">Update</button>
                                        </div>
                                   </form>
                              </div>
                         </div>
                         <div class="card card-bordered card-full">
                              <div class="card-inner">
                                   <form id="trails-form">
                                   <div class="row">
                                        <div class="col-md-6">
                                             <div class="card-head">
                                                  <h6 class="title">Trials Sold INCLUDE Filter</h6>
                                                  <button type="button" class="btn btn-dark add-btns trials-add" btn-type="trial">Add</button>
                                             </div>
                                             <div class="form-control-wrap">
                                                  <select class="form-select js-select2 select2-hidden-accessible" name="include[]" id="include" multiple="" data-placeholder="Select Multiple options" data-select2-id="10" tabindex="-1" aria-hidden="true">
                                                  @if(isset($membershipTrails) && $membershipTrails != null)
                                                       @foreach ($membershipTrails as $trials)
                                                            <option selected value="{{ $trials->id }}">{{ $trials->name ?? '' }}</option>
                                                       @endforeach
                                                  @endif
                                                  </select>
                                             </div>
                                        </div>
                                        <div class="col-md-6">
                                             <div class="card-head">
                                                  <h6 class="title">Daily Active Members EXCLUDE Filter</h6>
                                                  <button type="button" class="btn btn-dark active-add add-btns "  btn-type="exclude" >Add</button>
                                             </div>
                                             <div class="form-control-wrap">
                                                  <select class="form-select js-select2 select2-hidden-accessible" name="exclude[]" id="exclude" multiple="" data-placeholder="Select Multiple options" data-select2-id="9" tabindex="-1" aria-hidden="true">
                                                  @if(isset($activeMembers) && $activeMembers != null)
                                                       @foreach ($activeMembers as $members)
                                                            <option selected value="{{ $members->id }}">{{ $members->name ?? '' }}</option>
                                                       @endforeach
                                                  @endif
                                                  </select>
                                             </div>
                                        </div>
                                   </div>
                              </div>
               
                         
                         </div>
                    </div>
               </div>
          </div>
     </div>
</div>


<script>
     $(document).ready(function() {
          $('#include').on('change',function(){
               $('#overlay').show();
               var data = {
                    id: $(this).val(),
                    _token: "{{ csrf_token() }}"
               }

               $.ajax({
                    url: "{{ url('admin-dashboard/update/trials') }}",
                    type: "post",
                    data: data,
                    dataType: "json",
                    success: function(response){
                         if(response.success === true){
                              $('#overlay').hide();
                         }
                    },
                    error: function(error) {
                         $('#overlay').hide();
                    }
               })            
          })

          $('#exclude').on('change',function(){
               $('#overlay').show();
               var data = {
                    id: $(this).val(),
                    _token: "{{ csrf_token() }}"
               }

               $.ajax({
                    url: "{{ url('admin-dashbaord/update/members') }}",
                    type: "post",
                    data: data,
                    dataType: "json",
                    success: function(response){
                         if(response.success === true){
                              $('#overlay').hide();
                         }
                    },
                    error: function(error) {
                         $('#overlay').hide();
                    }
               })     
          })
     })
</script>

<script>
     $(document).ready(function(){
          $('.update-credentials').on('click',function(e){
               e.preventDefault(); 
               var username = $('#username').val();
               var password = $('#password').val();
               var isValid = true;

               if(username == '' || username == null){
                    $('#email-error').show();
                    isValid = false;
               }else{
                    $('#email-error').hide();
               }

               if(password == ''){
                    $('#password-error').show();
                    isValid = false;
               }else{
                    $('#password-error').hide();
               }

               if(isValid){
                    $('#credentialsForm').submit();
               }
          })  
     })

</script>



<script>

     $('.add-btns').on('click',(e)=>{
          var name = $('#name').val('');
          var button = $(e.target);
          var nameAttr = button.attr('btn-type');
          $('#myModal').modal('show');

          if(nameAttr=="trial"){
               $('#modalLabel').text('Add Trials Sold INCLUDE Filter');
               $('#trialForm').attr('action', "{{ url('admin-dashboard/add/trials') }}");
               return ; 
          }else if(nameAttr=="exclude"){
               $('#modalLabel').text('Add Daily Active Members EXCLUDE Filter');
               $('#trialForm').attr('action', "{{ url('admin-dashboard/add/active/members') }}");
          }else {
               alert(" Something went wrong...");
          }
     })

     $('.add-trials-btn').on('click',(e)=>{

          var name = $('#name').val();

          if(name == null || name == ''){
               $('#error').show();
               return 
          }

          $('#trialForm').submit();          
     })
</script>


@endsection