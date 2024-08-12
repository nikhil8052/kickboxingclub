@extends('admin_layout.master')
@section('content')

@section('css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />
@endsection

<div class="nk-content">
    <div class="container-fluid">
        <div class="d-flex paper card-preview card-bordered p-4 mb-3 date-filter">
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
                    <div class="nk-block">
                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                 <!-- <table class="datatable-init nowrap nk-tb-list nk-tb-ulist" data-auto-responsive="false">  -->
                                <table id="instance_data_table" class="nowrap nk-tb-list nk-tb-ulist table table-tranx" data-auto-responsive="false">
                                    <thead>
                                        <tr class="nk-tb-item nk-tb-head">
                                            <!-- <th class="nk-tb-col"><span class="sub-text">Membership Id</span></th> -->
                                            <th class="nk-tb-col"><span class="sub-text">Membership Name</span></th>
                                            <th class="nk-tb-col"><span class="sub-text">Location ID</span></th>
                                            <th class="nk-tb-col"><span class="sub-text">User ID</span></th>
                                            <th class="nk-tb-col"><span class="sub-text">Start date</span></th>
                                             <th class="nk-tb-col"><span class="sub-text">End date</span></th>
                                            <th class="nk-tb-col"><span class="sub-text">Status</span></th>
                                            <th class="nk-tb-col"><span class="sub-text">Purchased Date</span></th>
                                        </tr>
                                    </thead>
                                    <tbody >
                                        
                                    </tbody>
                                </table>
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
        $('#instance_data_table').DataTable({
            processing: true,
            serverSide: false, 
            ajax: {
                url: "{{ url('/admin-dashboard/get-instances') }}", 
                type: 'GET',
                dataSrc: '' 
            },
            columns: [
                // { data: 'membership_id' },
                { data: 'membership_name' },
                { data: 'purchase_location_id' },
                { data: 'user_id' },
                { data: 'start_date' },
                { data: 'end_date' },
                { data: 'status' },
                { data: 'purchase_date' }
                
            ]
        });
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