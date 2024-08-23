@extends('admin_layout/master')
@section('content')

<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block">
                    <div class="card card-bordered card-preview">
                        <div class="card-inner">
                            <table class="datatable-init nowrap nk-tb-list nk-tb-ulist table table-tranx" data-auto-responsive="false">
                                <thead>
                                    <tr class="nk-tb-item nk-tb-head">
                                        <th class="nk-tb-col"><span class="sub-text"></span>Name</th>
                                        <th class="nk-tb-col"><span class="sub-text"></span>Timezone</th>
                                        <th class="nk-tb-col"><span class="sub-text"></span>Email</th>
                                        <th class="nk-tb-col"><span class="sub-text"></span>Address</th>
                                        <th class="nk-tb-col"><span class="sub-text"></span>City</th>
                                        <th class="nk-tb-col"><span class="sub-text"></span>Latitude</th>
                                        <th class="nk-tb-col"><span class="sub-text"></span>Longitude</th>
                                        <th class="nk-tb-col"><span class="sub-text"></span>Language</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($locations) && $locations != null)
                                    @foreach($locations as $location)
                                    <tr>
                                        <td>{{ $location->name ?? '' }}</td>
                                        <td>{{ $location->timezone ?? '' }}</td>
                                        <td>{{ $location->email_address ?? '' }}</td>
                                        <td>{{ $location->address_line1 ?? '' }}</td>
                                        <td>{{ $location->city ?? '' }}</td>
                                        <td>{{ $location->latitude ?? '' }}</td>
                                        <td>{{ $location->longitude ?? '' }}</td>
                                        <td>{{ $location->primary_language ?? '' }}</td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div><!-- .card-preview -->
                    </div> <!-- nk-block -->
                </div><!-- .components-preview -->
            </div>
        </div>
    </div>
</div>

<script>
    //  $(document).ready(function(){
    //       $('#location-table').DataTable({
    //             "autoWidth": false,
    //             "fixedHeader": true,
    //             "ajax":{
    //                 "url":"{{ url('get/locations') }}",
    //                 "dataSrc":"data",
    //             },
    //             "columns": [
    //                 { "data": "attributes.name" },
    //                 { "data": "attributes.timezone" },
    //                 { "data": "attributes.email_address" },
    //                 { "data": "attributes.address_line1" },
    //                 { "data": "attributes.city" },
    //                 { "data": "attributes.latitude" },
    //                 { "data": "attributes.longitude" },
    //                 { "data": "attributes.primary_language" }
    //             ],
    //             "columnDefs": [
    //                 { "width": "5%", "targets": 0 },
    //                 { "width": "14%", "targets": 1 },
    //                 { "width": "30%", "targets": 2 },
    //                 { "width": "21%", "targets": 3 },
    //                 { "width": "7%", "targets": 4 },
    //                 { "width": "8%", "targets": 5 },
    //                 { "width": "8%", "targets": 6 },
    //                 { "width": "7%", "targets": 7 }
    //             ],
    //             // "scroller": true, 
    //             // "scrollY": "400px", 
    //             // "scrollCollapse": true,
    //         })
    //     })
</script>

@endsection