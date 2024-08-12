@extends('admin_layout.master') @section('content') @section('css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />
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
                <div class="form-control-wrap">
                    <label class="form-label" for="location_filter">Filter by Location:</label>
                    <select id="location_filter" class="form-select" name="location_filter">
                        <option value="">All</option>
                        <option value="Torrance">Torrance</option>
                        <option value="Lakewood">Lakewood</option>
                        <option value="Orange">Orange</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <button class="btn btn-dark" id="apply-filters">Apply Filter</button>
                </div>
            </div>
        </div> 
        <div class="nk-block top_sec">
            <div class="row g-gs">
                <div class="col-md-4 sales-blocks">
                    <div class="card top_box card-bordered card-full card-block">
                        <div class="card-inner">
                            <div class="card-title-group align-start mb-0">
                                <div class="card-title">
                                    <h6 class="subtitle">Completed</h6>
                                </div>
                                <div class="card-tools">
                                    <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip" data-bs-placement="left" aria-label="Total Sales MTD" data-bs-original-title="Total Sales MTD"></em>
                                </div>
                            </div>
                            <div class="card-amount">
                                <span class="amount" id="completed-amount">$00</span>
                                (<span id="completed-count"></span>)
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card top_box card-bordered card-full">
                        <div class="card-inner">
                            <div class="card-title-group align-start mb-0">
                                <div class="card-title">
                                    <h6 class="subtitle">Refunds</h6>
                                </div>
                                <div class="card-tools">
                                    <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip" data-bs-placement="left" aria-label="Total Forcasted Sales" data-bs-original-title="Total Forcasted Sales"></em>
                                </div>
                            </div>
                            <div class="card-amount">
                                <span class="amount" id="total-refunds">$00 </span>
                                 (<span id="refund-count"></span>)
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card top_box card-bordered card-full">
                        <div class="card-inner">
                            <div class="card-title-group align-start mb-0">
                                <div class="card-title">
                                    <h6 class="subtitle">Cancelled</h6>
                                </div>
                                <div class="card-tools">
                                    <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip" data-bs-placement="left" aria-label="Total Forcasted Sales" data-bs-original-title="Total Forcasted Sales"></em>
                                </div>
                            </div>
                            <div class="card-amount">
                                <span class="amount" id="cancelled-amount">$00 </span>
                                 (<span id="cancelled-count"></span>)
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card top_box card-bordered card-full">
                        <div class="card-inner">
                            <div class="card-title-group align-start mb-0">
                                <div class="card-title">
                                    <h6 class="subtitle">Payment Failure</h6>
                                </div>
                                <div class="card-tools">
                                    <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip" data-bs-placement="left" aria-label="Total Forcasted Sales" data-bs-original-title="Total Forcasted Sales"></em>
                                </div>
                            </div>
                            <div class="card-amount">
                                <span class="amount" id="paymentFailure-amount">$00 </span>
                                 (<span id="paymentFailure-count"></span>)
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card top_box card-bordered card-full">
                        <div class="card-inner">
                            <div class="card-title-group align-start mb-0">
                                <div class="card-title">
                                    <h6 class="subtitle">Pending</h6>
                                </div>
                                <div class="card-tools">
                                    <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip" data-bs-placement="left" aria-label="Total Forcasted Sales" data-bs-original-title="Total Forcasted Sales"></em>
                                </div>
                            </div>
                            <div class="card-amount">
                                <span class="amount" id="pending-amount">$00 </span>
                                 (<span id="pending-count"></span>)
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="nk-block top_sec">
            <div class="row g-gs">
                <div>
                    <table class="nowrap nk-tb-list nk-tb-ulist table table-tranx"  bordered>
                        <thead>
                          
                            <tr class="nk-tb-item nk-tb-head">
                                <th class="nk-tb-col"><span class="sub-text">Refunded</span></th>
                                <td id="refund-count"></td>
                                <td id="total-refunds"></td>
                            </tr>
                            <tr class="nk-tb-item nk-tb-head">
                                <th class="nk-tb-col"><span class="sub-text">Payment Failure</span></th>
                                <td id="paymentFailure-count"></td>
                                <td id="paymentFailure-amount"></td>
                            </tr>
                            <tr class="nk-tb-item nk-tb-head">
                                <th class="nk-tb-col"><span class="sub-text">Cancelled</span></th>
                                <td id="cancelled-count"></td>
                                <td id="cancelled-amount"></td>
                            </tr>
                            <tr class="nk-tb-item nk-tb-head">
                                <th class="nk-tb-col"><span class="sub-text">Pending & Deferred</span></th>
                                <td id="pending-count"></td>
                                <td id="pending-amount"></td>
                            </tr> 
                        </thead>
                    </table>
                </div>
            </div>
        </div> --}}
        <br />
        <div class="card">
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
        ajax: {
            url: "{{ url('/admin-dashboard/get-orders') }}",
            type: "GET",
            dataSrc: "",
            data: function(d) {
                var dateRangePicker = $('#date-range-picker').data('daterangepicker');
                if (dateRangePicker) {
                    var start = dateRangePicker.startDate ? dateRangePicker.startDate.format('YYYY-MM-DD') : '';
                    var end = dateRangePicker.endDate ? dateRangePicker.endDate.format('YYYY-MM-DD') : '';
                    var selectedStatus = $('#status_filter').val();
                    d.startDate = start;
                    d.endDate = end;
                    d.status = selectedStatus;
                }
            }
        },
        columns: [
            { data: "order_id" },
            { data: "location" },
            { data: "currency" },
            { data: "total" },
            { data: "status" },
            { data: "date_placed" }
        ],
        initComplete: function () {
            $.fn.dataTable.ext.search.push(
                function (settings, data, dataIndex) {
                    var dateRangePicker = $('#date-range-picker').data('daterangepicker');
                    if (dateRangePicker) {
                        var min = moment(dateRangePicker.startDate).startOf('day');
                        var max = moment(dateRangePicker.endDate).endOf('day');
                        var datePlaced = moment(data[5], 'YYYY-MM-DD HH:mm:ss'); // Ensure format matches

                        if (
                            (isNaN(min) && isNaN(max)) ||
                            (isNaN(min) && datePlaced <= max) ||
                            (min <= datePlaced && isNaN(max)) ||
                            (min <= datePlaced && datePlaced <= max)
                        ) {
                            // Status filtering
                            var selectedLocation = $('#location_filter').val();
                            if (selectedLocation === "" || data[1] === selectedLocation) {
                                return true;
                            }
                        }
                    }
                    return false;
                }
            );
        }
    });

    var start = moment().startOf("month");
    var end = moment().endOf("month");

    $("#date-range-picker").daterangepicker(
        {
            opens: "left",
            startDate: start,
            endDate: end,
            ranges: {
                Today: [moment(), moment()],
                Yesterday: [moment().subtract(1, "days"), moment().subtract(1, "days")],
                "Last 7 Days": [moment().subtract(6, "days"), moment()],
                "Last 30 Days": [moment().subtract(29, "days"), moment()],
                "This Month": [moment().startOf("month"), moment().endOf("month")],
                "Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")],
            }
        }
    );

    // Ensure that daterangepicker is initialized
    $("#date-range-picker").data('daterangepicker').setStartDate(start);
    $("#date-range-picker").data('daterangepicker').setEndDate(end);

    $("#apply-filters").on('click', function () {
        table.draw(); 
        updateTotals();
    });

    function updateTotals() {
        // Retrieve filtered data
        var data = table.rows({ search: 'applied' }).data().toArray();
        var totalSales = 0; 
        var totalRefunds = 0; 
        var completedSales = 0; 
        var totalPending = 0; 
        var totalcancelled = 0; 
        var totalpaymentFailed = 0; 

        var totalCounts = 0;
        var countCompleted = 0;
        var countRefunded = 0;
        var countPending = 0;
        var countCancelled = 0;
        var countPaymentFailed = 0;


        data.forEach(function (item) {
            var totalAmount = parseFloat(item.total) || 0; 

            totalSales += totalAmount;
            totalCounts++;

            switch (item.status) {
                case "Completed":
                    completedSales += totalAmount;
                    countCompleted++;
                    break;
                case "Refunded":
                    //totalRefunds += parseFloat(item.refund_total) || 0;
                    totalRefunds += totalAmount;
                    countRefunded++;
                    break;
                case "Deferred":
                    totalPending += totalAmount;
                    countPending++;
                    break;
                case "Pending":
                    totalPending += totalAmount;
                    countPending++;
                    break;
                case "Cancelled":
                    totalcancelled += totalAmount;
                    countCancelled++;
                    break;
                case "Payment Failure":
                    totalpaymentFailed += totalAmount;
                    countPaymentFailed++;
                    break;
            }
        });

        console.log('pf'+countPaymentFailed);
        console.log('cf'+countCancelled);
        console.log( 'cp' +countPending);

        $("#total-sales").text('$' + totalSales.toFixed(2));
        $("#total").text(totalCounts);
        $("#completed-amount").text('$' + completedSales.toFixed(2));
        $("#completed-count").text(countCompleted);
        $("#total-refunds").text('$' + totalRefunds.toFixed(2));
        $("#refund-count").text(countRefunded);
        $("#pending-amount").text('$' + totalPending.toFixed(2));
        $("#pending-count").text(countPending);
        $("#paymentFailure-amount").text('$' + totalpaymentFailed.toFixed(2));
        $("#paymentFailure-count").text(countPaymentFailed);
        $("#cancelled-amount").text('$' + totalcancelled.toFixed(2));
        $("#cancelled-count").text(countCancelled);
    }

    table.on('draw', function () {
        updateTotals();
    });
});

</script>

@endsection
