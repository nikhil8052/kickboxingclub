@extends('admin_layout.master') 
@section('content')
@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />
@endsection
<div class="nk-content ">
 
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm chart-filter-block busns_top">
                    <div class="nk-block d-flex busns_sls">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Business Performance</h3>
                        </div>
                        <form id="filterForm" class="ryt_frm_blk">
                            <div class="nk-block-head-content">
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                                    <div class="toggle-expand-content" data-content="pageMenu">
                                        <ul class="nk-block-tools g-3">
                                            <li class="filter-options"> 
                                                <div class="form-control-wrap">
                                                    <div class="form-group d-flex">
                                                        <label for="date-range-picker">Date</label>
                                                        <input type="text" name="dates" id="date-range-picker" class="form-control" />
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="filter-options">
                                                <div class="d-flex">
                                                    <span><label class="form-label" for="default-06">Location</label></span>
                                                    <div class="form-control-wrap ">
                                                        <select  id="location" class="form-select" data-placeholder="select location"  name="location" >
                                                            <option value="">All</option>
                                                            @foreach($locations as $location)
                                                                <option data-id="{{ $location->name }}" value="{{ $location->location_id }}">{{ $location->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="nk-block top_sec">
                    <div class="row g-gs">
                        <div class="col-md-4 sales-blocks">
                            <div class="card top_box card-bordered card-full card-block">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-0">
                                        <div class="card-title">
                                            <h6 class="subtitle">Total Sales </h6>
                                        </div>
                                        <div class="card-tools">
                                            <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip" data-bs-placement="left" aria-label="Total Sales " data-bs-original-title="Total Sales "></em>
                                        </div>
                                    </div>
                                    <div class="card-amount">
                                        <span class="amount" id="total_all_sales">${{ $totaloverAllsales ?? 0 }}</span>
                                    </div>
                                    <div class="invest-data">
                                        <div class="invest-data-ck"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                                            <canvas class="iv-data-chart chartjs-render-monitor" id="totalDeposit" width="34" height="48" style="display: block; width: 34px; height: 48px;"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card top_box card-bordered card-full">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-0">
                                        <div class="card-title">
                                            <h6 class="subtitle">Forecasted Sale</h6>
                                        </div>
                                        <div class="card-tools">
                                            <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip" data-bs-placement="left" aria-label="Total Credit Sales" data-bs-original-title="Total Credit Sales"></em>
                                        </div>
                                    </div>
                                    <div class="card-amount">
                                        <span class="amount" id="total_credit_sales">${{ $forcastedSales ?? 0 }} 
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card top_box card-bordered card-full">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-0">
                                        <div class="card-title">
                                            <h6 class="subtitle">Classpass Total Sales</h6>
                                        </div>
                                        <div class="card-tools">
                                            <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip" data-bs-placement="left" aria-label="Classpass Total Sales" data-bs-original-title="Classpass Total Sales"></em>
                                        </div>
                                    </div>
                                    <div class="card-amount">
                                        <span class="amount">$0
                                        </span>
                                    </div>
                                    <div class="invest-data">
                                        <div class="invest-data-ck"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                                            <canvas class="iv-data-chart chartjs-render-monitor" id="totalWithdraw" width="34" height="48" style="display: block; width: 34px; height: 48px;"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="col-md-4">
                            <div class="card top_box card-bordered  card-full">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-0">
                                        <div class="card-title">
                                            <h6 class="subtitle">Classpass Forecasted Sales</h6>
                                        </div>
                                        <div class="card-tools">
                                            <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip" data-bs-placement="left" aria-label="Classpass Forecasted Sales" data-bs-original-title="Classpass Forecasted Sales"></em>
                                        </div>
                                    </div>
                                    <div class="card-amount">
                                        <span class="amount" id="total_classpass_forecasted_sales"> $0
                                        </span>
                                    </div>
                                    <div class="invest-data">
                                        <div class="invest-data-ck"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                                            <canvas class="iv-data-chart chartjs-render-monitor" id="totalBalance" width="34" height="48" style="display: block; width: 34px; height: 48px;"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card top_box card-bordered card-full">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-0">
                                        <div class="card-title">
                                            <h6 class="subtitle">Classpass Daily Average</h6>
                                        </div>
                                        <div class="card-tools">
                                            <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip" data-bs-placement="left" aria-label="Classpass Daily Average" data-bs-original-title="Classpass Daily Average"></em>
                                        </div>
                                    </div>
                                    <div class="card-amount">
                                        <span class="amount">$0
                                        </span>
                                    </div>
                                    <div class="invest-data">
                                        <div class="invest-data-ck"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                                            <canvas class="iv-data-chart chartjs-render-monitor" id="totalWithdraw" width="34" height="48" style="display: block; width: 34px; height: 48px;"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="col-md-4">
                            <div class="card top_box card-bordered card-full">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-0">
                                        <div class="card-title">
                                            <h6 class="subtitle">Membership Billing</h6>
                                        </div>
                                        <div class="card-tools">
                                            <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip" data-bs-placement="left" aria-label="Membership Billing Processed By System" data-bs-original-title="Membership Billing Processed By System"></em>
                                        </div>
                                    </div>
                                    <div class="card-amount">
                                        <span class="amount" id="membership_billing">$0
                                        </span>
                                    </div>
                                    <div class="invest-data">
                                        <div class="invest-data-ck"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                                            <canvas class="iv-data-chart chartjs-render-monitor" id="totalWithdraw" width="34" height="48" style="display: block; width: 34px; height: 48px;"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="col-md-4">
                            <div class="card top_box card-bordered card-full">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-0">
                                        <div class="card-title">
                                            <h6 class="subtitle">Over The Counter</h6>
                                        </div>
                                        <div class="card-tools">
                                            <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip" data-bs-placement="left" aria-label="Membership Billing  Processed By System False" data-bs-original-title="Membership Billing  Processed By System False"></em>
                                        </div>
                                    </div>
                                    <div class="card-amount">
                                        <span class="amount" id="over_the_counter">$0
                                        </span>
                                    </div>
                                    <div class="invest-data">
                                        <div class="invest-data-ck"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                                            <canvas class="iv-data-chart chartjs-render-monitor" id="totalWithdraw" width="34" height="48" style="display: block; width: 34px; height: 48px;"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="col-md-4">
                            <div class="card top_box card-bordered card-full">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-0">
                                        <div class="card-title">
                                            <h6 class="subtitle">Active Members</h6>
                                        </div>
                                        <div class="card-tools">
                                            <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip" data-bs-placement="left" aria-label="Active Members" data-bs-original-title="Active Members"></em>
                                        </div>
                                    </div>
                                    <div class="card-amount">
                                        <span class="amount" id="active_members">0
                                        </span>
                                    </div>
                                    <div class="invest-data">
                                        <div class="invest-data-ck"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                                            <canvas class="iv-data-chart chartjs-render-monitor" id="totalWithdraw" width="34" height="48" style="display: block; width: 34px; height: 48px;"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="col-md-4">
                            <div class="card top_box card-bordered  card-full">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-0">
                                        <div class="card-title">
                                            <h6 class="subtitle">Glofox total sales</h6>
                                        </div>
                                        <div class="card-tools">
                                            <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip" data-bs-placement="left" aria-label="Glofix total sales" data-bs-original-title="Glofox total sales"></em>
                                        </div>
                                    </div>
                                    <div class="card-amount">
                                        <span class="amount" id="total_classpass_forecasted_sales"> $0
                                        </span>
                                    </div>
                                    <div class="invest-data">
                                        <div class="invest-data-ck"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                                            <canvas class="iv-data-chart chartjs-render-monitor" id="totalBalance" width="34" height="48" style="display: block; width: 34px; height: 48px;"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="nk-block line-chart-section">
                    <div class="card card-bordered card-preview line-chart-card">
                        <div class="card-inner">
                            <div class="nk-ck"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                                <canvas class="line-cnva" id="salesChart" width="918" height="260" style="display: block; width: 918px; height: 260px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="nk-block-head nk-block-head-sm chart-filter-block busns_top">
                    <div class="nk-block d-flex busns_sls">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Business sales</h3>
                        </div>
                    </div>
                </div>
                <div class="nk-block busines-info-blk pie-chart-section">
                    <div class="row g-gs bsn_rw">
                        <div class="col-xl-4 col-lg-6 col-md-6  mt-2">
                            <div class="card card-bordered card-full main_box-crcl">
                                <div class="card-inner">
                                    <div class="card-head  bsn_hd">
                                        <h6 class="title">Payments</h6> 
                                        <p class="info-icon" ><em class="icon ni ni-info-fill"></em></p>
                                    </div>
                                    <div class="pie_innr">
                                        <div id="payments_labels">
                                           
                                        </div>
                                        <div class="pie_wrapper">
                                            <div class="nk-ck-sm bsn_dv">
                                                <div class="chartjs-size-monitor">
                                                    <div class="chartjs-size-monitor-expand">
                                                        <div class="">
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="chartjs-size-monitor-shrink">
                                                        <div class="">
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                                <canvas class="pie-chart chartjs-render-monitor" width="259" height="180"  id="paymentChart" ></canvas>
                                            </div>
                                       </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-md-6  mt-2">
                            <div class="card card-bordered card-full main_box-crcl">
                                <div class="card-inner">
                                    <div class="card-head  bsn_hd">
                                        <h6 class="title">Memberships</h6>
                                        <p class="info-icon" ><em class="icon ni ni-info-fill"></em></p>
                                    </div>
                                    <div class="pie_innr">
                                        <div id="memberships_labels">
                                            
                                        </div>
                                       <div class="pie_wrapper">
                                            <div class="nk-ck-sm bsn_dv">
                                                <div class="chartjs-size-monitor">
                                                    <div class="chartjs-size-monitor-expand">
                                                        <div class="">
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="chartjs-size-monitor-shrink">
                                                        <div class="">
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                                <canvas class="pie-chart chartjs-render-monitor" width="259" height="180"  id="membershipChart" ></canvas>
                                            </div>
                                       </div>
                                   </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-md-6  mt-2">
                            <div class="card card-bordered card-full main_box-crcl">
                                <div class="card-inner">
                                    <div class="card-head  bsn_hd">
                                        <h6 class="title">Trial Sold</h6>
                                        <p class="info-icon" ><em class="icon ni ni-info-fill"></em></p>
                                    </div>
                                    <div class="pie_innr">
                                        <div id="trials_labels">
                                            
                                        </div>
                                        <div class="pie_wrapper">
                                            <div class="nk-ck-sm bsn_dv">
                                                <div class="chartjs-size-monitor">
                                                    <div class="chartjs-size-monitor-expand">
                                                        <div class="">
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="chartjs-size-monitor-shrink">
                                                        <div class="">
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                                <canvas class="pie-chart chartjs-render-monitor" width="259" height="180"  id="trialsoldChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-md-6  mt-2">
                            <div class="card card-bordered card-full main_box-crcl">
                                <div class="card-inner">
                                    <div class="card-head  bsn_hd">
                                        <h6 class="title">Intro visitors</h6>
                                        <p class="info-icon" ><em class="icon ni ni-info-fill"></em></p>
                                    </div>
                                    <div class="pie_innr">
                                        <div id="visitors_labels">
                                            
                                        </div>
                                        <div class="pie_wrapper">
                                            <div class="nk-ck-sm bsn_dv">
                                                <div class="chartjs-size-monitor">
                                                    <div class="chartjs-size-monitor-expand">
                                                        <div class="">
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="chartjs-size-monitor-shrink">
                                                        <div class="">
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                                <canvas class="pie-chart chartjs-render-monitor" width="259" height="180"  id="visitorsChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
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
    $(document).ready(function () {
        var start = moment().subtract(29, "days");
        var end = moment();
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

        var start = moment().startOf("day");
        var end = moment().endOf("day");
        filterData(start, end);
    });

</script>
<script>
   $(document).ready(function() {
        $.ajax({
            url: "{{url('/sales-data')}}",
            method: 'GET',
            success: function(data) {
                var labels = [];
                var datasets = [];
                var locations = Object.keys(data.sales);

                // Collect all six-month periods
                var allPeriods = new Set();
                locations.forEach(function (location) {
                    Object.keys(data.sales[location]).forEach(function (period) {
                        allPeriods.add(period);
                    });
                });

                labels = Array.from(allPeriods).sort();

                var customColors = [
                    '#B40200', // Red
                    '#33FF57', // Green
                    '#6576FF', // Blue
                  
                ];
                var colorIndex = 0;
                locations.forEach(function (location) {
                    var semiAnnualData = data.sales[location];
                    var salesValues = labels.map(function (period) {
                        return semiAnnualData[period] || 0;
                    });

                    datasets.push({
                        label: location,
                        data: salesValues,
                        borderColor: customColors[colorIndex % customColors.length],
                        backgroundColor: 'rgba(0, 0, 0, 0)',
                        borderWidth: 2,
                        // pointRadius: 0
                    });
                    colorIndex++;
                });

                var ctx = document.getElementById('salesChart').getContext('2d');
                var salesChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: datasets
                    },
                    options: {
                        scales: {
                            x: {
                                grid: {
                                    display: false, // This will hide the x-axis grid lines
                                }
                            },
                            y: {
                                grid: {
                                    display: false, // This will hide the y-axis grid lines
                                }
                            }
                        }
                    }
                });
            }
        });

    });
</script>
<script>
    $(document).ready(function() {
        var paymentsChart = null;
        var membershipsChart = null;
        var TrialSoldChart = null;
        var VisitorsChart = null;

        function loadChartData() {
            $('#overlay').show();

            $.ajax({
                url: '{{ url("/admin-dashboard") }}',
                method: 'GET',
                data: $('#filterForm').serialize(),
                success: function(data) {
                    updateCharts(data);
                    $('#overlay').hide();
                },
                error: function(){
                    $('#overlay').hide();
                }
            });
        }
 
        function updateCharts(data) {
            var paymentsCtx = document.getElementById('paymentChart').getContext('2d');
            var membershipsCtx = document.getElementById('membershipChart').getContext('2d');
            var TrialSoldCtx = document.getElementById('trialsoldChart').getContext('2d');
            var visitorsCtx = document.getElementById('visitorsChart').getContext('2d');

            // $('#total_membership_sales').html("$"+ data.totalMembershipSales);
            $('#total_credit_sales').html("$"+ data.forcastedSales);
            $('#total_all_sales').html("$"+ data.totaloverAllsales);
            $('#over_the_counter').html("$"+ data.overthecounter);
            $('#membership_billing').html("$"+ data.membershipbilling);
            $('#active_members').html( data.activeMembers);

            var paymentslabels = $('#payments_labels'); 
            var membershipslabels = $('#memberships_labels');
            var TrialSoldlabels = $('#trials_labels');
            var visitorslabels = $('#visitors_labels');

            if (paymentsChart) {
                paymentsChart.destroy();
            }
            if (membershipsChart) {
                membershipsChart.destroy();
            }
            if (TrialSoldChart) {
                TrialSoldChart.destroy();
            }
            if (VisitorsChart) {
                VisitorsChart.destroy();
            }

            paymentsChart = new Chart(paymentsCtx, {
                type: 'pie',
                data: {
                    datasets: [{
                        data: [
                            data.failedPayments,
                            data.completedPayments,
                            // data.pendingPayments 
                        ],
                        backgroundColor: ['#6576FF', '#36A2EB']
                    }]
                },
                options: {
                    tooltips: {
                        enabled: false 
                    }
                }
            });

            paymentslabels.html(`<div class="pi_labesl">
                                    <div class="pie_indicator">
                                        <span style="color: #6576FF;"><em class="icon ni ni-circle-fill"></em></span>
                                    </div>
                                    <div class="pie_values">
                                        <span class="label-heading" >Failed</span>
                                        <span class="label-value" >${data.failedPayments}</span>
                                    </div>
                                </div>
                               <div class="pi_labesl">
                                    <div class="pie_indicator">
                                        <span style="color: #36A2EB;"><em class="icon ni ni-circle-fill"></em></span>
                                    </div>
                                    <div class="pie_values">
                                        <span class="label-heading" >Completed</span>
                                        <span class="label-value" >${data.completedPayments}</span>
                                    </div>
                                </div>
                                `);





            membershipsChart = new Chart(membershipsCtx, {
                type: 'pie',
                data: {
                    datasets: [{
                        data: [
                            data.activeMemberships,
                            data.cancelledMemberships
                        ],
                        backgroundColor: ['#B40200', '#FFDEDE']
                    }]
                },
                options: {
                    tooltips: {
                        enabled: false // Disable tooltips on hover
                    }
                }
            });

            membershipslabels.html(`<div class="pi_labesl">
                                    <div class="pie_indicator">
                                        <span style="color: #B40200;"><em class="icon ni ni-circle-fill"></em></span>
                                    </div>
                                    <div class="pie_values">
                                        <span class="label-heading" >Active</span>
                                        <span class="label-value" >${data.activeMemberships}</span>
                                    </div>
                                </div>
                               <div class="pi_labesl">
                                    <div class="pie_indicator">
                                        <span style="color: #FFDEDE;"><em class="icon ni ni-circle-fill"></em></span>
                                    </div>
                                    <div class="pie_values">
                                        <span class="label-heading" >Cancelled</span>
                                        <span class="label-value" >${data.cancelledMemberships}</span>
                                    </div>
                                </div>
                               `);

            TrialSoldChart = new Chart(TrialSoldCtx, {
                type: 'pie',
                data: {
                    datasets: [{
                        data: [
                            data.TrialSoldMembershipsCount,
                        ],
                        backgroundColor: ['#000000']
                    }]
                },
                options: {
                    tooltips: {
                        enabled: false // Disable tooltips on hover
                    }
                }
            });

            TrialSoldlabels.html(`<div class="pi_labesl">
                                    <div class="pie_indicator">
                                        <span style="color: #000000;"><em class="icon ni ni-circle-fill"></em></span>
                                    </div>
                                    <div class="pie_values">
                                        <span class="label-heading" >Trial Sold</span>
                                        <span class="label-value" >${data.TrialSoldMembershipsCount}</span>
                                    </div>
                                </div>
                               `);

            VisitorsChart = new Chart(visitorsCtx, {
                type: 'pie',
                data: {
                    datasets: [{
                        data: [
                            data.allvisitors,
                        ],
                        backgroundColor: ['#B40200']
                    }]
                },
                options: {
                    tooltips: {
                        enabled: false // Disable tooltips on hover
                    }
                }
            });

            visitorslabels.html(`<div class="pi_labesl">
                                    <div class="pie_indicator">
                                        <span style="color: #B40200;"><em class="icon ni ni-circle-fill"></em></span>
                                    </div>
                                    <div class="pie_values">
                                        <span class="label-heading" >Intro Visitors</span>
                                        <span class="label-value" >${data.allvisitors}</span>
                                    </div>
                                </div>
                               `);
        }

        $('#date-range-picker').on('change',function(){
            $('#filterForm').submit();
        });
        $('#location').on('change',function(){
            $('#filterForm').submit();
        });

        loadChartData();

        $('#filterForm').on('submit', function(event) {
            event.preventDefault();
            loadChartData();
        });
    });
</script>
@endsection