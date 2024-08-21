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
                            <form action="{{ route('user.addProcc') }}" method="POST">
                                @csrf
                                <input type="hidden" id="user_id" name="id">
                                <div class="d-flex">
                                    <div class="col-md-6 p-2">
                                        <div class="form-group">
                                            <label for="first_name">First Name</label>
                                            <input type="text" id="first_name" name="first_name" placeholder="John" class="form-control" />
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
                                        </div>
                                    </div>
                                </div> 
                                <div class="col-md-3 p-2">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-dark" id="Save_records">Add User</button>
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
                    <button class="btn btn-primary" id="addnew">Add New</button>
                </div>
            </div>
            <div class="card card-bordered card-preview">
                <table class="table table-tranx" id="table">
                    <thead>
                        <tr class="tb-tnx-head">
                            <th class="tb-tnx-id"><span class="">#</span></th>
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
                                <span>Action</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody> <?php $a = 1; ?>
                        @foreach ($users as $user)
                            <tr class="tb-tnx-item">
                                <td class="tb-tnx-id">
                                    <a href="#"><span>{{ $a++ ?? '' }}</span></a>
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
                                        <input type="text" data-id="{{ $user->email ?? '' }}"
                                            class="titleName name{{ $user->email ?? '' }}" value="{{ $user->email ?? '' }}" disabled
                                            style="border: none; background: transparent;" />
                                    </div>
                                </td>

                                <td class="tb-tnx-action">
                                    <!-- <div class="dropdown drop">
                                        <a class="text-soft dropdown-toggle btn btn-icon btn-trigger"
                                            data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-xs">
                                            <ul class="link-list-plain"> -->
                                                <li><a  data-id ="{{ $user->id ?? '' }}" data-name="{{ $user->name }}" data-email="{{ $user->email }}"
                                                        data-permissions="{{ json_encode($user->permissionIds()) }}"
                                                        class="edit-category">Edit</a></li>
                                                <li><a href="{{ url('admin-dashboard/user-remove/'.$user->id) }}" data-id ="{{ $user->id ?? '' }}"
                                                        class="remove-category">Remove</a></li>
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
    $('#addnew').click(function(){
        $('#addnewcard').removeClass('d-none');
        $(this).hide();
        
    });
    $('.close').click(function(){
        $('#addnewcard').addClass('d-none');
        $('#addnew').show();
    });
</script>
<script>
    $(document).ready(function(){
        $("body").delegate(".edit-category", "click", function (e) {
            $('#addnewcard').removeClass('d-none');
            $('#addnew').hide();

            var id = $(this).data('id');
            var name = $(this).data('name');
            var email = $(this).data('email');
            var permissions = $(this).data('permissions');
            
            $('#user_id').val(id);
            $('#first_name').val(name.split(' ')[0]);
            $('#last_name').val(name.split(' ')[1]);
            $('#email').val(email);

            $('#permissions').val(permissions).trigger('change');

            $('#button_value').html('update');
            $('#add_new').removeClass('d-none');
            window.scrollTo(0, 0);
                
        });
        $("body").delegate(".addnew", "click", function (e) {
            $('#user_id').val('');
            $('#first_name').val('');
            $('#last_name').val('');
            $('#email').val('');

            let allPermissionValues = $('#permissions option').map(function() {
                return $(this).val();
            }).get();

            $('#permissions').val(allPermissionValues).trigger('change');
            $(this).addClass('d-none');
            $('#button_value').html('Add');
            $('#parent_category').prop('disabled',false); 
                
        });
        $("body").delegate(".close", "click", function (e) {
            $('#user_id').val('');
            $('#first_name').val('');
            $('#last_name').val('');
            $('#email').val('');

            let allPermissionValues = $('#permissions option').map(function() {
                return $(this).val();
            }).get();

            $('#permissions').val(allPermissionValues).trigger('change');
            $(this).addClass('d-none');
            $('#button_value').html('Add');
            $('#parent_category').prop('disabled',false); 
                
        });
    });
</script>
@endsection