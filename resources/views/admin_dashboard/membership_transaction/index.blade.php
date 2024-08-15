@extends('admin_layout/master')
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
                    <button class="btn btn-dark" id="filter">Search</button>
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
                                <table id="membership_transaction_table" class="nowrap nk-tb-list nk-tb-ulist table table-tranx" data-auto-responsive="false">
                                    <thead>
                                        <tr class="nk-tb-item nk-tb-head">
                                            <th class="nk-tb-col"><span class="sub-text">Membership name</span></th>
                                            <!-- <th class="nk-tb-col"><span class="sub-text">Transaction amount</span></th>
                                            <th class="nk-tb-col"><span class="sub-text">Membership instance id</span></th>
                                            <th class="nk-tb-col"><span class="sub-text">Transaction Date</span></th>
                                            <th class="nk-tb-col"><span class="sub-text">User id</span></th> -->
                                            <th class="nk-tb-col"><span class="sub-text">Total</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
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
        $('#membership_transaction_table').DataTable({
            processing: true,
            serverSide: false, 
            ajax: {
                url: "{{ url('/admin-dashboard/get/memberships-transactions') }}", 
                type: 'GET',
                dataSrc: '' 
            },
            columns: [
                { data: 'membership_name' },
                { data: 'total_count' }
            ]
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        // var start = moment().startOf("month");
        // var end = moment().endOf("month");
        $("#date-range-picker").daterangepicker(
            {
                opens: "left",
                // startDate: start,
                // endDate: end,
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

<script>
    // function filterByDate(){
    //     var date = $('#date-range-picker').val();
    //     var dates = date.split(" - ");
    //     startDate = dates[0];
    //     endDate = dates[1]; 

    //     var data = {
    //         start: startDate,
    //         end: endDate,
    //         _token: "{{ csrf_token() }}"
    //     }

    //     $.ajax({
    //         url: "{{ url('/admin-dashboard/get/memberships-transactions') }}",
    //         type: "get",
    //         data: data,
    //         success: function(response){
    //             console.log(response);
    //         }
    //     })
    // }

</script>

@endsection