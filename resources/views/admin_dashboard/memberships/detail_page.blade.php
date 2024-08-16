@extends('admin_layout/master')
@section('content')

@section('css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />
@endsection

<div class="nk-content membership-status">
     <div class="container-fluid">
          <?php if($_GET['membership'] === 'Vip'){
                    $membership = 'VIP';
               }else{
                    $membership = $_GET['membership'];
               }?>
          <h3 class="mem-name">{{ $membership ?? '' }}</h3>
          <div class="d-flex m-4 main-head-status">
               <div class="status-head">
                    <div class="form-group">
                         <button class="btn btn-light status-btn" id="active" onclick="getUserByStatus('active')">Active</button>
                    </div>
               
                    <div class="form-group">
                         <button class="btn btn-light status-btn" id="pending" onclick="getUserByStatus('pending')">Pending</button>
                    </div>
                    <div class="form-group">
                         <button class="btn btn-light status-btn" id="cancelled" onclick="getUserByStatus('cancelled')">Cancelled</button>
                    </div>
               </div>
          
               <!-- <div class="export">
                    <div class="form-group">
                         <a class="btn btn-dark">Export</a>
                    </div>
               </div> -->
          </div>
          <div class="d-flex user-filter">
               <div class="col-md-3">
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
                                   @if(isset($_GET['location']))
                                        @if($_GET['location'] == $location->location_id)
                                        <option value="{{ $location->location_id ?? '' }}" selected>{{ $location->name ?? '' }}</option>
                                        @else
                                        <option value="{{ $location->location_id ?? '' }}">{{ $location->name ?? '' }}</option>
                                        @endif
                                   @endif
                              @endforeach
                         @endif
                         </select>
                    </div>
               </div>
               <div class="col-md-3">
                    <div class="form-control-wrap">
                         <button class="btn btn-dark" onclick="userFilter()">Search</button>
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
                    <div class="components-preview">
                         <div class="nk-block nk-block-lg">
                              <div class="card card-bordered card-preview">
                                   @if(isset($_GET['start']) && isset($_GET['end']))
                                   <input type="hidden" id="startdate" value="{{ $_GET['start'] }}">
                                   <input type="hidden" id="enddate" value="{{ $_GET['end'] }}">
                                   @endif
                                   <div class="card-inner">
                                        <table class="datatable-init nowrap nk-tb-list nk-tb-ulist table table-bordered" id="membership-table" data-auto-responsive="false">
                                             <thead>
                                                  <tr class="nk-tb-item nk-tb-head">
                                                       <th scope="col">Name</th>
                                                       <th scope="col">Email Address</th>
                                                       <th scope="col">Phone Number</th>
                                                  </tr>
                                             </thead>
                                             <tbody id="users_data">
                                             @if(isset($memberships) && $memberships != null)
                                                  @foreach($memberships as $membership)
                                                  <tr>
                                                       <td>{{ $membership->user->full_name ?? '' }}</td>
                                                       <td>{{ $membership->user->email ?? '' }}</td>
                                                       <td>{{ $membership->user->phone_number ?? '' }}</td>
                                                  </tr>
                                                  @endforeach
                                             @endif
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

<script>
     function getUserByStatus(status){
          const queryString = window.location.search;
          const urlParams = new URLSearchParams(queryString);
          const location = urlParams.get('location');
          const membership = urlParams.get('membership');
          const start = urlParams.get('start');
          const end = urlParams.get('end');

          var url = `{{ url('admin-dashboard/memberships/status') }}?status=${status} &location=${location} &membership=${membership} &start=${start} &end=${end}`;
          window.location.href = url;
     }

     $(document).ready(function(){
          const queryString = window.location.search;
          const urlParams = new URLSearchParams(queryString);
          const status = urlParams.get('status');
          
          if(status !== '' && status !== null && status !== undefined){
               $('#'+status).addClass('active');
          }
     });
</script>

<script>
     function userFilter(){
          var date = $('#date-range-picker').val();
          var dates = date.split(" - ");
          var startDate = dates[0];
          var endDate = dates[1]; 
          var location = $('#location').val();

          const queryString = window.location.search;
          const urlParams = new URLSearchParams(queryString);
          const membership = urlParams.get('membership');
          const status = urlParams.get('status');

          var url = `{{ url('admin-dashboard/memberships/status') }}?status=${status} &location=${location} &membership=${membership} &start=${startDate} &end=${endDate}`;
          window.location.href = url;
          
     }
</script>

<script type="text/javascript">
    $(document).ready(function () {
          // var start = moment().startOf("month");
          // var end = moment().endOf("month");
          var start = $('#startdate').val();
          var end = $('#enddate').val();
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
$(document).ready(function () {
     $('#export-button').on('click', function () {
          var csvContent = '';

          var headers = [];
          $('#membership-table thead tr.nk-tb-head td').each(function () {
          var headerText = $(this).text().trim();
          if (headerText !== '') { 
               headers.push(headerText.replace(/,/g, "")); 
          }
          });

          csvContent += headers.join(',') + "\n"; 

          $('#membership-table tbody tr').each(function () {
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
               link.setAttribute('download', 'users.csv');
               link.style.visibility = 'hidden';
               document.body.appendChild(link);
               link.click();
               document.body.removeChild(link);
          }
     });
});

</script>

@endsection