@extends('admin_layout.master')
@section('content')
  
<div class="nk-content">
    <div class="container-fluid">
        <div class="card card-bordered card-preview d-none" id="addnewcard">
            <div class="card-inner">
                <div class="preview-block">
                    <div class="d-flex justify-content-between">
                        <span class="preview-title-lg overline-title">Add User</span>
                        <span class="close"><i class="fas fa-times"></i></span>
                    </div>
                    <div class="row gy-4">
                        <div class="col-sm-12">
                            <form id="user-form" action="{{ route('user.addProcc') }}" method="POST">
                                @csrf
                                <input type="hidden" id="user_id" name="id">
                                <div id="Role_Div" >
                                    <div  class="d-flex">
                                        <div class="col-md-6 p-2">
                                            <div class="form-group">
                                                <label for="user_type">Role</label>
                                                <div class="form-control-wrap">
                                                    <select class="form-select" name="user_type" id="user_type">
                                                        <option value="">Select Role</option>
                                                        <option value="employee">Employee</option>
                                                        <option value="user">User</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 p-2" id="select_employee" style="display:none">
                                            <div class="form-group">
                                                <label for="employee">Employee</label>
                                                <div class="form-control-wrap">
                                                    <select class="form-select" name="employee" id="employee">
                                                        <option value="">Select Employee</option>
                                                        @foreach($employees as $emp)
                                                            <option value="{{ $emp->employee_id }}">{{ $emp->user->full_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <span class="text-danger" id="select_employee_error" style="display: none;">Please select Employee.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="hidden-form" id="hidden-form" style="display:none">
                                    <div class="d-flex">
                                        <div class="col-md-6 p-2">
                                            <div class="form-group">
                                                <label for="first_name">First Name</label>
                                                <input type="text" id="first_name" name="first_name" placeholder="John" class="form-control" />
                                                <span class="text-danger" id="name_error" style="display: none;">Name is required.</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 p-2">
                                            <div class="form-group">
                                                <label for="last_name">Last Name</label>
                                                <input type="text" id="last_name" name="last_name" placeholder="Doe" class="form-control" />
                                            </div>
                                        </div>
                                    </div> 
                                
                                    <div class="d-flex">
                                        <div class="col-md-6 p-2">
                                            <div class="form-group">
                                                <label for="email">Email</label>
                                                <input type="text" id="email" name="email" placeholder="John@gmail.com" class="form-control" />
                                                <span class="text-danger" id="email_error" style="display: none;">Email is required.</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 p-2">
                                            <div class="form-group">
                                                <label  for="password">Password</label>
                                                <div class="form-control-wrap">
                                                    <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch lg" data-target="password">
                                                        <em class="passcode-icon icon-hide icon ni ni-eye"></em>
                                                        <em class="passcode-icon icon-show icon ni ni-eye-off"></em>
                                                    </a>
                                                    <input autocomplete="new-password" type="password" class="form-control" placeholder="*****" id="password" name="password">
                                                </div>
                                                <span class="text-danger" id="password_error" style="display: none;">Password is required.</span>
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="d-flex">
                                        <div class="col-md-6 p-2">
                                            <div class="form-group">
                                                <label for="permissions">Permissions</label>
                                                <div class="form-control-wrap">
                                                    <select class="form-select js-select2 select2-hidden-accessible" name="permissions[]" id="permissions" multiple="" data-placeholder="Select Multiple options" data-select2-id="9" tabindex="-1" aria-hidden="true">
                                                        @foreach ($permissions as $permission)
                                                            <option selected value="{{ $permission->id }}">{{ $permission->name ?? '' }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <span class="text-danger" id="permission_error" style="display: none;">Please assign at least one permission.</span>
                                            </div>
                                        </div>
                                    </div> 
                                    <div  class="col-md-12 p-2 d-flex justify-content-end">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-dark" id="Save_records">Add User</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="nk-block nk-block-lg my-4">
            <div class="nk-block-head">
                <div class="nk-block-head-content d-flex justify-content-between">
                    <h4 class="nk-block-title">Users</h4>
                    <button class="btn btn-dark" id="addnew">Add New</button>
                </div>
            </div>
            <div class="card card-bordered card-preview">
                <table class="table table-tranx" id="table">
                    <thead>
                        <tr class="tb-tnx-head">
                            <th class="tb-tnx-id"><span class="">#</span></th>
                            <th class="tb-tnx-info">
                                <span class="tb-tnx-desc d-none d-sm-inline-block">
                                    <span>Type</span>
                                </span>
                            </th>
                            <th class="tb-tnx-info">
                                <span class="tb-tnx-desc d-none d-sm-inline-block">
                                    <span> Name</span>
                                </span>
                            </th>
                            <th class="tb-tnx-info">
                                <span class="tb-tnx-desc d-none d-sm-inline-block">
                                    <span>Email</span>
                                </span>
                            </th>
                          
                            <th class="tb-tnx-action">
                                <span></span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr class="tb-tnx-item">
                                <td class="tb-tnx-id">
                                   <span>{{ $loop->iteration?? '' }}</span>
                                </td>
                                <td class="tb-tnx-info">
                                    <div class="tb-tnx-desc">
                                        <input type="text" data-id="{{ $user->id ?? '' }}"
                                            class="titleName name{{ $user->type ?? '' }}" value="{{ $user->type ?? '' }}" disabled
                                            style="border: none; background: transparent;" />
                                    </div>
                                </td>
                                <td class="tb-tnx-info">
                                    <div class="tb-tnx-desc">
                                        <input type="text" data-id="{{ $user->id ?? '' }}"
                                            class="titleName name{{ $user->id ?? '' }}" value="{{ $user->name ?? '' }}" disabled
                                            style="border: none; background: transparent;" />
                                    </div>
                                </td>
                                <td class="tb-tnx-info">
                                    <div class="tb-tnx-desc">
                                        <input type="text" data-id="{{ $user->id ?? '' }}"
                                            class="titleName name{{ $user->email ?? '' }}" value="{{ $user->email ?? '' }}" disabled
                                            style="border: none; background: transparent;" />
                                    </div>
                                </td>

                                <td class="tb-tnx-info d-flex p-3">
                                    <!-- <div class="dropdown drop">
                                        <a class="text-soft dropdown-toggle btn btn-icon btn-trigger"
                                            data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-xs">
                                            <ul class="link-list-plain"> -->
                                               <a  data-id ="{{ $user->id ?? '' }}" data-firstname="{{ $user->first_name }}" data-lastname="{{ $user->last_name }}" data-email="{{ $user->email }}"
                                                        data-permissions="{{ json_encode($user->permissionIds()) }}"
                                                        class="edit-category m-2 btn btn-light"><em class="icon ni ni-edit"></em></a>
                                               <a href="{{ url('admin-dashboard/user-remove/'.$user->id) }}" data-id ="{{ $user->id ?? '' }}"
                                                        class="remove-category m-2 btn btn-light"><em class="icon ni ni-trash"></em></a>
                                            <!-- </ul>
                                        </div>
                                    </div> -->
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('#user_type').on('change',function(){
            value = $(this).val();
            if(value == 'user'){
                $('#hidden-form').show();
                $('#select_employee').hide();
            } else {
                $('#hidden-form').hide();
                $('#select_employee').show();
            }
        });
        $('#employee').on('change',function(){
            EmployeeId = $(this).val();
            if(EmployeeId != null && EmployeeId != undefined && EmployeeId !="" ){
                GetEmployeeData(EmployeeId);
            } 
        });

        function GetEmployeeData(EmployeeId){
            $('#overlay').show();
            $.ajax({
            url: "{{ url('/get-employee-data') }}", 
            type: 'GET', 
            data: { id: EmployeeId }, 
            success: function(response) {
                $('#overlay').hide();
                if(response.employee){
                    $('#hidden-form').show();
                    $('#first_name').val(response.employee.user.first_name);
                    $('#last_name').val(response.employee.user.last_name);
                    $('#email').val(response.employee.user.email);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", status, error);
                $('#overlay').hide();
            }
        });
        }
    });
</script>
<script>
    $('#addnew').click(function(){
        $('#addnewcard').removeClass('d-none');
        $(this).hide();
        $('#hidden-form').hide();
        $('#Role_Div').show();
    });
    $('.close').click(function(){
        $('#addnewcard').addClass('d-none');
        $('#addnew').show();

        $('#hidden-form').hide();
        $('#Role_Div').show();
    });
</script>
<script>
    $(document).ready(function(){
        $("body").delegate(".edit-category", "click", function (e) {
            $('#addnewcard').removeClass('d-none');
            $('#addnew').hide();

            var id = $(this).data('id');
            var firstname = $(this).data('firstname');
            var lastname = $(this).data('lastname');
            var email = $(this).data('email');
            var permissions = $(this).data('permissions');

            $('#hidden-form').show();
            $('#Role_Div').hide();
            
            $('#user_id').val(id);
            $('#first_name').val(firstname);
            $('#last_name').val(lastname);
            $('#email').val(email);

            $('#permissions').val(permissions).trigger('change');

            $('#Save_records').html('Update User');
            // $('#add_new').removeClass('d-none');
            window.scrollTo(0, 0);
                
        });
        $("body").delegate("#addnew", "click", function (e) {
            $('#user_id').val('');
            $('#first_name').val('');
            $('#last_name').val('');
            $('#email').val('');

            $('#name_error').hide();
            $('#email_error').hide();
            $('#password_error').hide();
            $('#permission_error').hide();

            $('#hidden-form').hide();
            $('#Role_Div').show();

            let allPermissionValues = $('#permissions option').map(function() {
                return $(this).val();
            }).get();

            $('#permissions').val(allPermissionValues).trigger('change');
            // $(this).addClass('d-none');
            $('#Save_records').html('Add User');
            $('#parent_category').prop('disabled',false); 
                
        });
        $("body").delegate(".close", "click", function (e) {
            $('#user_id').val('');
            $('#first_name').val('');
            $('#last_name').val('');
            $('#email').val('');

            $('#name_error').hide();
            $('#email_error').hide();
            $('#password_error').hide();
            $('#permission_error').hide();

            $('#hidden-form').hide();
            $('#Role_Div').show();

            let allPermissionValues = $('#permissions option').map(function() {
                return $(this).val();
            }).get();

            $('#permissions').val(allPermissionValues).trigger('change');
            // $(this).addClass('d-none');
            $('#Save_records').html('Add User');
            $('#parent_category').prop('disabled',false); 

            // $('#addnewcard').addClass('d-none');
            // $('#addnew').show();
                
        });

        $('#user-form').submit(function(e) {
            e.preventDefault();

            var action = true;

            var userId = $('#user_id').val();
            var first_name = $('#first_name').val();
            var email = $('#email').val();
            var password = $('#password').val();
            var permissions = $('#permissions').val();

            if(first_name == ''){
                $('#name_error').show();
                action = false;
            }

            if(permissions.length < 1){
                $('#permission_error').show();
                action = false;
            }

            if(email == ''){
                $('#email_error').show();
                action = false;
            }

            if(userId == '' && password == ''){
                $('#password_error').show();
                action = false;
            }

            if(action === true) {
                this.submit();
            }
        });

        $('#first_name').on('change',function(){
            $('#name_error').hide();
        });
        $('#email').on('change',function(){
            $('#email_error').hide();
        });
        $('#password').on('change',function(){
            $('#password_error').hide();
        });
        $('#permissions').on('change',function(){
            $('#permission_error').hide();
        });
    });
</script>

@endsection