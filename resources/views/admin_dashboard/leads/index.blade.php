@extends('admin_layout.master')
@section('content')

@section('css')
     <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
     <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />
@endsection

<div class="nk-content">
     <div class="container-fluid">
          <div class="d-flex paper card-preview card-bordered p-4 mb-3 date-filter">
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
                         <div class="card card-bordered card-preview">
                              <div class="card-inner">
                                   <table id="trials_purchased" class="nowrap nk-tb-list nk-tb-ulist table table-tranx dataTable" data-auto-responsive="false">
                                        <thead>
                                             <tr class="nk-tb-item nk-tb-head">
                                                  <th class="nk-tb-col"><span class="sub-text"></span>Name</th>
                                                  <th class="nk-tb-col"><span class="sub-text"></span>Email</th>
                                                  <th class="nk-tb-col"><span class="sub-text"></span>Phone</th>
                                                  <th class="nk-tb-col"><span class="sub-text"></span>Trial</th>
                                                  <th class="nk-tb-col"><span class="sub-text"></span>Date</th>
                                             </tr>
                                        </thead>
                                        <tbody>
                                             
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
     $(document).ready(function () {
          var start = moment("2020-01-01").startOf("month");
          var end = moment().endOf("month");
          $("#date-range-picker").daterangepicker({
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
     $(document).ready(function () {
          var currentData = [];
          $('#trials_purchased').DataTable();
          $('#filter').on('click', function(){
               var dateRange = $('#date-range-picker').val();
               var dates = dateRange.split(" - ");
               startDate = dates[0];
               endDate = dates[1];

               var location = $('#location').val();

               purchasedTrialsFilter(location, startDate, endDate);
          });

          function purchasedTrialsFilter(location, startDate, endDate) {
               $('#overlay').show();
               var data = {
                    start_date: startDate,
                    end_date: endDate,
                    location_id: location,
               };

               $.ajax({
                    url: "{{ url('/admin-dashboard/get/trial/purchased') }}", 
                    type: "GET", 
                    data: data, 
                    success: function(response) {
                         currentData = response.data;
                         updatePurchasedTrialsTable(response.data);
                         $('#overlay').hide();
                    },
                    error: function(xhr, status, error) {
                         console.error("AJAX error:", status, error);
                         $('#overlay').hide();
                    }
               });
          }

          function updatePurchasedTrialsTable(data) {
               var table = $('#trials_purchased').DataTable();
               table.clear(); 
               var rows = [];

               $.each(data, function(index, item) {
                    var user = item.user ? item.user.full_name : 'unknown';
                    var email = item.user ? item.user.email: 'unknown';
                    var phone = item.user ? item.user.phone_number: 'unknown';
                    var trial = item.membership_name;
                    var date = item.purchase_date;

                    rows.push([
                         user,
                         email,
                         phone,
                         trial,
                         date
                    ]);
                
               });

               table.rows.add(rows); 
               table.draw();
          }

          $('#export-button').on('click', function () {
               var csvContent = '';
               var table = $('#trials_purchased').DataTable();
               
               var headers = [];
               $('#trials_purchased thead tr th').each(function () {
                    var headerText = $(this).text().trim();
                    if (headerText !== '') { 
                         headers.push(headerText.replace(/,/g, "")); 
                    }
               });

               csvContent += headers.join(',') + "\n";

               table.rows({ search: 'applied' }).every(function () {
                    var rowData = this.data(); 
                    var csvRow = rowData.map(function(cell) {
                         return typeof cell === 'string' ? cell.replace(/,/g, "") : cell;
                    });
                    csvContent += csvRow.join(',') + "\n";
               });

               var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });

               var link = document.createElement('a');
               if (link.download !== undefined) { 
                    var url = URL.createObjectURL(blob);
                    link.setAttribute('href', url);
                    link.setAttribute('download', 'Trials_Purchased_Not_Scheduled_Export.csv');
                    link.style.visibility = 'hidden';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
               }
          });
          purchasedTrialsFilter('', moment().year(2020).month(0).date(1).format('YYYY-MM-DD'), moment().endOf('month').format('YYYY-MM-DD'));
    });
</script>

<script>
     // $(document).ready(function() {
     //      var table = $('#trials_purchased').DataTable({
                
     //           processing: true,
     //           serverSide: false, 
     //           ajax: {
     //                url: "{{ url('/admin-dashboard/get/trial/purchased') }}", 
     //                type: 'GET',
     //                dataSrc: '',
     //                beforeSend: function() {
     //                     $('#overlay').show();
     //                },
     //                complete: function() {
     //                     $('#overlay').hide();
     //                } 
     //           },
     //           columns: [
     //                { data: 'user.full_name', 
     //                     render: function(data, type, row) {
     //                          return data ? data : 'unknown';
     //                     }
     //                },
     //                { data: 'user.email', 
     //                     render: function(data, type, row) {
     //                          return data ? data : 'unknown';
     //                     }
     //                },
     //                { data: 'user.phone_number', 
     //                     render: function(data, type, row) {
     //                          return data ? data : 'unknown';
     //                     }
     //                },
     //                { data: 'membership_name', 
     //                     render: function(data, type, row) {
     //                          return data ? data : 'unknown';
     //                     }
     //                },
     //                { data: 'purchase_date', 
     //                     render: function(data, type, row) {
     //                          return data ? data : 'unknown';
     //                     }
     //                },
               
     //           ]
     //      });
     // });

</script>



@endsection