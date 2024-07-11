@extends('admin.layouts.admin_app')
@section('page_title')
    {{ __('Group Lists') }}
@endsection
@section('content')
<div class="app-title">
    <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item">{{ __('Import') }}</li>
    </ul>
</div>
<a href="" id="delete_item"></a>
@if(Session::has('msg'))
    <p class="alert alert-info">{{ Session::get('msg') }}</p>
@endif
@if(Session::has('alert'))
    <p class="alert alert-danger">{{ Session::get('alert') }}</p>
@endif
@if(Session::has('message'))
    <p class="alert alert-info">{{ Session::get('message') }}</p>
@endif
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@php
    $form_count = DB::table('forms')->where('type', 'assessment')->count();
@endphp
<div class="row bg-white py-3">  
    <div class="col-md-12 d-flex justify-content-between align-items-center">
        <!-- <h3>{{ __('Question Groups') }}</h3> -->
        <h3>Import Forms & Question Groups</h3>
        <a href="{{ route('all_export') }}" class="btn btn-sm btn-secondary pull-right" @if($form_count <= 0) style="pointer-events: none;cursor: default;margin-right: 10px;opacity: 1;color:linen;" @else style="margin-right: 10px;" @endif><i class="fa fa-download" aria-hidden="true"></i>Export All</a>
    </div>  
    <div class="col-md-12 d-flex justify-content-end align-items-center">
        <small class="text-danger">Click to Export all Forms and Question Groups</small>
    </div> 
    <div class="col-md-12">
        <form method="POST" action="{{url('/Forms/import_file')}}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="import_file"></label>
                <input type="file" class="form-control" name="import_file" required>
            </div>
            <div class="">
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