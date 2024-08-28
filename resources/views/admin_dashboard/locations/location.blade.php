@extends('admin_layout/master')
@section('content')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />
@endsection

<div class="nk-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-end p-2">
            <div class="nk-block-head-content">
                <div class="mbsc-form-group">
                    <button class="btn btn-dark" id="export-button"><i class="fa fa-download"></i> Export</button>
                </div>
            </div>
        </div> 

        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block">
                    <div class="card card-bordered card-preview">
                        <div class="card-inner">
                            <table id="location-table" class="nowrap nk-tb-list nk-tb-ulist table table-tranx dataTable" data-auto-responsive="false">
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
    $(document).ready(function(){
        $('#overlay').show();
        
        $('#location-table').DataTable({
            processing: true,
            serverSide: false, 
            ajax: {
                url: "{{ url('admin-dashboard/get/locations') }}", 
                type: 'GET',
                dataSrc: '',
                beforeSend: function() {
                    $('#overlay').show();
                },
                complete: function() {
                    $('#overlay').hide();
                } 
            },
            columns: [
                { data: 'name' },
                { data: 'timezone' },
                { data: 'email_address' },
                { data: 'address_line1' },
                { data: 'city' },
                { data: 'latitude' },
                { data: 'longitude' },
                { data: 'primary_language' }                
            ]
        });
    })
</script>

<script>
    $(document).ready(function () {
        $('#export-button').on('click', function () {
            var csvContent = '';

            // Fix the selection to use the correct header element
            var headers = [];
            $('#location-table thead tr th').each(function () {
                var headerText = $(this).text().trim();
                if (headerText !== '') { 
                    headers.push(headerText.replace(/,/g, "")); 
                }
            });

            csvContent += headers.join(',') + "\n"; 

            $('#location-table tbody tr').each(function () {
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
                link.setAttribute('download', 'Location_Export.csv');
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        });
    });
</script>

@endsection