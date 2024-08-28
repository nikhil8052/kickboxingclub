@extends('admin_layout.master')
@section('content')

@section('css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />
@endsection

<div class="nk-content ">
     <div class="container-fluid">
          <!-- <div class="d-flex paper card-preview card-bordered p-4 mb-3">
               
          </div> -->
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
                                        <div class="d-flex justify-content-end">
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
                                        <table class="nowrap nk-tb-list nk-tb-ulist table table-bordered">
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
                                                            $membership_billing = App\Models\BillingCycle::where('start_date_copy',$current_date)->whereIn('status',['active','done'])->with('locations')->get();
                                                            // $membership_billing = App\Models\MembershipInstances::where('purchase_date',$current_date)->whereIn('status',['active','done'])->with('locations')->get();
                                                            // $membership_billing = App\Models\Orders::where('date_placed_copy',$current_date)->where('status','Completed')->get();
                                                       ?>
                                                                                    
                                                       @foreach($membership_billing as $billing)
                                                            @if($billing->locations->name === 'Torrance')
                                                                 <?php $torrance_billing += $billing->renewal_rate; ?>
                                                            @elseif($billing->locations->name === 'Lakewood')
                                                                 <?php $lakewood_billing += $billing->renewal_rate; ?>
                                                            @elseif($billing->locations->name === 'Orange')
                                                                 <?php $orange_billing += $billing->renewal_rate; ?>
                                                            @endif
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
          var month = $('#month').val();
          var url = `{{ url('admin-dashboard/get/billing-stats') }}?month=${month}`;
          window.location.href = url;
     }
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



    
    // Get the current date
    const today = new Date();
    // Extract the current year and month, and format them as "YYYY-MM"
    const currentMonth = today.toISOString().slice(0, 7);
    // Set the value of the input field to the current month
    document.getElementById('month').value = currentMonth;



</script>
@endsection