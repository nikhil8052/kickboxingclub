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
                    <input type="text" id="date-range-picker" placeholder="MM/DD/YYYY - MM/DD/YYYY" class="form-control" />
                </div>
            </div>
           
            <div class="col-md-3">
                <div class="form-control-wrap">
                    <label class="form-label" for="location_filter">Filter by Location:</label>
                    <select  id="location_filter" class="form-select" name="location_filter">
                        <option value="">All</option>
                        @foreach($locations as $location)
                            <option data-id="{{ $location->id }}" value="{{ $location->name }}">{{ $location->name }}</option>
                        @endforeach
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
                                <span class="amount" id="completed-amount">${{ $masterArray['totalcompletedSale'] ?? 0 }}</span>
                                (<span id="completed-count">{{ $masterArray['completedSaleCount'] ?? 0 }}</span>)
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
                                    <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip" data-bs-placement="left" aria-label="Total Refunds MTD" data-bs-original-title="Total Refunds MTD"></em>
                                </div>
                            </div>
                            <div class="card-amount">
                                <span class="amount" id="total-refunds">${{ $masterArray['totalRefunds'] ?? 0 }}</span>
                                 (<span id="refund-count">{{ $masterArray['RefundsCount'] ?? 0 }}</span>)
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
                                    <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip" data-bs-placement="left" aria-label="Total Cancelled MTD" data-bs-original-title="Total Cancelled MTD"></em>
                                </div>
                            </div>
                            <div class="card-amount">
                                <span class="amount" id="cancelled-amount">${{ $masterArray['totalcancelled'] ?? 0 }}</span>
                                 (<span id="cancelled-count">{{ $masterArray['CancelledCount'] ?? 0 }}</span>)
                            </div>
                        </div>
                    </div>
                </div>
                {{-- <div class="col-md-4">
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
                                <span class="amount" id="paymentFailure-amount">${{ $masterArray['totalPF'] ?? 0 }}</span>
                                 (<span id="paymentFailure-count">{{ $masterArray['paymentFailuerCount'] ?? 0 }}</span>)
                            </div>
                        </div>
                    </div>
                </div> --}}
                {{-- <div class="col-md-4">
                    <div class="card top_box card-bordered card-full">
                        <div class="card-inner">
                            <div class="card-title-group align-start mb-0">
                                <div class="card-title">
                                    <h6 class="subtitle">Pending</h6>
                                </div>
                                <div class="card-tools">
                                    <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip" data-bs-placement="left" aria-label="Total Pending MTD" data-bs-original-title="Total Forcasted Sales"></em>
                                </div>
                            </div>
                            <div class="card-amount">
                                <span class="amount" id="pending-amount">${{ $masterArray['totalPending'] ?? 0 }}</span>
                                 (<span id="pending-count">{{ $masterArray['pendingCount'] ?? 0 }}</span>)
                            </div>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
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
                <tbody id="orders_data">

                </tbody>
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
                },
            },
        );

        // OrderFilter(location=null, start, end);
    });
</script>

<script >
    $(document).ready(function () {

        var location = '';
        var startDate = '';
        var endDate = '';

        OrderFilter('', moment().startOf('month').format('YYYY-MM-DD'), moment().endOf('month').format('YYYY-MM-DD'));


        $("#apply-filters").on('click', function () {
            var dateRange = $('#date-range-picker').val();
            var dates = dateRange.split(" - ");
            startDate = dates[0];
            endDate = dates[1];

            var location = $('#location_filter').val();

            OrderFilter(location, startDate, endDate);
        });

        function OrderFilter(location, startDate, endDate) {
            $('#overlay').show();
            var data = {
                start_date: startDate,
                end_date: endDate,
                location: location,
            };

            $.ajax({
                url: "{{ url('admin-dashboard/get-sales') }}", 
                type: "GET", 
                data: data, 
                success: function(response) {
                    updateRecord(response.masterArray);
                    $('#overlay').hide();
                },
                error: function(xhr, status, error) {
                    console.error("AJAX error:", status, error);
                    $('#overlay').hide();
                }
            });
        }

        function updateRecord(data) {
            $("#completed-amount").text('$' + (data.totalcompletedSale));
            $("#total-refunds").text('$' + (data.totalRefunds));
            $("#pending-amount").text('$' + (data.totalPending));
            $("#paymentFailure-amount").text('$' +(data.totalPF));
            $("#cancelled-amount").text('$' + (data.totalcancelled));

            $("#completed-count").text(data.completedSaleCount);
            $("#refund-count").text(data.RefundsCount);
            $("#pending-count").text(data.pendingCount);
            $("#paymentFailure-count").text(data.paymentFailuerCount);
            $("#cancelled-count").text(data.CancelledCount);

            var table = $('#order_data_table').DataTable();
            table.clear(); 
            var rows = [];

            $.each(data.orders, function(index, item) {
                var number = item.number;
                var location = item.location;
                var currency = item.currency;
                if(item.status == 'Refunded' || item.status == 'Partially Refunded') {
                    var Amount = item.total_amount_refunded;
                    var status = item.status;
                    var orderDate = item.refund_date_created_copy;
                } else {
                    var Amount = item.total;
                    var status = item.status;
                    var orderDate = item.date_created_copy;
                }
                

                rows.push([
                    number,
                    location,
                    currency,
                    Amount,
                    status,
                    orderDate
                ]);
            });

            table.rows.add(rows); 
            table.draw();

        }
    });

</script>

@endsection
