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
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block d-flex ">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Business Performance</h3>
                            <!-- <div class="nk-block-des text-soft">
                                <p>Welcome to Crypto Invest Dashboard</p>
                            </div> -->
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
                                            <h6 class="subtitle">Total Sales MTD</h6>
                                        </div>
                                        <div class="card-tools">
                                            <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip" data-bs-placement="left" aria-label="Total Sales MTD" data-bs-original-title="Total Sales MTD"></em>
                                        </div>
                                    </div>
                                    <div class="card-amount">
                                        <span class="amount">${{ $totalsales ?? 0 }}
                                             <!-- <span class="currency currency-usd">USD</span> -->
                                            </span>
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
                                            <h6 class="subtitle">Total Forecasted Sales</h6>
                                        </div>
                                        <div class="card-tools">
                                            <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip" data-bs-placement="left" aria-label="Total Forcasted Sales" data-bs-original-title="Total Forcasted Sales"></em>
                                        </div>
                                    </div>
                                    <div class="card-amount">
                                        <span class="amount">${{ $forecastsales ?? 0 }} 
                                            <!-- <span class="currency currency-usd">USD</span> -->
                                        </span>
                                        <!-- <span class="change down text-danger"><em class="icon ni ni-arrow-long-down"></em>1.93%</span> -->
                                    </div>
                                    <div class="invest-data">
                                        <!-- <div class="invest-data-amount g-2">
                                            <div class="invest-data-history">
                                                <div class="title">This Month</div>
                                                <div class="amount">2,940.59 <span class="currency currency-usd">USD</span></div>
                                            </div>
                                            <div class="invest-data-history">
                                                <div class="title">This Week</div>
                                                <div class="amount">1,259.28 <span class="currency currency-usd">USD</span></div>
                                            </div>
                                        </div> -->
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
                                            <h6 class="subtitle">Memberships Sales</h6>
                                        </div>
                                        <div class="card-tools">
                                            <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip" data-bs-placement="left" aria-label="Total Balance in Account" data-bs-original-title="Total Balance in Account"></em>
                                        </div>
                                    </div>
                                    <div class="card-amount">
                                        <span class="amount"> $79,358.50 
                                            <!-- <span class="currency currency-usd">USD</span> -->
                                        </span>
                                    </div>
                                    <div class="invest-data">
                                        <!-- <div class="invest-data-amount g-2">
                                            <div class="invest-data-history">
                                                <div class="title">This Month</div>
                                                <div class="amount">2,940.59 <span class="currency currency-usd">USD</span></div>
                                            </div>
                                            <div class="invest-data-history">
                                                <div class="title">This Week</div>
                                                <div class="amount">1,259.28 <span class="currency currency-usd">USD</span></div>
                                            </div>
                                        </div> -->
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
                            <!-- <div class="nk-block-des text-soft">
                                <p>Welcome to Crypto Invest Dashboard</p>
                            </div> -->
                        </div>
                        <form id="filterForm" class="ryt_frm_blk">
                            <div class="nk-block-head-content">
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                                    <div class="toggle-expand-content" data-content="pageMenu">
                                        <ul class="nk-block-tools g-3">
                                            <!-- <li class="filter-options">
                                                <div class="d-flex">
                                                    <span><em class="icon ni ni-filter-alt"></em></span>
                                                    <div class="form-control-wrap ">
                                                        <div class="form-control-select">
                                                            <select class="form-control" name="filter" id="filter">
                                                                <option value="all">All</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li> -->
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
                                                        <div class="form-control-select">
                                                            <select class="form-control" name="location" id="location">
                                                                @foreach($locations as $location)
                                                                    <option data-id="{{ $location->name }}" value="{{ $location->location_id }}">{{ $location->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
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
                <div class="nk-block busines-info-blk">
                    <div class="row g-gs bsn_rw">
                        <div class="col-lg-4 col-md-6  mt-2">
                            <div class="card card-bordered card-full main_box-crcl">
                                <div class="card-inner">
                                    <div class="card-head text-center bsn_hd">
                                        <h6 class="title">Payments</h6>
                                    </div>
                                    <div class="nk-ck-sm bsn_dv"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                                        <canvas class="pie-chart chartjs-render-monitor" width="259" height="180"  id="paymentChart" ></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6  mt-2">
                            <div class="card card-bordered card-full main_box-crcl">
                                <div class="card-inner">
                                    <div class="card-head text-center bsn_hd">
                                        <h6 class="title">Memberships</h6>
                                    </div>
                                    <div class="nk-ck-sm bsn_dv"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                                        <canvas class="pie-chart chartjs-render-monitor" width="259" height="180"  id="membershipChart" ></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6  mt-2">
                            <div class="card card-bordered card-full main_box-crcl">
                                <div class="card-inner">
                                    <div class="card-head text-center bsn_hd">
                                        <h6 class="title">Trial Sold</h6>
                                    </div>
                                    <div class="nk-ck-sm bsn_dv"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                                        <canvas class="pie-chart chartjs-render-monitor" width="259" height="180"  id="trialsoldChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6  mt-2">
                            <div class="card card-bordered card-full main_box-crcl">
                                <div class="card-inner">
                                    <div class="card-head text-center bsn_hd">
                                        <h6 class="title">Intro visitors</h6>
                                    </div>
                                    <div class="nk-ck-sm bsn_dv">
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

        // Initial filter to show today's data
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
                    labels: [
                        `Failed Payments: ${data.failedPayments}`,
                        `Completed Payments: ${data.completedPayments}`,
                        `Pending Payments: ${data.pendingPayments}`
                    ],
                    datasets: [{
                        data: [
                            data.failedPayments,
                            data.completedPayments,
                            data.pendingPayments
                        ],
                        backgroundColor: ['#6576FF', '#36A2EB', '#FFCE56']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'left'
                        },
                        datalabels: {
                            color: '#fff', 
                            anchor: 'end',
                            align: 'left', 
                            offset: 10, 
                            font: {
                                weight: 'bold',
                                size: 14
                            },
                            formatter: (value, context) => {
                                const label = context.chart.data.labels[context.dataIndex];
                                return `${label}`;
                            }
                        }
                    }
                },
                // plugins: [ChartDataLabels]
            });

            membershipsChart = new Chart(membershipsCtx, {
                type: 'pie',
                data: {
                    labels: [`Active Memberships: ${data.activeMemberships}`, `Cancelled Memberships: ${data.cancelledMemberships}`],
                    datasets: [{
                        data: [
                            // data.totalMemberships,
                            data.activeMemberships,
                            data.cancelledMemberships
                        ],
                        backgroundColor: ['#B40200', '#FFDEDE']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        // datalabels: {
                        //     color: '#fff',
                        //     display: true,
                        // }
                    }
                }
            });

            TrialSoldChart = new Chart(TrialSoldCtx, {
                type: 'pie',
                data: {
                    labels: [`Trial Sold: ${data.TrialSoldMembershipsCount}`],
                    datasets: [{
                        data: [
                            data.TrialSoldMembershipsCount,
                        ],
                        backgroundColor: ['#000000','#E7E7E7']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        // datalabels: {
                        //     color: '#fff',
                        //     display: true,
                        // }
                    }
                }
            });

            VisitorsChart = new Chart(visitorsCtx, {
                type: 'pie',
                data: {
                    labels: [`intro visitors: ${data.allvisitors}`],
                    datasets: [{
                        data: [
                            data.allvisitors,
                        ],
                        backgroundColor: ['#B40200', '#36A2EB', '#FFCE56']
                    }]
                },
                options: {
                    responsive: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        // datalabels: {
                        //     color: '#fff',
                        //     display: true,
                        //     // formatter: function(value) {
                        //     //     return value;
                        //     // }
                        // }
                    }
                }
            });
        }

        $('#date-range-picker,#location').on('change',function(){
            $('#filterForm').submit();
        });

        // Initial load
        loadChartData();

        // Handle form submission
        $('#filterForm').on('submit', function(event) {
            event.preventDefault();
            loadChartData();
        });
    });
</script>
@endsection