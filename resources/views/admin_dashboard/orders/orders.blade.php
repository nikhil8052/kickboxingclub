@extends('admin_layout.master') @section('content') @section('css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />
@endsection

<div class="nk-content">
    <div class="container-fluid">
        <div class="d-flex card-preview card-bordered p-4 mb-3">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="date-range-picker">Date Filter</label>
                    <input type="text" id="date-range-picker" class="form-control" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <button id="search-btn" class="btn btn-dark">Search</button>
                </div>
            </div>
        </div>
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block">
                    <div class="card card-bordered card-preview">
                        <div class="card-inner">
                            <div class="card card-bordered card-preview">
                                <table id="salesTable" class="nowrap nk-tb-list nk-tb-ulist table table-bordered" data-auto-responsive="false">
                                    <thead>
                                        <tr class="nk-tb-item nk-tb-head">
                                            <!-- <th class="nk-tb-col heading_td"><span class="sub-text">Location</span></th> -->
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <br />
                            <hr />
                            <div class="card card-bordered card-preview">
                                <table id="paymentsTable" class="nowrap nk-tb-list nk-tb-ulist table table-bordered" data-auto-responsive="false">
                                    <thead>
                                        <tr class="nk-tb-item nk-tb-head">
                                            <!-- <th class="nk-tb-col heading_td"><span class="sub-text">Location</span></th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be dynamically inserted here -->
                                    </tbody>
                                </table>
                            </div>
                            <br />
                            <hr />
                            <div class="card card-bordered card-preview">
                                <table id="membersTable" class="nowrap nk-tb-list nk-tb-ulist table table-bordered" data-auto-responsive="false">
                                    <thead>
                                        <tr class="nk-tb-item nk-tb-head"></tr>
                                    </thead>
                                    <tbody></tbody>
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

<script>
    $(document).ready(function () {

        loadData(startDate = null,endDate = null);

        function loadData(start_date, end_date) {
            $('#loader').show();
            $.ajax({
                url: "{{ url('/admin-dashboard/get-sales') }}",
                method: "GET",
                data: {
                    startDate: start_date,
                    endDate: end_date,
                },
                success: function (response) {
                    // console.log(response);
                    var masterArray = response.masterArray;
                    populateTables(masterArray);
                    // $('#loader').hide();
                },
                error: function (xhr, status, error) {
                    $('#loader').hide();
                    console.error("AJAX request failed");
                    console.error(xhr, status, error);
                },
            });
        }

        function populateTables(masterArray) {
            var salesTable = $("#salesTable tbody");
            var paymentsTable = $("#paymentsTable tbody");
            var membersTable = $("#membersTable tbody");

            salesTable.empty();
            paymentsTable.empty();
            membersTable.empty();

            // Clear existing headers
            $("#salesTable thead tr").empty();
            $("#paymentsTable thead tr").empty();
            $("#membersTable thead tr").empty();

            var locations = Object.keys(masterArray);
            var headerRow = '<th class="nk-tb-col heading_td"><span class="sub-text">Location</span></th>';
            locations.forEach(function (location) {
                headerRow += '<th class="nk-tb-col"><span class="sub-text">' + location + "</span></th>";
            });
            $("#salesTable thead tr").append(headerRow);
            $("#paymentsTable thead tr").append(headerRow);
            $("#membersTable thead tr").append(headerRow);

            var salesData = [
                { metric: "Mariana Tek Sales to Date", key: "alldataSum" },
                { metric: "Mariana Tek Forecasted Sales for Month", key: "forecast" },
                { metric: "Totals", key: "total" },
            ];
            salesData.forEach(function (data) {
                var row = '<tr class="nk-tb-item"><td class="nk-tb-col heading_td"><span class="tb-lead">' + data.metric + "</span></td>";
                locations.forEach(function (location) {
                    var value;

                    if (data.key === "total") {
                        var alldataSum = masterArray[location]["alldataSum"];
                        var forecast = masterArray[location]["forecast"];
                        
                        // Ensure alldataSum and forecast are strings, otherwise convert them
                        var cleanedNumber1 = (typeof alldataSum === 'string' ? alldataSum : alldataSum.toString()).replace(/[$,]/g, '');
                        var cleanedNumber2 = (typeof forecast === 'string' ? forecast : forecast.toString()).replace(/[$,]/g, '');
                        
                        var number1 = parseFloat(cleanedNumber1) || 0;
                        var number2 = parseFloat(cleanedNumber2) || 0;
                        value = number1 + number2;
                    } else {
                        value = masterArray[location][data.key] ?? 0;
                    }

                    var formattedValue = value.toLocaleString('en-IN');
                    row += '<td class="nk-tb-col"><span class="tb-lead">' + formattedValue + "</span></td>";
                });
                row += "</tr>";
                salesTable.append(row);
            });

            var paymentData = [
                { metric: "Completed Payments", key: "completePayments" },
                { metric: "Failed Payments", key: "failedPayments" },
                { metric: "Pending Payments", key: "pendingPayments" },
            ];
            paymentData.forEach(function (data) {
                var row = '<tr class="nk-tb-item"><td class="nk-tb-col heading_td"><span class="tb-lead">' + data.metric + "</span></td>";
                locations.forEach(function (location) {
                    var value = masterArray[location][data.key] ?? 0;
                    row += '<td class="nk-tb-col"><span class="tb-lead">' + value + "</span></td>";
                });
                row += "</tr>";
                paymentsTable.append(row);
            });

            var memberData = [
                { metric: "Active Members", key: "activeMembership" },
                { metric: "Cancelled MTD", key: "cancelledMembership" },
            ];
            memberData.forEach(function (data) {
                var row = '<tr class="nk-tb-item"><td class="nk-tb-col heading_td"><span class="tb-lead">' + data.metric + "</span></td>";
                locations.forEach(function (location) {
                    var value = masterArray[location][data.key] ?? 0;
                    row += '<td class="nk-tb-col"><span class="tb-lead">' + value + "</span></td>";
                });
                row += "</tr>";
                membersTable.append(row);
            });
            $('#loader').hide();
        }
       

        $("#search-btn").on("click", functitd class="nk-tb-item nk-tb-head">Employee ID</td>
                                                <td class="nk-tb-item nk-tb-head">Employee Name</td>
                                                <td class="nk-tb-item nk-tb-head">Location</td>
                                                <td class="nk-tb-item nk-tb-head">Shift Date</td>
                                                <td class="nk-tb-item nk-tb-head">Duration</td>
                                                <td class="nk-tb-item nk-tb-head">Start time</td>
                                                <td class="nk-tb-item nk-tb-head">End time</td>on () {
            var filter = $('#date-range-picker').val();
            // console.log(filter);
            var startDate, endDate;

            var dates = filter.split(' - '); 

            var startDate = formatDate(new Date(dates[0])); 
            var endDate = formatDate(new Date(dates[1])); 

            loadData(startDate, endDate);

        });

        function formatDate(date) {
            var d = new Date(date);
            var day = ("0" + d.getDate()).slice(-2);
            var month = ("0" + (d.getMonth() + 1)).slice(-2);
            var year = d.getFullYear();
            return `${year}-${month}-${day}`;
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
