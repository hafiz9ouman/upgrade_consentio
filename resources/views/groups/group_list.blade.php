@extends('admin.layouts.admin_app')
@section('page_title')
    {{ __('Group Lists') }}
@endsection
@section('content')
<div class="app-title">
    <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{route('groups_list')}}">{{ __('Question Groups List') }}</a></li>
    </ul>
</div>
<a href="" id="delete_item"></a>
<div class="row bg-white p-4">  
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h3>{{ __('Question Groups') }}</h3>
        <a href="{{ route('group_add') }}"  class="btn btn-sm btn-primary"><i class="fa fa-plus mr-0"></i> {{ __('Add Group') }}</a>
    </div>  
    <div class="col-12 p-5 " style="overflow-y: auto;">
        <table class="table" id="group-table" style="min-width:720px">
            <thead class="back_blue">
                <tr>
                    <th class="px-0 pl-2">#</th>

                    <th class="px-0">{{ __('Group Name En') }}</th>

                    <th class="px-0">{{ __('Group Name Fr') }}</th>

                    <th class="px-0">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @if(!count($groups) > 0)
                <tr><td colspan="4" style="text-align: center; vertical-align: middle;"> No Data Found  </td></tr>
                @else
                    @foreach($groups as $group)
                    <tr>
                        <td> {{ $loop->iteration }} </td>
                        <td> {{ $group->group_name }} </td>
                        <td> {{ $group->group_name_fr }} </td>
                        <td>
                            <a href="{{ route('group_edit',$group->id) }}"  class="btn btn-sm btn-primary" title="Edit Group"><i class="fa fa-edit mr-0"></i></a>
                            <a href="{{ route('group_add_quetion', $group->id) }}"  class="btn btn-sm btn-primary"> Add / Edit Questions</a>
                            <a href="javascript:" onclick="submitDuplicate('/group/duplicate/{{$group->id}}')"  class="btn btn-sm btn-primary" title="Duplicate Group"><i> Duplicate </i></a> 
                            <a href="javascript:" onclick="submitDelete('/group/delete/{{$group->id}}')"        class="btn btn-sm btn-danger" title="Delete Group"><i class="fa fa-times mr-0"></i></a> 
                        </td>
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
            $('#group-table').DataTable();
        });

        function submitDelete(url){
            swal({
                title:"Are you sure?",
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
        function submitDuplicate(url){
            swal({
                title:"Are you want to duplicate?",
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