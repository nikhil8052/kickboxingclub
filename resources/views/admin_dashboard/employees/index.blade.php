@extends('admin_layout.master') 
@section('content')

@section('css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />

<style>
    table#users_data_table .regular-pay {
        cursor: pointer;
        text-decoration: underline;
    }

    .close{
        text-decoration: none;
    }
</style>

@endsection


<div class="nk-content">
    <div class="container-fluid">
        <div class="d-flex paper card-preview card-bordered p-4 mb-3">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="date-range-picker">Date Filter</label>
                    <input type="text" id="date-range-picker" class="form-control" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <button class="btn btn-dark">Search</button>
                </div>
            </div>
        </div>

        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block">
                    <div class="card card-bordered card-preview mt-5 p-4">
                        <table id="users_data_table" class="nowrap nk-tb-list nk-tb-ulist table table-tranx" data-auto-responsive="false">
                            <thead>
                                <tr class="nk-tb-item nk-tb-head">
                                    <th class="nk-tb-col"><span class="sub-text">ID</span></th>
                                    <th class="nk-tb-col"><span class="sub-text">Employee Name</span></th>
                                    <th class="nk-tb-col"><span class="sub-text">Email</span></th>
                                    <th class="nk-tb-col"><span class="sub-text">Country</span></th>
                                    <th class="nk-tb-col"><span class="sub-text">Location</span></th>
                                    <th class="nk-tb-col"><span class="sub-text">Regular Pay</span></th>
                                    <th class="nk-tb-col"><span class="sub-text">Instructor Pay</span></th>
                                </tr>
                            </thead>
                            <tbody >
                                
                            </tbody>
                        </table>
                    </div>
                    <div class="modal fade" id="modalForm">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Update Pay Rate</h5>
                                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                                        <em class="icon ni ni-cross"></em>
                                    </a>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ url('/admin-dashboard/pay-rates/procc') }}" id="payRateform" method="post">
                                        @csrf
                                        <input type="hidden" id="employee_id" name="employee_id" value="">
                                        <div class="form-group">
                                            <label class="form-label" for="regular_pay">Regular Pay</label>
                                            <div class="form-control-wrap">
                                                <input type="number" class="form-control" id="regular_pay" name="regular_pay" value="">
                                            </div>
                                            <span class="text text-danger" id="regular-error" style="display:none;">This field is required</span>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label" for="instructor_pay">Instructor Pay</label>
                                            <div class="form-control-wrap">
                                                <input type="number" class="form-control" id="instructor_pay" name="instructor_pay" value="">
                                            </div>
                                            <span class="text text-danger" id="instructor-error" style="display:none;">This field is required</span>
                                        </div>
                                        <div class="form-group">
                                            <button type="button" class="btn btn-dark update_rates_btn">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('js')

<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

@endsection


<script type="text/javascript">
    $(document).ready(function() {
        $('#users_data_table').DataTable({
            processing: true,
            serverSide: false, 
            ajax: {
                url: "{{ url('/admin-dashboard/get-employees') }}", 
                type: 'GET',
                dataSrc: '' 
            },
            columns: [
                { data: 'employee_id' },
                { data: 'user.full_name'},
                { data: 'user.email'},
                { data: 'user.country',
                    render: function(data, type, row) {
                        return data ? data : 'null';
                    }
                },
                { data: 'user.location.name',
                    render: function(data, type, row) {
                        return data ? data : 'null'; 
                    }
                },
                { data: 'payrate.regular_pay',
                    render: function(data, type, row) {
                        return data ? `$${data}` : 'null'; 
                    },
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).addClass('regular-pay');
                    }
                },
                { data: 'payrate.instructor_pay',
                    render: function(data, type, row) {
                        return data ? `$${data}` : 'null'; 
                    },
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).addClass('regular-pay');
                    }
                }
            ]
        });

        var table = $('#users_data_table').DataTable();
        $('#users_data_table').on('click','.regular-pay',function(){
            var row = $(this).closest('tr');
            var data = table.row(row).data();

            var id = data['employee_id'];
            var regular_pay = data.payrate ? data.payrate.regular_pay : undefined;
            var instructor_pay = data.payrate ? data.payrate.instructor_pay : undefined;

            $('#employee_id').val(id);
            $('#regular_pay').val(regular_pay);
            $('#instructor_pay').val(instructor_pay);
            $('#regular-error').hide();
            $('#instructor-error').hide();
            $('#modalForm').modal('show');
        })
    });


    $('.update_rates_btn').on('click',(e)=>{
        e.preventDefault(); 
        var isValid = true;
        var regularPay = $('#regular_pay').val();
        var instructorPay = $('#instructor_pay').val();

        if(regularPay == null || regularPay == ''){
            $('#regular-error').show();
            isValid = false;
        }else{
            $('#regular-error').hide();
        }

        if(instructorPay == null || instructorPay == ''){
            $('#instructor-error').show();
            isValid = false;
        }else{
            $('#instructor-error').hide();
        }

        if(isValid){
            $('#payRateform').submit();
        }
    });

</script>

<script type="text/javascript">
    $(document).ready(function () {
        $("#date-range-picker").daterangepicker(
            {
                opens: "left",
                ranges: {
                    Today: [moment(), moment()],
                    Yesterday: [moment().subtract(1, "days"), moment().subtract(1, "days")],
                    "Last 7 Days": [moment().subtract(6, "days"), moment()],
                    "Last 30 Days": [moment().subtract(29, "days"), moment()],
                    "This Month": [moment().startOf("month"), moment().endOf("month")],
                    "Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")],
                },
            },
            function (start, end, label) {
                filterData(start, end);
            }
        );

        function filterData(start, end) {
            $("#data-list li").each(function () {
                var date = moment($(this).data("date"));
                if (date.isBetween(start, end, "day", "[]")) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }

        // Initial filter to show today's data
        var start = moment().startOf("day");
        var end = moment().endOf("day");
        filterData(start, end);
    });
</script>

@endsection
