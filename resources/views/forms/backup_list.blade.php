@extends('admin.layouts.admin_app')
@section('page_title')
    {{ __('Group Lists') }}
@endsection
@section('content')
<div class="app-title">
    <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{url('Forms/AdminFormsList')}}">{{ __('Manage Assessment Forms') }}</a></li>
    <li class="breadcrumb-item">{{ __('Backup List') }}</li>
    </ul>
</div>
<a href="" id="delete_item"></a>
@if(Session::has('msg'))
    <p class="alert alert-info">{{ Session::get('msg') }}</p>
@endif
@if(Session::has('alert'))
    <p class="alert alert-danger">{{ Session::get('alert') }}</p>
@endif
<div class="row bg-white py-3">  
    <div class="col-md-12 d-flex justify-content-between align-items-center">
        <!-- <h3>{{ __('Question Groups') }}</h3> -->
        <h3>Import Forms</h3>
       
    </div>  
    <div class="col-md-12">
        <form method="POST" action="{{url('/Forms/import_file')}}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="import_file"></label>
                <input type="file" class="form-control" name="import_file" required>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary">
            </div>
        </form>
    </div>

</div>

@endsection
@push('scripts')
    <script>
        $(function(){
            $('#group-table').DataTable({
                order: [],
                "scrollX": true,
			    "autoWidth": false
            });
        });

        function restore(url){
            swal({
                title:"Are you sure you want to Restore Form?",
                type: "info",
                showCancelButton: true,
                confirmButtonColor: "#05DD6B",
                confirmButtonText: "YES",
                cancelButtonText: "NO",
                closeOnConfirm: false
            }, function(val){
                if (val){
                    $('#delete_item').attr('href', url);
                    document.getElementById('delete_item').click();
                }
                swal.close();
            })
        }
    </script>
@endpush