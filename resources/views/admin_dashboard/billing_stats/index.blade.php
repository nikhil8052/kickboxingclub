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
                    <div class="components-preview wide-md mx-auto">
                         <div class="nk-block-head nk-block-head-lg wide-sm">
                              <div class="nk-block-head-content">
                              </div>
                         </div>
                         <div class="nk-block nk-block-lg">
                              <div class="card card-bordered card-preview">
                                   <div class="card-inner">
                                        <div class="d-flex justify-content-between">
                                             <div class="nk-block-head-content">
                                                  <div class="mbsc-form-group">
                                                       <span class="d-none d-sm-inline-block"></span>
                                                       <button class="btn btn-dark" id="export-button"><i class="fa fa-download"></i> Export</button>
                                                  </div>
                                             </div>
                                             <div class="dropdown-filter">
                                                  <label for="month">
                                                       <span class="d-none d-sm-inline-block">Month</span>
                                                       <div class="form-control">
                                                            <input type="month" id="month" name="month">
                                                       </div>
                                                  </label>
                                                  <label for="year">
                                                       <span class="d-none d-sm-inline-block"></span>
                                                       <div class="form-control">
                                                            <button type="button" onclick="searchByMonthANDYear()">Search</button>
                                                       </div>
                                                  </label>
                                             </div>
                                        </div>
                                        <h6 class="mt-2">Billing Stats</h6>
                                        <table class="nowrap nk-tb-list nk-tb-ulist table table-bordered" id="billing-table">
                                             <thead>
                                                  <tr>
                                                       <th>Date</th>
                                                       <th>Torrance</th>
                                                       <th>Lakewood</th>
                                                       <th>Orange</th>
                                                  </tr>
                                             </thead>
                                             <tbody>
                                             @if(isset($dates) && $dates != null)
                                                  @foreach($dates as $date)
                                                  <tr>
                                                       <?php 
                                                            $current_date = $date->format('Y-m-d'); 
                                                            $torrance_billing = 0;
                                                            $lakewood_billing = 0;
                                                            $orange_billing = 0;
                                                       ?>
                                                       <td>{{ $current_date ?? '' }}</td>
                                                       <?php
                                                            $membershipInstanceIds = App\Models\MembershipInstances::where('billing_type','bill_on_purchase')->whereIn('status',['done','active'])->pluck('membership_id');
                                                            $orders = App\Models\Orders::where('date_created_copy',$current_date)
                                                                      ->whereHas('orderlines', function($query) use ($membershipInstanceIds) {
                                                                           $query->whereIn('membership_instance_id', $membershipInstanceIds);
                                                                      })

                                                                      ->with(['orderlines' => function($query) use ($membershipInstanceIds) {
                                                                           $query->whereIn('membership_instance_id', $membershipInstanceIds)
                                                                                ->with('membership_instance');
                                                                      }])
                                                                      ->get();

                                                       ?>
                                                       @foreach($orders as $order)
                                                            @foreach($order->orderlines as $orderline)
                                                                 <?php      
                                                                      $location = $order->location;
                                                                      $total = $orderline->line_total;
                                                                 ?>
                                                                 
                                                                 @if($location === 'Torrance')
                                                                 <?php $torrance_billing += $total; ?>
                                                                 @endif
                                                                 @if($location === 'Lakewood')
                                                                 <?php $lakewood_billing += $total; ?>
                                                                 @endif
                                                                 @if($location === 'Orange')
                                                                 <?php $orange_billing += $total; ?>
                                                                 @endif
                                                            @endforeach
                                                       @endforeach
                                             
                                                       <td>${{ number_format($torrance_billing) ?? '' }}</td>
                                                       <td>${{ number_format($lakewood_billing) ?? '' }}</td>
                                                       <td>${{ number_format($orange_billing) ?? '' }}</td>   
                                                  </tr>
                                                  @endforeach
                                             @endif
                                             </tbody>
                                        </table>
                                   </div>
                              </div><!-- .card-preview -->
                         </div><!-- nk-block -->
                    </div><!-- .components-preview -->
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

<script>
     $(document).ready(function(){
          const queryString = window.location.search;
          const urlParams = new URLSearchParams(queryString);
          const month = urlParams.get('month');
          
          if(month !== '' && month !== null && month !== undefined){
               $('#month').val(month);
          }
     })
</script>

<script>
     function searchByMonthANDYear(){
          $('#overlay').show();
          var month = $('#month').val();
          var url = `{{ url('admin-dashboard/get/billing-stats') }}?month=${month}`;
          window.location.href = url;
     }
     // $('#overlay').hide();

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

          var start = moment().startOf("day");
          var end = moment().endOf("day");
          filterData(start, end);
     });

     const today = new Date();
     const currentMonth = today.toISOString().slice(0, 7);
     document.getElementById('month').value = currentMonth;

</script>

<script>

     $(document).ready(function(){
          $('#export-button').on('click',function(){
               let table = document.getElementById('billing-table');
               let rows = table.querySelectorAll('tr');
               let csv = [];

               rows.forEach(function (row) {
                    let cells = row.querySelectorAll('th, td');
                    let rowData = [];
                    cells.forEach(function (cell) {
                         let cellValue = '"' + cell.innerText.replace(/"/g, '""') + '"';
                         rowData.push(cellValue);
                    });
                    csv.push(rowData.join(','));
               });

               let csvContent = "data:text/csv;charset=utf-8," + csv.join("\n");

               let link = document.createElement('a');
               link.setAttribute('href', encodeURI(csvContent));
               link.setAttribute('download', 'Billing_Export.csv');
               document.body.appendChild(link);
               link.click();
               document.body.removeChild(link)
          });
     });

</script>

@endsection