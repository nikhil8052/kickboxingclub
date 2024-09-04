@extends('admin_layout.master')
@section('content')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />
@endsection

<div class="nk-content">
     <div class="container-fluid">
          <!-- <div class="d-flex justify-content-end p-2">
               <div class="nk-block-head-content">
                    <div class="mbsc-form-group">
                         <button class="btn btn-dark" id="export-button"><i class="fa fa-download"></i> Export</button>
                    </div>
               </div>
          </div>  -->

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
                                                  <th class="nk-tb-col"><span class="sub-text"></span>Trail</th>
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
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
@endsection

<script>
     $(document).ready(function() {
          var table = $('#trials_purchased').DataTable({
                
               processing: true,
               serverSide: false, 
               ajax: {
                    url: "{{ url('/admin-dashboard/get/trial/purchased') }}", 
                    type: 'GET',
                    dataSrc: '',
                    beforeSend: function() {
                         // $('#overlay').show();
                    },
                    complete: function() {
                         // $('#overlay').hide();
                    } 
               },
               columns: [
                    { data: 'user.full_name', 
                         render: function(data, type, row) {
                              return data ? data : 'unknown';
                         }
                    },
                    { data: 'user.email', 
                         render: function(data, type, row) {
                              return data ? data : 'unknown';
                         }
                    },
                    { data: 'user.phone_number', 
                         render: function(data, type, row) {
                              return data ? data : 'unknown';
                         }
                    },
                    { data: 'membership_name', 
                         render: function(data, type, row) {
                              return data ? data : 'unknown';
                         }
                    },
                    { data: 'purchase_date', 
                         render: function(data, type, row) {
                              return data ? data : 'unknown';
                         }
                    },
               
               ]
          });
     });

</script>

@endsection