@extends('admin_layout.master') @section('content') @section('css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />
@endsection

<div class="nk-content">
    <div class="container-fluid">
        <!-- <div class="d-flex paper card-preview card-bordered p-4 mb-3">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="date-range-picker">Date Filter</label>
                    <input type="text" id="date-range-picker" class="form-control" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <button class="btn btn-dark" onclick="dateFilter()">Search</button>
                </div>
            </div>
        </div> -->

        <div>
            <label for="status_filter">Filter by Status:</label>
            <select id="status_filter" name="status_filter">
                <option value="">All</option>
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
                <option value="Refunded">Refunded</option>
                <option value="Deferred">Deferred</option>
                <option value="Payment Failure">Payment Failure</option>
                <!-- Add more options as needed -->
            </select>
        </div>

        <div class="row card">
            <table id="order_data_table" class="nowrap nk-tb-list nk-tb-ulist table table-tranx" data-auto-responsive="true" bordered>
                <thead>
                    <tr class="nk-tb-item nk-tb-head">
                        <th class="nk-tb-col"><span class="sub-text">Order Number</span></th>
                        <th class="nk-tb-col"><span class="sub-text">Location</span></th>
                        <th class="nk-tb-col"><span class="sub-text">Currency</span></th>
                        <th class="nk-tb-col"><span class="sub-text">Amount</span></th>
                        <th class="nk-tb-col"><span class="sub-text">Status</span></th>
                        <th class="nk-tb-col"><span class="sub-text">Order Date</span></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
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
    $(document).ready(function () {
        var table = $("#order_data_table").DataTable({
            processing: true,
            serverSide: false,
            pageLength: 50, 
            lengthMenu: [50, 100, 150], 
            dom: "Bfrtip", 
            buttons: ["copy", "excel", "pdf", "print"],
            ajax: {
                url: "{{ url('/admin-dashboard/get-orders') }}",
                type: "GET",
                dataSrc: "",
            },
            columns: [{ data: "order_id" }, { data: "location" }, { data: "currency" }, { data: "total" }, { data: "status" }, { data: "date_placed" }],
        });

        $('#status_filter').on('change', function() {
            var selectedStatus = $(this).val();
            table.column(4).search(selectedStatus).draw(); 
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

<script>
    function dateFilter(){
        console.log($('#date-range-picker').val());
    }
</script>

@endsection
