@extends('admin_layout.master')
@section('content')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />

    <style>
        table#payrolesTable .employee-name {
            cursor: pointer;
            /* text-decoration: underline; */
        }

        .close{
            text-decoration: none;
        }
    </style>
@endsection

    <div class="nk-content">
        <div class="modal fade" id="modalForm">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Employee Details</h5>
                        <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <em class="icon ni ni-cross"></em>
                        </a>
                    </div>
                    <div class="modal-body">
                        <p>Employee name: <span id="name">kjfhhuihi</span></p>
                        <p>Location: <span id="location_id">erfr</span></p>
                        <p>Start Date: <span id="start_date">erfr</span</p>
                        <p>End Date: <span id="end_date">erfr</span</p>
                        <p>Duration: <span id="duration">erfr</span</p>
                        <p>Total Payrate: <span id="total-pay">erfr</span</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="d-flex paper card-preview card-bordered p-4 mb-3 date-filter">
                <div class="col-md-4">
                    <div class="form-control-wrap">
                        <label class="form-label" for="date-range-picker">Date</label>
                        <input type="text" id="date-range-picker" class="form-control" />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-control-wrap">
                        <label class="form-label" for="location">Locations</label>
                        <select name="location" id="location" class="form-select">
                            @if(isset($locations) && $locations != null)
                                <option value="">All</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->location_id ?? '' }}">{{ $location->name ?? '' }}</option>
                                @endforeach
                            @endif
                        </select> 
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <button class="btn btn-dark" id="apply-filters">Apply Filter</button>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <button class="btn btn-dark" id="export-button"><i class="fa fa-download"></i> Export</button>
                    </div>
                </div>
            </div> 
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block">
                        <div class="card card-bordered card-preview">
                            <div class="dataTables_wrapper no-footer">
                                <table id="payrolesTable" class="nowrap nk-tb-list nk-tb-ulist table table-tranx dataTable no-footer" data-auto-responsive="true">
                                    <thead>
                                        <tr class="nk-tb-item nk-tb-head">
                                            <td class="nk-tb-col">Sno.</td>
                                            <td class="nk-tb-col">Employee Name</td>
                                            <td class="nk-tb-col">Location</td>
                                            <td class="nk-tb-col">Start Date</td>
                                            <td class="nk-tb-col">Start time</td>
                                            <td class="nk-tb-col">End date</td>
                                            <td class="nk-tb-col">End time</td>
                                            <td class="nk-tb-col">Duration</td>
                                            <td class="nk-tb-col">Regular PayRate</td>
                                        </tr>
                                    </thead>
                                    <tbody id="payroll-table-data">

                                    </tbody>
                                </table>
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
    <script>
        $(document).ready(function () {
            $('#apply-filters').on('click', function(){
                var dateRange = $('#date-range-picker').val();
                var dates = dateRange.split(" - ");
                startDate = dates[0];
                endDate = dates[1];

                var location = $('#location').val();

                membershipFilter(location, startDate, endDate);
            });

            function membershipFilter(location, startDate, endDate) {
                var data = {
                    start_date: startDate,
                    end_date: endDate,
                    location_id: location,
                };

                $.ajax({
                    url: "{{ url('admin-dashboard/get-payroll') }}", 
                    type: "GET", 
                    data: data, 
                    success: function(response) {
                        updateMembershipTable(response.shifts);
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX error:", status, error);
                    }
                });
            }

            function updateMembershipTable(data) {
                var table = $('#payroll-table-data');
                table.empty(); 

                $.each(data, function(index, item) {
                    var employeeName = item.employee && item.employee.user ? item.employee.user.full_name : 'Unknown';
                    var emp_id = item.employee && item.employee.user ? item.employee.user.user_id : 'undefined';
                    var startDateTime = formatDateTime(item.start_datetime);
                    var endDateTime = formatDateTime(item.end_datetime);
                    var totalDuration = secondsToTime(Math.floor(item.duration || 0));
                    var regularPay = item.regular_pay_amount.toFixed(2);

                    var row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td class="employee-name" data-id=${emp_id}' data-location=${item.location.location_id}>${employeeName}</td>
                            <td>${item.location.name}</td>
                            <td>${startDateTime.date}</td>
                            <td>${startDateTime.time}</td>
                            <td>${endDateTime.date}</td>
                            <td>${endDateTime.time}</td>
                            <td>${totalDuration}</td>
                            <th>$${regularPay}</th>
                        </tr>
                    `;
                    table.append(row); 
                });

                $('.employee-name').on('click', function(e) {
                    e.preventDefault();
                    var id = $(this).data('id');
                    var locationId = $(this).data('location');
                    var dateRange = $('#date-range-picker').val();
                    var dates = dateRange.split(" - ");
                    startDate = dates[0];
                    endDate = dates[1];
                    employeeDetails(id,locationId,startDate,endDate);
                    // $('#modalForm').modal('show');
                });

                function employeeDetails(id,locationId,startDate,endDate){
                    var data = {
                        id: id,
                        location_id: locationId,
                        startDate: startDate,
                        endDate: endDate,
                        _token: "{{ csrf_token() }}"
                    }
                    $.ajax({
                        url: "{{ url('admin-dashboard/get/employees/details') }}",
                        type: "post",
                        data: data,
                        dataType: "json",
                        success: function(response){

                            if (response.success === true) {
                                if (response.details) {
                                    var details = response.details;
                                    var totalPayrate = 0;
                                    var totalDuration = 0;
                                    $.each(details, function(key, val) {
                                        totalPayrate += parseFloat(val.regular_pay_amount);
                                        var name = val.employee.user.full_name;
                                        var location = val.location.name;
                                        var startDate = data.startDate;
                                        var endDate = data.endDate;
                                        totalDuration += parseFloat(val.duration);
                                        duration = secondsToTime(Math.floor(totalDuration || 0));

                                        $("#name").html(name);
                                        $("#location_id").html(location);
                                        $("#start_date").html(startDate);
                                        $("#end_date").html(endDate);
                                        $("#duration").html(duration);
                                        $("#total-pay").html(totalPayrate.toFixed(2));
                                    });
                                }
                                $('#modalForm').modal('show');
                            }
                        },
                        error: function(error) {
                            console.log("AJAX error:",error);
                        }
                    })
                }
            }

            function formatDateTime(dateTimeString) {
                if (!dateTimeString) return { date: null, time: null };
                let date = new Date(dateTimeString);
                return {
                    date: date.toISOString().split('T')[0],  
                    time: date.toTimeString().split(' ')[0]  
                };
            }

            function secondsToTime(seconds) {
                var hours = Math.floor(seconds / 3600);
                var minutes = Math.floor((seconds % 3600) / 60);
                var secs = seconds % 60;

                return [
                    hours.toString().padStart(2, '0'),
                    minutes.toString().padStart(2, '0'),
                    secs.toString().padStart(2, '0')
                ].join(':');
            }

            membershipFilter('', moment().startOf('month').format('YYYY-MM-DD'), moment().endOf('month').format('YYYY-MM-DD'));
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#export-button').on('click', function () {
                var csvContent = '';

                var headers = [];
                $('#payrolesTable thead tr.nk-tb-head td').each(function () {
                    var headerText = $(this).text().trim();
                    if (headerText !== '') { 
                        headers.push(headerText.replace(/,/g, "")); 
                    }
                });

                csvContent += headers.join(',') + "\n"; 

                $('#payrolesTable tbody tr').each(function () {
                    var rowData = [];
                    $(this).find('td').each(function () {
                        var cellText = $(this).text().trim(); 
                        rowData.push(cellText.replace(/,/g, "")); 
                    });
                    $(this).find('th').each(function () {
                        var cellText = $(this).text().trim(); 
                        rowData.push(cellText.replace(/,/g, "")); 
                    });
                    csvContent += rowData.join(',') + "\n"; 
                });

                var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });

                var link = document.createElement('a');
                if (link.download !== undefined) { 
                    var url = URL.createObjectURL(blob);
                    link.setAttribute('href', url);
                    link.setAttribute('download', 'Payroll_Export.csv');
                    link.style.visibility = 'hidden';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            });
        });
    </script>
@endsection