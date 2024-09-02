@extends('admin_layout.master')
@section('content')
    <div class="nk-content">
        <div class="container-fluid">
            <div class="card card-bordered card-preview d-none" id="addnewcard">
                <div class="card-inner">
                    <div class="preview-block">
                        <div class="d-flex justify-content-between">
                            <span class="preview-title-lg overline-title">Add</span>
                            <span class="close"><i class="fas fa-times"></i></span>
                        </div>
                        <div class="row gy-4">
                            <div class="col-sm-12">
                                <form id="soldmembership-form" action="{{ route('membership.sold.addProcc') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" id="id">
                                    <div class="hidden-form" id="hidden-form" >
                                        <div class="d-flex">
                                            <div class="col-md-6 p-2">
                                                <div class="form-group">
                                                    <label for="m_name">Name</label>
                                                    <input type="text" id="m_name" name="m_name"  class="form-control" />
                                                </div>
                                                <span id="m_name_error" style="display: none"  class="text-danger">Enter membership name</span>
                                            </div>
                                            <div class="col-md-6 p-2">
                                                <div class="form-group">
                                                    <label for="user_type">Membership</label>
                                                    <div class="form-control-wrap">
                                                        <select class="form-select  js-select2 select2-hidden-accessible" name="membership_type" id="membership_type">
                                                            <option  value=""></option>
                                                            @foreach ($membership_types as $mtype)
                                                                <option value="{{ $mtype->id }}">{{ $mtype->type }}</option>
                                                            @endforeach
                                                        </select>
                                                        <span id="membership_type_error" style="display: none" class="text-danger">Select membership</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 
                                        <div class="d-flex">
                                            <div class="col-md-6 p-2">
                                                <div class="form-group">
                                                    <label for="email">Weekly Billing</label>
                                                    <input type="text" id="weekly_billing" name="weekly_billing"  class="form-control" />
                                                </div>
                                                <span id="weekly_billing_error" style="display: none" class="text-danger">Enter Weekly Billing</span>
                                            </div>
                                            <div class="col-md-6 p-2">
                                                <div class="form-group">
                                                    <label for="email">Monthly Billing</label>
                                                    <input type="text" id="monthly_billing" name="monthly_billing"  class="form-control" readonly/>
                                                </div>
                                            </div>
                                        </div> 
                                       
                                        <div class="d-flex">
                                            @if(isset(auth()->user()->employee))
                                                <div class="col-md-6 p-2">
                                                    <div class="form-group">
                                                        <label for="location">Location</label>
                                                        <div class="form-control-wrap">
                                                            <select class="form-select js-select2 select2-hidden-accessible" name="location" id="location">
                                                                @foreach (json_decode(auth()->user()->employee->turfs,true) as $location)
                                                                    @if($location['type'] == 'locations')
                                                                        @foreach($locations as $l)
                                                                            @if($location['id'] == $l->location_id)
                                                                                <option  value="{{ $l->location_id }}">{{ $l->name ?? '' }}</option>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="col-md-6 p-2">
                                                <div class="form-group">
                                                    <label for="trial">Trial</label>
                                                    <div class="form-control-wrap">
                                                        <select class="form-select js-select2 select2-hidden-accessible" name="trial" id="trial">
                                                            <option  value=""></option>
                                                            @foreach ($membershipstrail as $mtrial)
                                                                <option  value="{{ $mtrial->id }}">{{ $mtrial->name ?? '' }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 
                                        <div  class="col-md-12 p-2 d-flex justify-content-end">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-dark" id="Save_records">Add</button>
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
                        <h4 class="nk-block-title">Membership Sold</h4>
                        <button class="btn btn-dark" id="addnew">Add New</button>
                    </div>
                </div>
                <div class="card card-bordered card-preview">
                    <table class="table table-tranx" id="table">
                        <thead>
                            <tr class="tb-tnx-head">
                                <th class="tb-tnx-info">
                                    <span class="">
                                        <span>Name</span>
                                    </span>
                                </th>
                                <th class="tb-tnx-info">
                                    <span class="">
                                        <span>Type</span>
                                    </span>
                                </th>
                                <th class="tb-tnx-info">
                                    <span class="">
                                        <span> Weekly billing</span>
                                    </span>
                                </th>
                                <th class="tb-tnx-info">
                                    <span class="">
                                        <span> Monthly billing</span>
                                    </span>
                                </th>
                                {{-- <th class="tb-tnx-info">
                                    <span>Trial</span>
                                </th> --}}
                                <th class="tb-tnx-info">
                                    <span>Date</span>
                                </th>
                                <th class="tb-tnx-action">
                                    <span></span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($soldmemberships as $sold)
                                <tr class="tb-tnx-item">
                                    <td class="tb-tnx-info">
                                        <div class="tb-tnx-">
                                            <input type="text" data-id="{{ $sold->id ?? '' }}"  
                                                class="titleName name{{ $sold->name ?? '' }}" value="{{ $sold->name ?? '' }}" disabled
                                                style="border: none; background: transparent;" />
                                        </div>
                                    </td>
                                    <td class="tb-tnx-info">
                                        <div class="tb-tnx-">
                                            <input type="text" data-id="{{ $sold->id ?? '' }}"
                                                class="titleName name{{ $sold->id ?? '' }}" value="{{ $sold->membershiptype->type ?? '' }}" disabled
                                                style="border: none; background: transparent;" />
                                        </div>
                                    </td>
                                    <td class="tb-tnx-info">
                                        <div class="tb-tnx-">
                                            <input type="text" data-id="{{ $sold->id ?? '' }}"
                                                class="titleName name{{ $sold->id ?? '' }}" value="${{ $sold->weekly_billing ?? '' }}" disabled
                                                style="border: none; background: transparent;" />
                                        </div>
                                    </td>
                                    <td class="tb-tnx-info">
                                        <div class="tb-tnx-">
                                            <input type="text" data-id="{{ $sold->id ?? '' }}"
                                                class="titleName name{{ $sold->id ?? '' }}" value="${{ $sold->monthly_billing ?? '' }}" disabled
                                                style="border: none; background: transparent;" />
                                        </div>
                                    </td>
                                    {{-- <td class="tb-tnx-info">
                                        @if($sold->trial_id)
                                           <span>{{ $sold->trial->name ?? null }}</span>
                                        @else
                                            <span>null</span>
                                        @endif
                                    </td> --}}
                                    <td class="tb-tnx-info">
                                        <div class="tb-tnx-">
                                            <input type="text" data-id="{{ $sold->id ?? '' }}"
                                                class="titleName name{{ $sold->id ?? '' }}" value="{{ $sold->sold_date ?? null }}" disabled 
                                                style="border: none; background: transparent;" />
                                        </div>
                                    </td>
                                    <td class="tb-tnx-info d-flex p-3">
                                        <a  data-id ="{{ $sold->id }}" data-name="{{ $sold->name }}" data-wbilling="{{ $sold->weekly_billing }}" data-mbilling="{{ $sold->monthly_billing }}"
                                                data-memtype="{{ $sold->membership_typeId }}" data-trial="{{ $sold->trial_id }}" data-location="{{ $sold->location_id }}"
                                                class="edit-category m-2 btn btn-light"><em class="icon ni ni-edit"></em></a>
                                        <a href="{{ url('admin-dashboard/membership-sold-remove/'.$sold->id) }}" data-id ="{{ $sold->id ?? '' }}"
                                                class="remove-category m-2 btn btn-light"><em class="icon ni ni-trash"></em></a>
                                          
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
            $('#weekly_billing').on('change', function() {
                var weeklyBilling = $(this).val(); 
                
                var date = new Date();
                var year = date.getFullYear();
                var month = date.getMonth() + 1; 
                
                var daysInMonth = new Date(year, month, 0).getDate();
                var weeksInMonth = daysInMonth / 7;
                var monthlyBilling = (weeklyBilling * weeksInMonth).toFixed(2);
                
                $('#monthly_billing').val(monthlyBilling);
            });

            $('#soldmembership-form').submit(function(e) {
                e.preventDefault();

                var action = true;

                var m_name = $('#m_name').val();
                var membership_type = $('#membership_type').val();
                var weekly_billing = $('#weekly_billing').val();

                if(m_name == ''|| m_name == null || m_name == undefined){
                    $('#m_name_error').show();
                    action = false;
                }

                if(membership_type == '' || membership_type == null || membership_type == undefined){
                    $('#membership_type_error').show();
                    action = false;
                }

                if(weekly_billing == ''|| weekly_billing == null || weekly_billing == undefined){
                    $('#weekly_billing_error').show();
                    action = false;
                }

                if(action === true) {
                    this.submit();
                }
            });

            $('#m_name').on('change',function(){
                $('#m_name_error').hide();
            });
            $('#membership_type').on('change',function(){
                $('#membership_type_error').hide();
            });
            $('#weekly_billing').on('change',function(){
                $('#weekly_billing_error').hide();
            });

            $("body").delegate(".edit-category", "click", function (e) {
                $('#addnewcard').removeClass('d-none');
                $('#addnew').hide();

                var id = $(this).data('id');
                var name = $(this).data('name');
                var weeklyB = $(this).data('wbilling');
                var monthlyB = $(this).data('mbilling');
                var memtype = $(this).data('memtype');
                var mtrial = $(this).data('trial');
                var location = $(this).data('location');

                
                $('#id').val(id);
                $('#m_name').val(name);
                $('#weekly_billing').val(weeklyB);
                $('#monthly_billing').val(monthlyB);

                $('#membership_type').val(memtype).trigger('change');
                $('#trial').val(mtrial).trigger('change');
                $('#location').val(location).trigger('change');


                $('#Save_records').html('Update');
                window.scrollTo(0, 0);
                    
            });
            $("body").delegate("#addnew", "click", function (e) {
                $('#id').val('');
                $('#m_name').val('');
                $('#weekly_billing').val('');
                $('#monthly_billing').val('');

                $('#name_error').hide();
                $('#email_error').hide();
                $('#password_error').hide();
                $('#permission_error').hide();

                $('#permissions').val('').trigger('change');
                $('#Save_records').html('Add');
        });
     
        });
    </script>
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
@endsection