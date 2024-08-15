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
                    <button class="btn btn-dark" id="export-button">Export</button>
                </div>
            </div>
        </div>
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block">
                    <div class="nk-block">
                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <h6 class="mt-2">Active</h6>
                                <table class="nowrap nk-tb-list nk-tb-ulist table table-bordered" id="payroll-stats-table" data-auto-responsive="false">
                                    <thead>
                                        <tr>
                                            <th scope="col">Employee</th>
                                            <th scope="col">Total Shifts</th>
                                            <th scope="col">Duration</th>
                                        </tr>
                                    </thead>
                                    <tbody id="payroll-table-data">
                                        @foreach($shifts as $shift)
                                        <tr>
                                            <td>{{ $shift->employee->user->full_name }}</td>
                                            <td>{{ $shift->total_shifts }}</td>
                                            <td>
                                                @php
                                                    $totalSeconds = $shift->total_duration_seconds;
                                                    $hours = floor($totalSeconds / 3600);
                                                    $minutes = floor(($totalSeconds % 3600) / 60);
                                                    $seconds = $totalSeconds % 60;
                                                @endphp
                                                {{ sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds) }}
                                            </td>
                                        </tr>
                                        @endforeach
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
            // function (start, end, label) {
            //         filterData(start, end);
            // }
        );

    });
</script>

<script >
    $(document).ready(function(){

        var location = '';
        var startDate = '';
        var endDate = '';

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
                url: "{{ url('admin-dashboard/payroll-stats') }}", 
                type: "GET", 
                data: data, 
                success: function(response) {
                    console.log(response); 
                    updateMembershipTable(response.shifts);
                },
                error: function(xhr, status, error) {
                    console.error("AJAX error:", status, error);
                }
            });
        }

        function updateMembershipTable(data) {
            var table = $('#payroll-table-data');
            table.empty(); // Clear existing rows

            $.each(data, function(index, item) {
                var employeeName = item.employee && item.employee.user ? item.employee.user.full_name : 'Unknown';
                var totalShifts = item.total_shifts || 0;
                var totalDuration = secondsToTime(Math.floor(item.total_duration_seconds || 0));

                var row = `
                    <tr>
                        <td>${employeeName}</td>
                        <td>${totalShifts}</td>
                        <td>${totalDuration}</td>
                    </tr>
                `;
                table.append(row); // Add new row
            });
            
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

    });

</script>
<script>
    $(document).ready(function () {
        $('#export-button').on('click', function () {
            var csvContent = '';

            // Fix the selection to use the correct header element
            var headers = [];
            $('#payroll-stats-table thead tr th').each(function () {
                var headerText = $(this).text().trim();
                if (headerText !== '') { 
                    headers.push(headerText.replace(/,/g, "")); 
                }
            });

            csvContent += headers.join(',') + "\n"; 

            $('#payroll-stats-table tbody tr').each(function () {
                var rowData = [];
                $(this).find('td').each(function () {
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
                link.setAttribute('download', 'export.csv');
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        });
    });
</script>

@endsection