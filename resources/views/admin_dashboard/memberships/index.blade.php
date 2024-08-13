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
                    <div class="form-group">
                         <button class="btn btn-dark" onclick="dateFilter()">Search</button>
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
          </div>

          <div class="nk-content-inner">
               <div class="nk-content-body">
                    <div class="nk-block">
                         <div class="nk-block">
                              <div class="card card-bordered card-preview">
                                   <div class="card-inner">
                                        <!-- <div class="nk-block-between g-3 p-2">
                                             <div class="nk-block-head-content"></div>
                                             <div class="nk-block-head-content">
                                                  <div class="mbsc-form-group">
                                                       <label for="location"><span class="d-none d-sm-inline-block">Location:</span></label>
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
                                        </div> -->
                                        <h6 class="mt-2">Active</h6>
                                        <table class="nowrap nk-tb-list nk-tb-ulist table table-bordered" id="membership-table" data-auto-responsive="false">
                                             <thead>
                                                  <tr>
                                                       <th scope="col">Membership Name</th>
                                                       <th scope="col">Quantity</th>
                                                       <th scope="col">Billing</th>
                                                  </tr>
                                             </thead>
                                             <tbody>
                                                  <tr id="starter" class='clickable-row' data-status='active' data-member="Starter">
                                                       <td>Starter</td>
                                                       @if(isset($starter))
                                                            <?php $starter_billing = 0;?>
                                                       <td>{{ count($starter) }}</td>
                                                            @foreach($starter as $start)
                                                                 <?php 
                                                                      $starter_billing += $start->renewal_rate; 
                                                                 ?>
                                                            @endforeach
                                                            
                                                       <td>${{ number_format($starter_billing)?? '' }}</td>
                                                       @endif
                                                  </tr>
                                                  <tr id="club" class='clickable-row' data-status='active' data-member="Club">
                                                       <td>Club</td>
                                                       @if(isset($club))
                                                            <?php $club_billing = 0;?>
                                                       <td>{{ count($club) }}</td>
                                                            @foreach($club as $clu)
                                                                 <?php 
                                                                      $club_billing += $clu->renewal_rate; 
                                                                 ?>
                                                            @endforeach
                                                       <td>${{ number_format($club_billing) ?? '' }}</td>
                                                       @endif
                                                  </tr>
                                                  <tr id="gold" class='clickable-row' data-status='active' data-member="Gold">
                                                       <td>Gold</td>
                                                       @if(isset($gold))
                                                       <?php $gold_billing = 0;?>
                                                       <td>{{ count($gold) }}</td>
                                                            @foreach($gold as $gol)
                                                                 <?php 
                                                                      $gold_billing += $gol->renewal_rate; 
                                                                 ?>
                                                            @endforeach
                                                       <td>${{ number_format($gold_billing) ?? '' }}</td>
                                                       @endif
                                                  </tr>
                                                  <tr id="vip" class='clickable-row' data-status='active' data-member="Vip">
                                                       <td>VIP</td>
                                                       @if(isset($vip))
                                                       <?php $vip_billing = 0;?>
                                                       <td>{{ count($vip) }}</td>
                                                            @foreach($vip as $VIP)
                                                                 <?php 
                                                                      $vip_billing += $VIP->renewal_rate; 
                                                                 ?>
                                                            @endforeach
                                                       <td>${{ number_format($vip_billing) ?? '' }}</td>
                                                       @endif
                                                  </tr>
                                                  <tr id="totals">
                                                  @php 
                                                       $billing_total = $starter_billing + $club_billing + $gold_billing + $vip_billing;
                                                  @endphp
                                                       <th>Totals</th>
                                                       <th>{{ $total ?? '' }}</th>
                                                       <th>${{ number_format($billing_total) ?? '' }}</th>
                                                  </tr>
                                             </tbody>
                                        </table>
                                   </div>
                                   <div class="card-inner">
                                        <h6>Pending</h6>
                                        <table class="nowrap nk-tb-list nk-tb-ulist table table-bordered" id="membership-table" data-auto-responsive="false">
                                             <thead>
                                                  <tr>
                                                       <th scope="col">Membership Name</th>
                                                       <th scope="col">Quantity</th>
                                                       <th scope="col">Billing</th>
                                                  </tr>
                                             </thead>
                                             <tbody>
                                                  <tr id="starter_pending" class='clickable-row' data-status='pending' data-member="Starter">
                                                       <td>Starter</td>
                                                       @if(isset($pending_starter))
                                                            <?php $pending_starter_billing = 0;?>
                                                       <td>{{ count($pending_starter) }}</td>
                                                            @foreach($pending_starter as $pending_start)
                                                                 <?php 
                                                                      $pending_starter_billing += $pending_start->renewal_rate; 
                                                                 ?>
                                                            @endforeach
                                                            
                                                       <td>${{ number_format($pending_starter_billing)?? '' }}</td>
                                                       @endif
                                                  </tr>
                                                  <tr id="club_pending" class='clickable-row' data-status='pending' data-member="Club">
                                                       <td>Club</td>
                                                       @if(isset($pending_club))
                                                            <?php $pending_club_billing = 0;?>
                                                       <td>{{ count($pending_club) }}</td>
                                                            @foreach($pending_club as $pending_clu)
                                                                 <?php 
                                                                      $pending_club_billing += $pending_clu->renewal_rate; 
                                                                 ?>
                                                            @endforeach
                                                       <td>${{ number_format($pending_club_billing) ?? '' }}</td>
                                                       @endif
                                                  </tr>
                                                  <tr id="gold_pending" class='clickable-row' data-status='pending' data-member="Gold">
                                                       <td>Gold</td>
                                                       @if(isset($pending_gold))
                                                       <?php $pending_gold_billing = 0;?>
                                                       <td>{{ count($pending_gold) }}</td>
                                                            @foreach($pending_gold as $pending_gol)
                                                                 <?php 
                                                                      $pending_gold_billing += $pending_gol->renewal_rate; 
                                                                 ?>
                                                            @endforeach
                                                       <td>${{ number_format($pending_gold_billing) ?? '' }}</td>
                                                       @endif
                                                  </tr>
                                                  <tr id="vip_pending" class='clickable-row' data-status='pending' data-member="Vip">
                                                       <td>VIP</td>
                                                       @if(isset($pending_vip))
                                                       <?php $pending_vip_billing = 0;?>
                                                       <td>{{ count($pending_vip) }}</td>
                                                            @foreach($pending_vip as $VIP)
                                                                 <?php 
                                                                      $pending_vip_billing += $VIP->renewal_rate; 
                                                                 ?>
                                                            @endforeach
                                                       <td>${{ number_format($pending_vip_billing) ?? '' }}</td>
                                                       @endif
                                                  </tr>
                                                  
                                                  <tr id="total_pending">
                                                  @php 
                                                       $pending_billing_total = $pending_starter_billing + $pending_club_billing + $pending_gold_billing + $pending_vip_billing;
                                                  @endphp
                                                       <th>Totals</th>
                                                       <th>{{ $pending_total ?? '' }}</th>
                                                       <th>${{ number_format($pending_billing_total) ?? '' }}</th>
                                                  </tr>
                                             </tbody>
                                        </table>
                                   </div>
                                   <div class="card-inner">
                                        <h6>Cancelled</h6>
                                        <table class="nowrap nk-tb-list nk-tb-ulist table table-bordered" id="membership-table" data-auto-responsive="false">
                                             <thead>
                                                  <tr>
                                                       <th scope="col">Membership Name</th>
                                                       <th scope="col">Quantity</th>
                                                       <th scope="col">Billing</th>
                                                  </tr>
                                             </thead>
                                             <tbody>
                                                  <tr id="starter_cancel" class='clickable-row' data-status='cancelled' data-member="Starter">
                                                       <td>Starter</td>
                                                       @if(isset($cancel_starter))
                                                            <?php $cancel_starter_billing = 0;?>
                                                       <td>{{ count($cancel_starter) }}</td>
                                                            @foreach($cancel_starter as $cancel_start)
                                                                 <?php 
                                                                      $cancel_starter_billing += $cancel_start->renewal_rate; 
                                                                 ?>
                                                            @endforeach
                                                            
                                                       <td>${{ number_format($cancel_starter_billing)?? '' }}</td>
                                                       @endif
                                                  </tr>
                                                  <tr id="club_cancel" class='clickable-row' data-status='cancelled' data-member="Club">
                                                       <td>Club</td>
                                                       @if(isset($cancel_club))
                                                            <?php $cancel_club_billing = 0;?>
                                                       <td>{{ count($cancel_club) }}</td>
                                                            @foreach($cancel_club as $cancel_clu)
                                                                 <?php 
                                                                      $cancel_club_billing += $cancel_clu->renewal_rate; 
                                                                 ?>
                                                            @endforeach
                                                       <td>${{ number_format($cancel_club_billing) ?? '' }}</td>
                                                       @endif
                                                  </tr>
                                                  <tr id="gold_cancel" class='clickable-row' data-status='cancelled' data-member="Gold">
                                                       <td>Gold</td>
                                                       @if(isset($cancel_gold))
                                                       <?php $cancel_gold_billing = 0;?>
                                                       <td>{{ count($cancel_gold) }}</td>
                                                            @foreach($cancel_gold as $cancel_gol)
                                                                 <?php 
                                                                      $cancel_gold_billing += $cancel_gol->renewal_rate; 
                                                                 ?>
                                                            @endforeach
                                                       <td>${{ number_format($cancel_gold_billing) ?? '' }}</td>
                                                       @endif
                                                  </tr>
                                                  <tr id="vip_cancel" class='clickable-row' data-status='cancelled' data-member="Vip">
                                                       <td>VIP</td>
                                                       @if(isset($cancel_vip))
                                                       <?php $cancel_vip_billing = 0;?>
                                                       <td>{{ count($cancel_vip) }}</td>
                                                            @foreach($cancel_vip as $cancel_v)
                                                                 <?php 
                                                                      $cancel_vip_billing += $cancel_v->renewal_rate; 
                                                                 ?>
                                                            @endforeach
                                                       <td>${{ number_format($cancel_vip_billing) ?? '' }}</td>
                                                       @endif
                                                  </tr>
                                                  
                                                  <tr id="total_cancel">
                                                  @php 
                                                       $cancel_billing_total = $cancel_starter_billing + $cancel_club_billing + $cancel_gold_billing + $cancel_vip_billing;
                                                  @endphp
                                                       <th>Totals</th>
                                                       <th>{{ $cancel_total ?? '' }}</th>
                                                       <th>${{ number_format($cancel_billing_total) ?? '' }}</th>
                                                  </tr>
                                             </tbody>
                                        </table>
                                   </div>
                              </div>
                         </div><!-- .card-preview -->
                    </div> <!-- nk-block -->
               </div><!-- .components-preview -->
          </div>
     </div>
</div>

@section('js')

<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

@endsection

<script>
     $(document).ready(function(){
          $('#location').on('change',function(){
               if($(this).val() !== undefined && $(this).val() !== '' && $(this).val() !== null){
                    var data = {
                         id: $(this).val(),
                         _token: "{{ csrf_token() }}"
                    }

                    $.ajax({
                         url: "{{ url('admin-dashboard/memberships/locations') }}",
                         type: "POST",
                         data: data,
                         dataType: "json",
                         success: function(response){
                              if(response.status === 200){
                                   
                                   if(response.active !== null){
                                        var starter = response.active[0];
                                        var club = response.active[1];
                                        var gold = response.active[2];
                                        var vip = response.active[3];
                                        var active_total = response.active[4];

                                        if(starter){
                                             var starter_quantity = starter.length;
                                             var starter_billing = 0;
                                             $.each(starter,function(key,val){
                                                  starter_billing += parseFloat(val.renewal_rate);
                                             });

                                             var starter_row = `<td>Starter</td><td>${starter_quantity}</td><td>$${new Intl.NumberFormat('en-IN').format(starter_billing)}</td>`;
                                             $('#starter').html(starter_row);
                                        }

                                        if(club){
                                             var club_quantity = club.length;
                                             var club_billing = 0;
                                             $.each(club,function(key,val){
                                                  club_billing += parseFloat(val.renewal_rate);
                                             });
                                             var club_row = `<td>Club</td><td>${club_quantity}</td><td>$${new Intl.NumberFormat('en-IN').format(club_billing)}</td>`;
                                             $('#club').html(club_row);
                                        }
     

                                        if(gold){
                                             var gold_quantity = gold.length;
                                             var gold_billing = 0;
                                             $.each(gold,function(key,val){
                                                  gold_billing += parseFloat(val.renewal_rate);
                                             });
                                             var gold_row = `<td>Gold</td><td>${gold_quantity}</td><td>$${new Intl.NumberFormat('en-IN').format(gold_billing)}</td>`;
                                             $('#gold').html(gold_row);
                                        }

                                        if(vip){
                                             var vip_quantity = vip.length;
                                             var vip_billing = 0;
                                             $.each(vip,function(key,val){
                                                  vip_billing += parseFloat(val.renewal_rate);
                                                  
                                             });
                                             var vip_row = `<td>VIP</td><td>${vip_quantity}</td><td>$${new Intl.NumberFormat('en-IN').format(vip_billing)}</td>`;
                                             $('#vip').html(vip_row);
                                        }
                                        var total_quantity = starter_quantity + club_quantity + gold_quantity + vip_quantity;
                                        var billing_total = starter_billing + club_billing + gold_billing + vip_billing;
                                        var total_row = `<th>Totals</th><th>${total_quantity}</th><th>$${new Intl.NumberFormat('en-IN').format(billing_total)}</th>`;
                                        $('#totals').html(total_row);
                                   }

                                   if(response.pending !== null){
                                        var pending_starter = response.pending[0];
                                        var pending_club = response.pending[1];
                                        var pending_gold = response.pending[2];
                                        var pending_vip = response.pending[3];
                                        var pending_total = response.pending[4];
               
                                        if(pending_starter){
                                             var pending_starter_quantity = pending_starter.length;
                                             var pending_starter_billing = 0;
                                             $.each(pending_starter,function(key,val){
                                                  pending_starter_billing += parseFloat(val.renewal_rate);
                                             });
                                             var pending_starter_row = `<td>Starter</td><td>${pending_starter_quantity}</td><td>$${new Intl.NumberFormat('en-IN').format(pending_starter_billing)}</td>`;
                                             $('#starter_pending').html(pending_starter_row);
                                        }

                                        if(pending_club){
                                             var pending_club_quantity = pending_club.length;
                                             var pending_club_billing = 0;
                                             $.each(pending_club,function(key,val){
                                                  pending_club_billing += parseFloat(val.renewal_rate);
                                             });
                                             var pending_club_row = `<td>Club</td><td>${pending_club_quantity}</td><td>$${new Intl.NumberFormat('en-IN').format(pending_club_billing)}</td>`;
                                             $('#club_pending').html(pending_club_row);
                                        }

                                        if(pending_gold){
                                             var pending_gold_quantity = pending_gold.length;
                                             var pending_gold_billing = 0;
                                             $.each(pending_gold,function(key,val){
                                                  pending_gold_billing += parseFloat(val.renewal_rate);
                                             });
                                             var pending_gold_row = `<td>Gold</td><td>${pending_gold_quantity}</td><td>$${new Intl.NumberFormat('en-IN').format(pending_gold_billing)}</td>`;
                                             $('#gold_pending').html(pending_gold_row);
                                        }

                                        if(pending_vip){
                                             var pending_vip_quantity = pending_vip.length;
                                             var pending_vip_billing = 0;
                                             $.each(pending_vip,function(key,val){
                                                  pending_vip_billing += parseFloat(val.renewal_rate);
                                             });
                                             var pending_vip_row = `<td>VIP</td><td>${pending_vip_quantity}</td><td>$${pending_vip_billing}</td>`;
                                             $('#vip_pending').html(pending_vip_row);
                                        }

                                        var pending_billing_total = pending_starter_billing + pending_club_billing + pending_gold_billing + pending_vip_billing;
                                        var pending_total_row = `<th>Totals</th><th>${pending_total}</th><th>$${new Intl.NumberFormat('en-IN').format(pending_billing_total)}</th>`;
                                        $('#total_pending').html(pending_total_row);

                                   }

                                   if(response.cancel !== null){
                                        var cancel_starter = response.cancel[0];
                                        var cancel_club = response.cancel[1];
                                        var cancel_gold = response.cancel[2];
                                        var cancel_vip = response.cancel[3];
                                        var cancel_total = response.cancel[4];

                                        if(cancel_starter.length >= 0){
                                             var cancel_starter_quantity = cancel_starter.length;
                                             var cancel_starter_billing = 0;
                                             $.each(cancel_starter,function(key,val){
                                                  cancel_starter_billing += parseFloat(val.renewal_rate);
                                             });
                                             var cancel_starter_row = `<td>Starter</td><td>${cancel_starter_quantity}</td><td>$${new Intl.NumberFormat('en-IN').format(cancel_starter_billing)}</td>`;
                                             $('#starter_cancel').html(cancel_starter_row);
                                        }

                                        if(cancel_club){
                                             var cancel_club_quantity = cancel_club.length;
                                             var cancel_club_billing = 0;
                                             $.each(cancel_club,function(key,val){
                                                  cancel_club_billing += parseFloat(val.renewal_rate);
                                             });
                                             var cancel_club_row = `<td>Club</td><td>${cancel_club_quantity}</td><td>$${new Intl.NumberFormat('en-IN').format(cancel_club_billing)}</td>`;
                                             $('#club_cancel').html(cancel_club_row);
                                        }

                                        if(cancel_gold){
                                             var cancel_gold_quantity = cancel_gold.length;
                                             var cancel_gold_billing = 0;
                                             $.each(cancel_gold,function(key,val){
                                                  cancel_gold_billing += parseFloat(val.renewal_rate);
                                             });
                                             var cancel_gold_row = `<td>Gold</td><td>${cancel_gold_quantity}</td><td>$${new Intl.NumberFormat('en-IN').format(cancel_gold_billing)}</td>`;
                                             $('#gold_cancel').html(cancel_gold_row);
                                        }

                                        if(cancel_vip){
                                             var cancel_vip_quantity = cancel_vip.length;
                                             var cancel_vip_billing = 0;
                                             $.each(cancel_vip,function(key,val){
                                                  cancel_vip_billing += parseFloat(val.renewal_rate);
                                             });
                                             var cancel_vip_row = `<td>VIP</td><td>${cancel_vip_quantity}</td><td>$${new Intl.NumberFormat('en-IN').format(cancel_vip_billing)}</td>`;
                                             $('#vip_cancel').html(cancel_vip_row);
                                        }

                                        var cancel_billing_total = cancel_starter_billing + cancel_club_billing + cancel_gold_billing + cancel_vip_billing;
                                        var cancel_total_row = `<th>Totals</th><th>${cancel_total}</th><th>$${new Intl.NumberFormat('en-IN').format(cancel_billing_total)}</th>`;
                                        $('#total_cancel').html(cancel_total_row);
                                   }
                              }
                         }

                    })
               }else{
                    window.location.href = "{{ url('admin-dashboard/memberships') }}";
               }
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

          var start = moment().startOf("day");
          var end = moment().endOf("day");
          filterData(start, end);
    });
</script>

<script>
     $(document).ready(function($) {
          $(".clickable-row").click(function() {
               var status = $(this).data('status');
               var location = $('#location').val();
               var membership_name = $(this).data('member');
               // var date = $('#date-range-picker').val();
               // var dates = date.split(" - ");
               // var startDate = dates[0];
               // var endDate = dates[1];

               var url = `{{ url('admin-dashboard/memberships/status') }}?status=${status} &location=${location} &membership=${membership_name}`;
               window.location.href = url;
          });
     });
</script>

<script>

     function dateFilter(){
          var date = $('#date-range-picker').val();
          var dates = date.split(" - ");
          var startDate = dates[0];
          var endDate = dates[1];
          var location = $('#location').val();

          var data = {
               startDate: startDate,
               endDate: endDate,
               location_id: location,
               _token: "{{ csrf_token() }}"
          }

          $.ajax({
               url: "{{ url('admin-dashboard/memberships/date') }}",
               type: "post",
               data: data,
               dataType: "json",
               success:function(response){
                    if(response.status === 200){
                         if(response.active !== null){
                              var starter = response.active[0];
                              var club = response.active[1];
                              var gold = response.active[2];
                              var vip = response.active[3];
                              var active_total = response.active[4];

                              if(starter){
                                   var starter_quantity = starter.length;
                                   var starter_billing = 0;
                                   $.each(starter,function(key,val){
                                        starter_billing += parseFloat(val.renewal_rate);
                                   });

                                   var starter_row = `<td>Starter</td><td>${starter_quantity}</td><td>$${new Intl.NumberFormat('en-IN').format(starter_billing)}</td>`;
                                   $('#starter').html(starter_row);
                              }

                              if(club){
                                   var club_quantity = club.length;
                                   var club_billing = 0;
                                   $.each(club,function(key,val){
                                        club_billing += parseFloat(val.renewal_rate);
                                   });
                                   var club_row = `<td>Club</td><td>${club_quantity}</td><td>$${new Intl.NumberFormat('en-IN').format(club_billing)}</td>`;
                                   $('#club').html(club_row);
                              }
                              
                              if(gold){
                                   var gold_quantity = gold.length;
                                   var gold_billing = 0;
                                   $.each(gold,function(key,val){
                                        gold_billing += parseFloat(val.renewal_rate);
                                   });
                                   var gold_row = `<td>Gold</td><td>${gold_quantity}</td><td>$${new Intl.NumberFormat('en-IN').format(gold_billing)}</td>`;
                                   $('#gold').html(gold_row);
                              }

                              if(vip){
                                   var vip_quantity = vip.length;
                                   var vip_billing = 0;
                                   $.each(vip,function(key,val){
                                        vip_billing += parseFloat(val.renewal_rate);
                                        
                                   });
                                   var vip_row = `<td>VIP</td><td>${vip_quantity}</td><td>$${new Intl.NumberFormat('en-IN').format(vip_billing)}</td>`;
                                   $('#vip').html(vip_row);
                              }
                              var total_quantity = starter_quantity + club_quantity + gold_quantity + vip_quantity;
                              var billing_total = starter_billing + club_billing + gold_billing + vip_billing;
                              var total_row = `<th>Totals</th><th>${total_quantity}</th><th>$${new Intl.NumberFormat('en-IN').format(billing_total)}</th>`;
                              $('#totals').html(total_row);
                         }

                         if(response.pending !== null){
                              var pending_starter = response.pending[0];
                              var pending_club = response.pending[1];
                              var pending_gold = response.pending[2];
                              var pending_vip = response.pending[3];
                              var pending_total = response.pending[4];
     
                              if(pending_starter){
                                   var pending_starter_quantity = pending_starter.length;
                                   var pending_starter_billing = 0;
                                   $.each(pending_starter,function(key,val){
                                        pending_starter_billing += parseFloat(val.renewal_rate);
                                   });
                                   var pending_starter_row = `<td>Starter</td><td>${pending_starter_quantity}</td><td>$${new Intl.NumberFormat('en-IN').format(pending_starter_billing)}</td>`;
                                   $('#starter_pending').html(pending_starter_row);
                              }

                              if(pending_club){
                                   var pending_club_quantity = pending_club.length;
                                   var pending_club_billing = 0;
                                   $.each(pending_club,function(key,val){
                                        pending_club_billing += parseFloat(val.renewal_rate);
                                   });
                                   var pending_club_row = `<td>Club</td><td>${pending_club_quantity}</td><td>$${new Intl.NumberFormat('en-IN').format(pending_club_billing)}</td>`;
                                   $('#club_pending').html(pending_club_row);
                              }

                              if(pending_gold){
                                   var pending_gold_quantity = pending_gold.length;
                                   var pending_gold_billing = 0;
                                   $.each(pending_gold,function(key,val){
                                        pending_gold_billing += parseFloat(val.renewal_rate);
                                   });
                                   var pending_gold_row = `<td>Gold</td><td>${pending_gold_quantity}</td><td>$${new Intl.NumberFormat('en-IN').format(pending_gold_billing)}</td>`;
                                   $('#gold_pending').html(pending_gold_row);
                              }

                              if(pending_vip){
                                   var pending_vip_quantity = pending_vip.length;
                                   var pending_vip_billing = 0;
                                   $.each(pending_vip,function(key,val){
                                        pending_vip_billing += parseFloat(val.renewal_rate);
                                   });
                                   var pending_vip_row = `<td>VIP</td><td>${pending_vip_quantity}</td><td>$${pending_vip_billing}</td>`;
                                   $('#vip_pending').html(pending_vip_row);
                              }

                              var pending_billing_total = pending_starter_billing + pending_club_billing + pending_gold_billing + pending_vip_billing;
                              var pending_total_row = `<th>Totals</th><th>${pending_total}</th><th>$${new Intl.NumberFormat('en-IN').format(pending_billing_total)}</th>`;
                              $('#total_pending').html(pending_total_row);

                         }

                         if(response.cancel !== null){
                              var cancel_starter = response.cancel[0];
                              var cancel_club = response.cancel[1];
                              var cancel_gold = response.cancel[2];
                              var cancel_vip = response.cancel[3];
                              var cancel_total = response.cancel[4];

                              if(cancel_starter.length >= 0){
                                   var cancel_starter_quantity = cancel_starter.length;
                                   var cancel_starter_billing = 0;
                                   $.each(cancel_starter,function(key,val){
                                        cancel_starter_billing += parseFloat(val.renewal_rate);
                                   });
                                   var cancel_starter_row = `<td>Starter</td><td>${cancel_starter_quantity}</td><td>$${new Intl.NumberFormat('en-IN').format(cancel_starter_billing)}</td>`;
                                   $('#starter_cancel').html(cancel_starter_row);
                              }

                              if(cancel_club){
                                   var cancel_club_quantity = cancel_club.length;
                                   var cancel_club_billing = 0;
                                   $.each(cancel_club,function(key,val){
                                        cancel_club_billing += parseFloat(val.renewal_rate);
                                   });
                                   var cancel_club_row = `<td>Club</td><td>${cancel_club_quantity}</td><td>$${new Intl.NumberFormat('en-IN').format(cancel_club_billing)}</td>`;
                                   $('#club_cancel').html(cancel_club_row);
                              }

                              if(cancel_gold){
                                   var cancel_gold_quantity = cancel_gold.length;
                                   var cancel_gold_billing = 0;
                                   $.each(cancel_gold,function(key,val){
                                        cancel_gold_billing += parseFloat(val.renewal_rate);
                                   });
                                   var cancel_gold_row = `<td>Gold</td><td>${cancel_gold_quantity}</td><td>$${new Intl.NumberFormat('en-IN').format(cancel_gold_billing)}</td>`;
                                   $('#gold_cancel').html(cancel_gold_row);
                              }

                              if(cancel_vip){
                                   var cancel_vip_quantity = cancel_vip.length;
                                   var cancel_vip_billing = 0;
                                   $.each(cancel_vip,function(key,val){
                                        cancel_vip_billing += parseFloat(val.renewal_rate);
                                   });
                                   var cancel_vip_row = `<td>VIP</td><td>${cancel_vip_quantity}</td><td>$${new Intl.NumberFormat('en-IN').format(cancel_vip_billing)}</td>`;
                                   $('#vip_cancel').html(cancel_vip_row);
                              }

                              var cancel_billing_total = cancel_starter_billing + cancel_club_billing + cancel_gold_billing + cancel_vip_billing;
                              var cancel_total_row = `<th>Totals</th><th>${cancel_total}</th><th>$${new Intl.NumberFormat('en-IN').format(cancel_billing_total)}</th>`;
                              $('#total_cancel').html(cancel_total_row);
                         }
                    }
               }
          })
     }

</script>

@endsection