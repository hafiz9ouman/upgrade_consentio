@extends('admin.layouts.admin_app')
@section('page_title')
    {{ __('Group Lists') }}
@endsection
@section('content')
<div class="app-title">
    <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{route('groups_list')}}">{{ __('Question Groups List') }}</a></li>
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
        <h3>Group Backup List</h3>
       
    </div>  
    <div class="col-md-12">
        <table class="table " id="group-table">
            <thead class="back_blue ">
                <tr>
                    <th class="">{{ __('Group Name En') }}</th>
                    <th class="px-0">{{ __('Group Name Fr') }}</th>
                    <th class="px-0">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @if(count($forms_list) == 0)
                    <tr>
                        <td colspan="3" style="text-align: center; vertical-align: middle;">No Data Found</td>
                    </tr>
                @else
                    @foreach($forms_list as $data)
                        <tr>
                            <td>{{ $data->group_name }}</td>
                            <td>{{ $data->group_name_fr }}</td>
                            <td><a class="btn btn-sm btn-success" href="javascript:" onclick="restore('/Group/restore/{{$data->id}}')">Restore Form</a></td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
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