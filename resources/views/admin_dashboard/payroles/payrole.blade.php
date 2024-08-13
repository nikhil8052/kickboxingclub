@extends('admin_layout.master')
@section('content')
    <div class="nk-content">
        <div class="container-fluid">
            <!-- <div class="d-flex card-preview card-bordered p-4 mb-3">
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
            </div> -->
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block">
                        <div class="card card-bordered card-preview">
                            <div class="dataTables_wrapper no-footer">
                                <table id="payrolesTable" class="datatable-init nowrap nk-tb-list nk-tb-ulist table table-tranx dataTable no-footer" data-auto-responsive="true">
                                    <thead>
                                        <tr class="nk-tb-item nk-tb-head">
                                            <td class="nk-tb-col"></td>
                                            <!-- <td class="nk-tb-col">Employee ID</td> -->
                                            <td class="nk-tb-col">Employee Name</td>
                                            <td class="nk-tb-col">Location</td>
                                            <td class="nk-tb-col">Start Date</td>
                                            <td class="nk-tb-col">Start time</td>
                                            <td class="nk-tb-col">End date</td>
                                            <td class="nk-tb-col">End time</td>
                                            <td class="nk-tb-col">Duration</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($alldata as $data)
                                            @php
                                                $startDateTime = Carbon\Carbon::parse($data->start_datetime);
                                                $formattedStartDate = $startDateTime->format('Y-m-d');
                                                $formattedStartTime = $startDateTime->format('H:i:s');

                                                if ($data->end_datetime) {
                                                    $endDateTime = Carbon\Carbon::parse($data->end_datetime);
                                                    $interval = $startDateTime->diff($endDateTime);
                                                    $formattedDuration = $interval->format('%H:%I:%S');
                                                    $formattedEndDate = $endDateTime->format('Y-m-d');
                                                    $formattedEndTime = $endDateTime->format('H:i:s');
                                                } else {
                                                    $formattedDuration = null;
                                                    $formattedEndDate = null;
                                                    $formattedEndTime = null;
                                                }
                                            @endphp
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <!-- <td>{{ $data->time_clock_id }}</td> -->
                                                <td>{{ $data->employee->user->full_name }}</td>
                                                <td>{{ $data->location->name }}</td>
                                                <!-- <td>{{ $formattedDate ?? null }}</td> -->
                                                <td>{{ $formattedStartDate ?? null }}</td>
                                                <td>{{ $formattedStartTime ?? null }}</td>
                                                <td>{{ $formattedEndDate ?? null }}</td>
                                                <td>{{ $formattedEndTime ?? null }}</td>
                                                <td>{{ $formattedDuration ?? null }}</td>
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

    @section('js')
        <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    @endsection

    <!-- <script type="text/javascript">
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
            var start = moment().startOf("day");
            var end = moment().endOf("day");
            filterData(start, end);
        });
    </script> -->
    <!-- <script type="text/javascript">
        $(document).ready(function () {
            $("#payrolesTable").DataTable({
                processing: true,
                serverSide: false,
                pageLength: 50, 
                lengthMenu: [50, 100, 150], 
                dom: "Bfrtip",
                buttons: ["copy", "excel", "pdf", "print"],
                ajax: {
                    url: "{{ url('/admin-dashboard/get-payroles') }}",
                    type: "GET",
                    dataSrc: "",
                },
                columns: [{ data: "order_id" }, { data: "location" }, { data: "currency" }, { data: "total" }, { data: "status" }, { data: "date_placed" }],
            }); 
        });
    </script> -->
@endsection