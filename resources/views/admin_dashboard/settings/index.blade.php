@extends('admin_layout/master')
@section('content')

<div class="nk-content ">
     <div class="container-fluid">
          <div class="nk-content-inner">
               <div class="nk-content-body">
                    <div class="nk-block">
                         <h6 class="nk-block-title page-title">Marianatek Credentials</h4>
                         <div class="card card-bordered">
                              <div class="card-inner col-md-6">
                                   <form id="myform" action="{{ url('admin-dashboard/update/credentials') }}" method="post">
                                        @csrf
                                        <div class="form-group">
                                             <label class="form-label" for="username">Username</label>
                                             <div class="form-control-wrap">
                                                  <input type="text" class="form-control" name="username" id="username" value="{{ $user->email ?? '' }}">
                                             </div>
                                            <span class="text text-danger" id="email-error" style="display:none;">This field is required</span>
                                        </div>
                                        <div class="form-group">
                                             <label class="form-label" for="password">Password</label>
                                             <div class="form-control-wrap">
                                                  <input type="password" class="form-control" name="password" id="password" placeholder="*******">
                                             </div>
                                            <span class="text text-danger" id="password-error" style="display:none;">This field is required</span>
                                        </div>
                                        <div class="form-group">
                                             <input type="submit" class="btn btn-dark" value="Update">
                                        </div>
                                   </form>
                              </div>
                         </div>
                         <div class="card card-bordered card-full">
                              <div class="card-inner">
                                   <form id="trails-form">
                                   <div class="row">
                                        <div class="col-md-6">
                                             <div class="card-head">
                                                  <h6 class="title">Trials Sold INCLUDE Filter</h6>
                                             </div>
                                             <div class="form-control-wrap">
                                                  <select class="form-select js-select2 select2-hidden-accessible" name="include[]" id="include" multiple="" data-placeholder="Select Multiple options" data-select2-id="10" tabindex="-1" aria-hidden="true">
                                                  @if(isset($membershipTrails) && $membershipTrails != null)
                                                       @foreach ($membershipTrails as $trials)
                                                            <option selected value="{{ $trials->id }}">{{ $trials->name ?? '' }}</option>
                                                       @endforeach
                                                  @endif
                                                  </select>
                                             </div>
                                        </div>
                                        <div class="col-md-6">
                                             <div class="card-head">
                                                  <h6 class="title">Daily Active Members EXCLUDE Filter</h6>
                                             </div>
                                             <div class="form-control-wrap">
                                                  <select class="form-select js-select2 select2-hidden-accessible" name="exclude[]" id="exclude" multiple="" data-placeholder="Select Multiple options" data-select2-id="9" tabindex="-1" aria-hidden="true">
                                                  @if(isset($activeMembers) && $activeMembers != null)
                                                       @foreach ($activeMembers as $members)
                                                            <option selected value="{{ $members->id }}">{{ $members->name ?? '' }}</option>
                                                       @endforeach
                                                  @endif
                                                  </select>
                                             </div>
                                        </div>
                                   </div>
                              </div>
                         </div>
                    </div>
               </div>
          </div>
     </div>
</div>


<script>
     // $(document).ready(function(){
     //      $('#include').on('change',function(){
              
     //      });
     // })

</script>

@endsection