@extends('admin.client.client_app')
@section('content')
<style>
    .row-btn {
        margin-bottom:10px;
        display:flex;
        flex-direction:row;
        justify-content: flex-end;
    }
    .expired {
        color:#d73b3b;
    }
    #forms-list_wrapper {
        white-space: nowrap;
        padding-top: 15px;
    }
    .align_button {
        display: flex;
        justify-content: space-between;
    }
</style>


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
      <div class="main_custom_table">
        <div class="table_filter_section">
          <div class="select_tbl_filter">
            <div class="main_filter_tbl">
              <p>{{ __('Show') }}</p>
              <select>
                <option>10</option>
                <option>20</option>
                <option>30</option>
              </select>
              <p>{{ __('Entries') }}</p>
            </div>
          </div>
        </div>
        <div class="main_table_redisign">

          @section('page_title')
          {{-- <div class="table_breadcrumb"> --}}
            {{-- <h3> --}}
            {{ __('ORGANISATION USERS') }} {{ __('(EXCLUDING ADMINS)') }}
            {{-- </h3> --}}
          @endsection
          {{-- <div class="table_breadcrumb">
            <h3>GENERATED FORMS</h3>
          </div> --}}

          <div class="over_main_div">
            <a href="{{url('add_user')}}" class="btn btn-sm btn-primary pull-right cust_color" style="margin-top: 15px; float: right; margin-right: 10px "><i class="fa fa-plus" ></i> {{ __('Add Organization User') }}</a>
            <table class="table table-striped text-center paginated" >
            <thead>
            <tr style = "text-transform:uppercase;">
                         <th>{{ __('Name') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Image') }}</th>
                            <th>{{ __('User Type') }}</th>
                            <th>{{ __('Added By') }}</th>
                            {{-- <th>Super User status</th> --}}
                            <th>{{ __('Permissions') }}</th>                          

                            <th width="130" class="text-center">{{ __('Actions') }}</th>
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
                                    <a href="{{url('edit_user/' . $row->id)}}" class="btn btn-sm btn-info"><i class="fa fa-edit"></i></a>
                                    <a href="javascript:void(0)" data-id="{{$row->id}}" class="btn btn-sm btn-danger removePartner"><i class="fa fa-trash"></i></a>
                                </div> --}}

                                <div class="action_icons">
                                   <a href="{{url('edit_user/' . $row->id)}}"><i class="bx bx-edit"></i></a>
                                   <a href="javascript:void(0)" data-id="{{$row->id}}" class="removePartner" data-id="46"><i class="bx bxs-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
        </tbody>
            </table>
            <div class="table_footer">
              <p>{{ __('Showing 1 to 9 of 9 entries')}}</p>
              <div class="table_custom_pagination">
                <p class="active_pagination">1</p>
                <p>2</p>
                <p>3</p>
              </div>
            </div>
        </div>
        </div>
      </div>
    </section>



<!-- </div> -->

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