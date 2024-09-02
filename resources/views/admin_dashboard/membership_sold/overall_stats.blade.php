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
                        <select  id="location_filter" class="form-select js-select2 select2-hidden-accessible" data-placeholder="select location" multiple="" name="location_filter[]" data-select2-id="9" tabindex="-1" aria-hidden="true">
                            @foreach($locations as $location)
                                <option data-id="{{ $location->location_id }}" value="{{ $location->location_id }}">{{ $location->name }}</option>
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
            <br />
            <div class="card">
                <table id="sales-table" class="nowrap nk-tb-list nk-tb-ulist table table-bordered">
                    <thead>
                        <tr class="nk-tb-item nk-tb-head">
                            <td class="nk-tb-col heading_td" >Membership</td>
                            <td class="nk-tb-col heading_td" >Quantity</td>
                            <td class="nk-tb-col heading_td" >Billing</td>
                        </tr>
                    </thead>
                    <tbody id="Sales_stats_table" >
                    
                    </tbody>
                </table>
            </div>
            <br />
            <div class="card">
                <table id="employee-sales-table" class="nowrap nk-tb-list nk-tb-ulist table table-bordered">
                    <thead>
                        <tr class="nk-tb-item nk-tb-head">
                            <td class="nk-tb-col heading_td" >Employee</td>
                            <td class="nk-tb-col heading_td" >Total</td>
                            <td class="nk-tb-col heading_td" >Starter</td>
                            <td class="nk-tb-col heading_td" >Club</td>
                            <td class="nk-tb-col heading_td" >Vip</td>
                            <td class="nk-tb-col heading_td" >Gold</td>
                            <td class="nk-tb-col heading_td" >Commission Earned</td>
                        </tr>
                    </thead>
                    <tbody id="employee_stats_table" >
                    
                    </tbody>
                </table>
            </div>
            <br />
            <div class="card">
                <div class="p-2" >
                    <div class="col-md-12 d-flex justify-content-between">
                        <div class="form-group">
                        </div>
                        <div class="form-group">
                            <button class="btn btn-light" id="export-button"><i class="fa fa-download"></i> Export</button>
                        </div>
                    </div>
                </div>
                <div class="p-2">
                    <table id="sold_membership_table" class="nowrap nk-tb-list nk-tb-ulist table table-tranx" data-auto-responsive="true" bordered>
                        <thead>
                            <tr class="nk-tb-item nk-tb-head">
                                <th class="nk-tb-col">Name</th>
                                <th class="nk-tb-col">Type</th>
                                <th class="nk-tb-col">Weekly billing</th>
                                <th class="nk-tb-col">Monthly billing</th>
                                <th class="nk-tb-col">Location</th>
                                <th class="nk-tb-col">Sold By</th>
                                <th class="nk-tb-col">Date</th>
                            </tr>
                        </thead>
                        <tbody id="membership_sold_data">

                        </tbody>
                    </table>
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
        });
    </script>

    <script >
        $(document).ready(function () {

            var location = '';
            var startDate = '';
            var endDate = '';
            var csvContent = ''; 

            OrderFilter('', moment().startOf('month').format('YYYY-MM-DD'), moment().endOf('month').format('YYYY-MM-DD'));


            $("#apply-filters").on('click', function () {
                var dateRange = $('#date-range-picker').val();
                var dates = dateRange.split(" - ");
                startDate = dates[0];
                endDate = dates[1];

                var location = $('#location_filter').val();

                OrderFilter(location, startDate, endDate);
            });

            $('#export-button').on('click', function () {
                if (csvContent === '') {
                    return;
                }

                var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });

                var link = document.createElement('a');
                if (link.download !== undefined) { 
                    var url = URL.createObjectURL(blob);
                    link.setAttribute('href', url);
                    link.setAttribute('download', 'membership_Sold.csv');
                    link.style.visibility = 'hidden';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            });

            function OrderFilter(location, startDate, endDate) {
                $('#overlay').show();
                var data = {
                    start_date: startDate,
                    end_date: endDate,
                    location: location,
                };

                $.ajax({
                    url: "{{ url('admin-dashboard/get-overall-sold-stats') }}", 
                    type: "GET", 
                    data: data, 
                    success: function(response) {
                        updateRecord(response.masterArray);
                        generateCSVContent(response.masterArray);
                        $('#overlay').hide();
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX error:", status, error);
                        $('#overlay').hide();
                    }
                });
            }


            function updateRecord(data) {

                var sales = $('#Sales_stats_table');

                sale_html = `<tr>
                        <td class="nk-tb-col heading_td" >Starter</td>
                        <td class="nk-tb-col"><span class="tb-lead">${data.Mstarter?.count || 0}</span></td>
                        <td class="nk-tb-col"><span class="tb-lead">$${data.Mstarter?.billing || 0}</span></td>
                    </tr>
                    <tr>
                        <td class="nk-tb-col heading_td" >Club</td>
                        <td class="nk-tb-col"><span class="tb-lead">${data.Mclub?.count || 0}</span></td>
                        <td class="nk-tb-col"><span class="tb-lead">$${data.Mclub?.billing || 0}</span></td>
                    </tr>
                    <tr>
                        <td class="nk-tb-col heading_td" >Vip</td>
                        <td class="nk-tb-col"><span class="tb-lead">${data.Mvip?.count || 0}</span></td>
                        <td class="nk-tb-col"><span class="tb-lead">$${data.Mvip?.billing || 0}</span></td>
                    </tr>
                    <tr >
                        <td class="nk-tb-col heading_td" >Gold</td>
                        <td class="nk-tb-col"><span class="tb-lead">${data.Mgold?.count || 0}</span></td>
                        <td class="nk-tb-col"><span class="tb-lead">$${data.Mgold?.billing || 0}</span></td>
                    </tr>
                    <hr>
                    <tr>
                        <td class="nk-tb-col heading_td" >total</td>
                        <td class="nk-tb-col"><span class="tb-lead">${data.Mtotal?.count || 0}</span></td>
                        <td class="nk-tb-col"><span class="tb-lead">$${data.Mtotal?.billing || 0}</span></td>
                    </tr>`;

                sales.empty();
                sales.html(sale_html);


                var Esales = $('#employee_stats_table');

                var Esale_html; 

                $.each(data.employeeStats, function(key, d) { 
                    Esale_html += `<tr>
                        <td class="nk-tb-col heading_td" >${d.employee_name}</td>
                        <td class="nk-tb-col"><span class="tb-lead">${d.total}</span></td>
                        <td class="nk-tb-col"><span class="tb-lead">${d.starter}</span></td>
                        <td class="nk-tb-col"><span class="tb-lead">${d.club}</span></td>
                        <td class="nk-tb-col"><span class="tb-lead">${d.vip}</span></td>
                        <td class="nk-tb-col"><span class="tb-lead">${d.gold}</span></td>
                        <td class="nk-tb-col"><span class="tb-lead">$${d.commision}</span></td>
                    </tr>`;
                });
                
                Esales.empty();
                Esales.html(Esale_html);

                var table = $('#sold_membership_table').DataTable();
                table.clear(); 
                var rows = [];

                $.each(data.records, function(index, item) {
                    var name = item.name;
                    var location =item.location ? item.location.name : 'unknown'; 
                    var type = item.membershiptype ? item.membershiptype.type : 'unknown'; 
                    var soldBy = item.user ? item.user.name : 'unknown'; 
                    var weeklyB = '$' + item.weekly_billing;
                    var monthlyB = '$' + item.monthly_billing;
                    var DateSold = item.sold_date;
                    
                    rows.push([
                        name,
                        type,
                        weeklyB,
                        monthlyB,
                        location,
                        soldBy,
                        DateSold,
                    ]);
                });

                table.rows.add(rows); 
                table.draw();
            }

            function generateCSVContent(data) {
                csvContent = ''; 

                var headers = ["Name", "Type", "Weekly billing", "Monthly Billing", "Location", "sold By","Date"];
                csvContent += headers.join(',') + "\n";

                $.each(data.records, function(index, item) {
                    var name = item.name;
                    var location =item.location ? item.location.name : 'unknown'; 
                    var type = item.membershiptype ? item.membershiptype.type : 'unknown'; 
                    var soldBy = item.user ? item.user.name : 'unknown'; 
                    var weeklyB = item.weekly_billing;
                    var monthlyB = item.monthly_billing;
                    var DateSold = item.sold_date;

                    var rowData = [
                        name,
                        type,
                        weeklyB,
                        monthlyB,
                        location,
                        soldBy,
                        DateSold,
                    ];

                    csvContent += rowData.join(',') + "\n";
                });
            }
        });
    </script>
@endsection
