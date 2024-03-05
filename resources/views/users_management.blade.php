@extends('admin.client.client_app')
@section('content')



 <link href="{{ url('frontend/css/jquery.mswitch.css')}}"  rel="stylesheet" type="text/css">

 @if (session('alert'))
    <div class="alert alert-danger">
        {{ session('alert') }}
    </div>
@endif 
{{-- <div class="container-fluid">
  <div class="align_button">    
     @if(!isset($all))
    <h3 class="tile-title">User Forms {{app('request')->input('ext_user_only')?'(External Users Only)':'(Internal and External Users)'}}</h3>
    @endif
    
    @if(!isset($all))
    <div class="row-btn">
        <button class="btn btn-primary" data-toggle="modal" data-target="#myModal">Send Link to External Users</button>
    </div>
    @endif
    </div>
</div>  --}}

    
    <section class="assets_list">
        <div class="row">
            <div class="col">
                <a href="{{url('add_user')}}" class="button pull-left cust_color mb-2"><i class="fa fa-plus" ></i> {{ __('Add Organization User') }}</a>
            </div>
        </div>
      <div class="row">
        <div class="col-12">
            <div class="card">

          @section('page_title')
          {{-- <div class="table_breadcrumb"> --}}
            {{-- <h3> --}}
            {{ __('ORGANISATION USERS') }}
            {{-- </h3> --}}
          @endsection
          {{-- <div class="table_breadcrumb">
            <h3>GENERATED FORMS</h3>
          </div> --}}

          <div class="card-table">
            <table id="datatable" class="table table-striped text-center" >
            <thead>
            <tr style = "text-transform:uppercase;">
                         <th style="vertical-align: middle;">{{ __('Name') }}</th>
                            <th style="vertical-align: middle;">{{ __('Email') }}</th>
                            <th style="vertical-align: middle;">{{ __('Image') }}</th>
                            <th style="vertical-align: middle;">{{ __('User Type') }}</th>
                            <th style="vertical-align: middle;">{{ __('Added By') }}</th>
                            {{-- <th style="vertical-align: middle;">Super User status</th> --}}
                            <th style="vertical-align: middle;">{{ __('Permissions') }}</th>                          

                            <th style="vertical-align: middle;" width="130" class="text-center">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>

            @foreach($user as $row)
                        <tr>                            
                            <td> {{$row->name}} </td>                           
                            <td>{{$row->email}}</td>
                            @if($row->image_name=="")
                            <td><img  src="{{url('dummy.jpg')}}" style="border-radius: 9px; height: 50px; " /> </td>
                            @else
                            <td><img src="<?php echo url("public/img2/$row->image_name");?>" style="border-radius: 9px;height: 50px; "> </td>
                            @endif
                            @if($row->user_type == 0)
                            <td> User </td>
                            @else($row->user_type == 1)
                            <td> Super User </td>                           
                            @endif
                            <td>{{ $row->added_by }}</td>
                            
                            {{-- <td>
                                @if($row->user_type==1)
                                <!-- <div class="badge badge-rounded bg-green">Active</div>  -->
                                <a href="javascript:void(0)" data-id="{{$row->id}}" data-status="{{$row->user_type}}" id="change_status" class="btn btn-sm btn-success"> @lang('users.active')</a>
                                @else
                                <a href="javascript:void(0)" data-id="{{$row->id}}" data-status="{{$row->user_type}}" id="change_status" class="btn btn-sm btn-danger"> @lang('users.inactive')</a>
                                <!-- <div class="badge badge-rounded bg-red">Inactive</div>  -->
                                @endif
                            </td> --}}

                               <td class="text-center">
                                     
                                     <a href="{{ url('/Orgusers/permissions/'.$row->id)}}" class="btn btn-sm btn-dark"><i class="fa fa-unlock-alt" aria-hidden="true"></i> {{ __('Change Permissions')}}</a>


                               </td>
                            <td class="text-center">
                                {{-- <div class="actions-btns dule-btns">
                                    <!-- <a href="javascript:void(0)" data-id="{{$row->id}}" data-status="{{$row->status}}" id="change_status" class="btn btn-sm btn-primary"> <i class="fa fa-eye"> </i></a>  -->
                                    <a href="{{url('edit_user/' . $row->id)}}" class="btn btn-sm btn-info"><img src="{{url('assets-new/img/action-edit.png')}}" alt=""></a>
                                    <a href="javascript:void(0)" data-id="{{$row->id}}" class="btn btn-sm btn-danger removePartner"><img src="{{url('assets/img/action-delete.png')}}" alt=""></a>
                                </div> --}}

                                <div class="action_icons">
                                   <a href="{{url('edit_user/' . $row->id)}}"><img src="{{url('assets-new/img/action-edit.png')}}" alt=""></a>
                                   <a href="javascript:void(0)" data-id="{{$row->id}}" class="removePartner" data-id="46"><img src="{{url('assets-new/img/action-delete.png')}}" alt=""></a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
        </tbody>
            </table>
                <!-- <div class="table_footer">
                <p>{{ __('Showing 1 to 9 of 9 entries')}}</p>
                <div class="table_custom_pagination">
                    <p class="active_pagination">1</p>
                    <p>2</p>
                    <p>3</p>
                </div>
                </div> -->
        </div>
        </div>
      </div>
    </section>



<!-- </div> -->
<script>
    $(document).ready(function() {
      $('#datatable').DataTable();
    });
</script>

<script type="text/javascript">
    $(document).ready(function(){
        
        $('#users').DataTable();

        $( "body" ).on( "click", ".removePartner", function () {
            var task_id = $( this ).attr( "data-id" );
            var form_data = {
                id: task_id
            };
            swal( {
                    title: "{!!  __('Delete User') !!}",
                    text: "{!! __('Are you sure you want to delete? All other data will also deleted') !!}",
                    type: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#F79426',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{!! __('Yes') !!}",
                    cancelButtonText: "{!! __('No') !!}",
                    showLoaderOnConfirm: true
                },
                function () {
                    $.ajax( {
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $( 'meta[name="csrf-token"]' ).attr( 'content' )
                        },
                        url: '<?php echo url("delete_user"); ?>',
                        data: form_data,
                        success: function ( msg ) {
                            swal( "{!! __('User Deleted Successfully') !!}", '', 'success' )
                            setTimeout( function () {
                                location.reload();
                            }, 2000 );
                        }
                    } );
                } );
    
        } );
        $( "body" ).on( "click", "#change_status", function () {
            var id = parseInt( $( this ).attr( "data-id" ) );
            var status = parseInt( $( this ).attr( "data-status" ) );
            if ( status == 0 ) {
                var s = 1
            } else if ( status == 1 ) {
                s = 0
            }
            var form_data = {
                id: id,
                status: s
            };
            swal( {
                    title: "@lang('users.change_status')",
                    text: "@lang('users.change_status_msg')",
                    type: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#F79426',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes',
                    showLoaderOnConfirm: true
                },
                function () {
                    $.ajax( {
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $( 'meta[name="csrf-token"]' ).attr( 'content' )
                        },
                        url: '<?php echo url("users/change_status"); ?>',
                        data: form_data,
                        success: function ( msg ) {
                            swal( "@lang('users.success_change')", '', 'success' )
                            setTimeout( function () {
                                location.reload();
                            }, 2000 );
                        }
                    } );
                } );
    
    
        } );        
        
        
    });



</script>

<style>
    .sweet-alert h2 {
        font-size: 1.3rem !important;
    }
    
    .sweet-alert .sa-icon {
        margin: 30px auto 35px !important;
    }
</style>
@endsection