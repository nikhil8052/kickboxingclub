@extends('admin_layout.master') 
@section('content')

@section('css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />

<style>
    table#users_data_table .regular-pay {
        cursor: pointer;
        text-decoration: underline;
    }

    .close{
        text-decoration: none;
    }
</style>

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
                <div class="form-group">
                    <label class="form-label" for="location">Locations</label>
                    <select name="location" id="location" class="form-select" name="location">
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
                    <button class="btn btn-dark" id="filter">Search</button>
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
                    <div class="card card-bordered card-preview mt-5 p-4">
                        <table id="users_data_table" class="nowrap nk-tb-list nk-tb-ulist table table-tranx" data-auto-responsive="false">
                            <thead>
                                <tr class="nk-tb-item nk-tb-head">
                                    <th class="nk-tb-col"><span class="sub-text">ID</span></th>
                                    <th class="nk-tb-col"><span class="sub-text">Employee Name</span></th>
                                    <th class="nk-tb-col"><span class="sub-text">Email</span></th>
                                    <th class="nk-tb-col"><span class="sub-text">Country</span></th>
                                    <th class="nk-tb-col"><span class="sub-text">Location</span></th>
                                    <th class="nk-tb-col"><span class="sub-text">Regular Pay</span></th>
                                    <th class="nk-tb-col"><span class="sub-text">Instructor Pay</span></th>
                                </tr>
                            </thead>
                            <tbody >
                                
                            </tbody>
                        </table>
                    </div>
                    <div class="modal fade" id="modalForm">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Update Pay Rate</h5>
                                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                                        <em class="icon ni ni-cross"></em>
                                    </a>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ url('/admin-dashboard/pay-rates/procc') }}" id="payRateform" method="post">
                                        @csrf
                                        <input type="hidden" id="employee_id" name="employee_id" value="">
                                        <div class="form-group">
                                            <label class="form-label" for="regular_pay">Regular Pay</label>
                                            <div class="form-control-wrap">
                                                <input type="number" class="form-control" id="regular_pay" name="regular_pay" value="">
                                            </div>
                                            <span class="text text-danger" id="regular-error" style="display:none;">This field is required</span>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label" for="instructor_pay">Instructor Pay</label>
                                            <div class="form-control-wrap">
                                                <input type="number" class="form-control" id="instructor_pay" name="instructor_pay" value="">
                                            </div>
                                            <span class="text text-danger" id="instructor-error" style="display:none;">This field is required</span>
                                        </div>
                                        <div class="form-group">
                                            <button type="button" class="btn btn-dark update_rates_btn">Update</button>
                                        </div>
                                    </form>
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
    $(document).ready(function() {
        $('#overlay').show();
        // var startOfMonth = moment().subtract(1, "month").startOf("month").format('YYYY-MM-DD');
        var startOfMonth = moment().year(2020).month(0).date(1).format('YYYY-MM-DD');
        var endOfMonth = moment().subtract(1, "month").endOf("month").format('YYYY-MM-DD');

        $('#date-range-picker').val(startOfMonth + ' - ' + endOfMonth);

        var table = $('#users_data_table').DataTable({
            
            processing: true,
            serverSide: true, 
            ajax: {
                url: "{{ url('/admin-dashboard/get-employees') }}", 
                type: 'GET',
                data: function (d) {
                    var dateRange = $('#date-range-picker').val();
                    if (dateRange) {
                        var dates = dateRange.split(" - ");
                        d.start_date = dates[0]; 
                        d.end_date = dates[1];  
                    } else {
                        d.start_date = startOfMonth;
                        d.end_date = endOfMonth;
                    }
                    d.location_id = $('#location').val(); 
                },
                dataSrc: function(json) {
                    return json.data; 
                },
                beforeSend: function() {
                    $('#overlay').show();
                },
                complete: function() {
                    $('#overlay').hide();
                } 
            },
            columns: [
                { data: 'employee_id' },
                { data: 'user.full_name'},
                { data: 'user.email'},
                { data: 'user.country', 
                    render: function(data, type, row) {
                        return data ? data : 'unknown';
                    }
                },
                { data: 'user.location.name', 
                    render: function(data, type, row) {
                        return data ? data : 'unknown';
                    }
                },
                { data: 'payrate.regular_pay',
                    render: function(data, type, row) {
                        return data ? `$${data}` : 'null';
                    },
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).addClass('regular-pay');
                    }
                },
                { data: 'payrate.instructor_pay',
                    render: function(data, type, row) {
                        return data ? `$${data}` : 'null';
                    },
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).addClass('regular-pay');
                    }
                }
            ],
            stateSave: true
        });

        $('#filter').on('click', function() {
            // $('#overlay').show();
            table.ajax.reload();
        });

        $('#users_data_table').on('click', '.regular-pay', function() {
            var row = $(this).closest('tr');
            var data = table.row(row).data();

            var id = data['employee_id'];
            var regular_pay = data.payrate ? data.payrate.regular_pay : undefined;
            var instructor_pay = data.payrate ? data.payrate.instructor_pay : undefined;

            $('#employee_id').val(id);
            $('#regular_pay').val(regular_pay);
            $('#instructor_pay').val(instructor_pay);
            $('#regular-error').hide();
            $('#instructor-error').hide();
            $('#modalForm').modal('show');
        });

        $('.update_rates_btn').on('click', function(e) {
            e.preventDefault(); 
            var isValid = true;
            var regularPay = $('#regular_pay').val();
            var instructorPay = $('#instructor_pay').val();

            if (regularPay == null || regularPay == '') {
                $('#regular-error').show();
                isValid = false;
            } else {
                $('#regular-error').hide();
            }

            if (instructorPay == null || instructorPay == '') {
                $('#instructor-error').show();
                isValid = false;
            } else {
                $('#instructor-error').hide();
            }

            if (isValid) {
                $('#payRateform').submit();
            }
        });

        $('#export-button').on('click', function () {
            $.ajax({
                url: "{{ url('/admin-dashboard/get-employees') }}",
                type: 'GET',
                data: {
                    export: true, // Custom parameter to indicate export
                    start_date: $('#date-range-picker').val().split(" - ")[0],
                    end_date: $('#date-range-picker').val().split(" - ")[1],
                    location_id: $('#location').val()
                },
                success: function (response) {
                    var csvContent = '';

                    // Generate CSV headers
                    var headers = [];
                    $('#users_data_table thead tr th').each(function () {
                        var headerText = $(this).text().trim();
                        if (headerText !== '') { 
                            headers.push(headerText.replace(/,/g, "")); 
                        }
                    });

                    csvContent += headers.join(',') + "\n";
                    response.data.forEach(function (rowData) {
                        var csvRow = [];
                        csvRow.push(rowData.employee_id);
                        csvRow.push(rowData.user ? rowData.user.full_name : 'unknown');
                        csvRow.push(rowData.user ? rowData.user.email : 'unknown');
                        csvRow.push(rowData.user ? (rowData.user.country ? rowData.user.country : 'unknown') : 'unknown');
                        csvRow.push(rowData.user && rowData.user.location ? rowData.user.location.name : 'unknown');
                        csvRow.push(rowData.payrate ? `$${rowData.payrate.regular_pay}` : 'null');
                        csvRow.push(rowData.payrate ? `$${rowData.payrate.instructor_pay}` : 'null');

                        csvContent += csvRow.join(',') + "\n";
                    });

                    var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                    var link = document.createElement('a');
                    if (link.download !== undefined) {
                        var url = URL.createObjectURL(blob);
                        link.setAttribute('href', url);
                        link.setAttribute('download', 'Employees_Export.csv');
                        link.style.visibility = 'hidden';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching data for export:", status, error);
                }
            });
        });

    });
</script>


<script type="text/javascript">
    $(document).ready(function () {
        var start = moment("2020-01-01").startOf("month");
        var end = moment().subtract(1, "month").endOf("month");
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

@endsection
