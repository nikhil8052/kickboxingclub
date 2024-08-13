@extends('admin_layout/master')
@section('content')

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
               <!-- <div class="search-box">
                    <div class="form-group">
                         <input type="text" placeholder="Search here" id="search"><i class="fa fa-search" aria-hidden="true"></i>
                    </div>
               </div> -->
          </div>
          <div class="nk-content-inner">
               <div class="nk-content-body">
                    <div class="nk-block">
                         <div class="nk-block">
                              <div class="card card-bordered card-preview">
                                   <div class="card-inner">
                                        <table class="datatable-init nowrap nk-tb-list nk-tb-ulist table table-bordered" id="membership-table" data-auto-responsive="false">
                                             <thead>
                                                  <tr>
                                                       <th scope="col">Name</th>
                                                       <th scope="col">Email Address</th>
                                                       <th scope="col">Phone Number</th>
                                                  </tr>
                                             </thead>
                                             <tbody>
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

<script>
     function getUserByStatus(status){
          const queryString = window.location.search;
          const urlParams = new URLSearchParams(queryString);
          const location = urlParams.get('location');
          const membership = urlParams.get('membership');
          // const start = urlParams.get('start');
          // const end = urlParams.get('end');

          var url = `{{ url('admin-dashboard/memberships/status') }}?status=${status} &location=${location} &membership=${membership}`;
          window.location.href = url;
     }

     $(document).ready(function(){
          const queryString = window.location.search;
          const urlParams = new URLSearchParams(queryString);
          const status = urlParams.get('status');
          
          if(status !== '' && status !== null && status !== undefined){
               $('#'+status).addClass('active');
          }
     })
</script>

@endsection