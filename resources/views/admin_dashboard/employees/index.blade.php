@extends('admin_layout.master') 
@section('content')

<!-- <link rel="stylesheet" href="https://cdn.datatables.net/2.1.3/css/dataTables.dataTables.min.css" />

<script src="https://cdn.datatables.net/2.1.3/js/dataTables.min.js"></script> -->
@section('css')
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
                <div class="form-group">
                    <button class="btn btn-dark">Search</button>
                </div>
            </div>
        </div>

        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block">
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
                                            <!-- <th class="nk-tb-col"><span class="sub-text">Can Chat</span></th>
                                            <th class="nk-tb-col"><span class="sub-text">Is Active</span></th> -->
                                        </tr>
                                    </thead>
                                    <tbody >
                                        
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


<script type="text/javascript">
    $(document).ready(function() {
        $('#users_data_table').DataTable({
            processing: true,
            serverSide: false, 
            ajax: {
                url: "{{ url('/admin-dashboard/get-employees') }}", 
                type: 'GET',
                dataSrc: '' 
            },
            columns: [
                { data: 'employee_id' },
                { data: 'user.full_name'},
                { data: 'user.email'},
                { data: 'user.country',
                    render: function(data, type, row) {
                        return data ? data : 'null';
                    }
                },
                { data: 'user.location.name',
                    render: function(data, type, row) {
                        return data ? data : 'null'; 
                    }
                },
                // { data: 'recent_location_id',
                //     render: function (data, type, row) {
                //         return data ? data : 'null';
                //     }
                // },
                // { data: 'public_profile_id',
                //     render: function (data, type, row) {
                //         return data ? data : 'null';
                //     }
                // },
                // { data: 'can_chat',
                //     render: function(data, type, row) {
                //         return data == 1 ? 'Yes' : 'No';
                //     }
                // },
                // { 
                //     data: 'is_active',
                //     render: function(data, type, row) {
                //         return data == 1 ? 'Yes' : 'No';
                //     }
                // },
            ]
        });
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
