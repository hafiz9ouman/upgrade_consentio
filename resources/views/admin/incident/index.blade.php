@extends(($user_type=='1')?('admin.layouts.admin_app'):('admin.client.client_app'))
@section( 'content' )
@if($user_type !='1')
<style>
    .tile{
        margin-left: 15px;
    }
    .Critical{
      background-color: red;
    }
    .Low{
      background-color: #0CC673;

    }
    .High{
      background-color: #FFC100;

    }
    .Medium{
      background-color: yellow;

    }
</style>
@endif
@if (session('alert'))
    <div class="alert alert-danger">
        {{ session('alert') }}
    </div>
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
@if($user_type=='1')
{{-- <div class="app-title">

	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i>
		</li>
		<li class="breadcrumb-item"><a href="{{url('/incident')}}">{{ __('Incident Register') }} </a>
		</li>
	</ul>
</div> --}}
@endif

<div class="session" style="margin-bottom: 20px">
@if(Session::has('error'))
      <div class="alert alert-danger">
      <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    {{ Session::get('error') }}
    </div>
    @endif
    @if(Session::has('success'))
      <div class="alert alert-success">
      <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    {{ Session::get('success') }}
  </div>
    @endif
     @if(Session::has('alert'))
      <div class="alert alert-danger">
      <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    {{ Session::get('alert') }}
    </div>
    @endif 
</div>

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
                        <div class="add_more_tbl">
                            <a href="{{url('add_inccident')}}" type="button" class="btn rounded_button"><i class='bx bx-plus mr-1' ></i> {{ __('ADD INCIDENT') }}</a>
                        </div>
                    </div>
                </div>
                <div class="main_table_redisign">
                    {{-- <div class="table_breadcrumb">
                        <h3>INCIDENT REGISTER</h3>
                    </div> --}}
                    @section('page_title')
                    {{ __('INCIDENT REGISTER') }}
                    @endsection
                    <div class="over_main_div">
                    <table class="table table-striped text-center">
                      <thead>
                        <tr>
                          <th scope="col">{{ __('INCIDENT') }} <br> {{ __('NAME') }}</th>
                          <th scope="col">{{ __('TYPE') }}</th>
                          <th scope="col">{{ __('ORGANIZATION') }}</th>
                          <th scope="col">{{ __('ASSIGNEE') }}</th>
                          <th scope="col">{{ __('ROOT CAUSE') }}</th>
                          <th scope="col">{{ __('DATE') }} <br> {{ __('DISCOVERED') }}</th>
                          <th scope="col">{{ __('DEADLINE') }} <br> DATE</th>
                          <th scope="col">{{ __('STATUS') }}</th>
                          <th scope="col">{{ __('SEVERITY') }}</th>
                          <th scope="col">{{ __('DATE') }}</th>
                          <th scope="col">{{ __('ACTIONS') }}</th>
                        </tr>
                      </thead>
                      <tbody>
                         @if($user_type=='1')
        
            @foreach($incident_register as $row)
            <tr>
                            <td>{{$row->name}}</td>
                <td><?php $incident  = DB::table('incident_type')->where('id',$row->incident_type)->first();?> {{ __($incident->name)}}</td>
                <td><?php $org  = DB::table('users')->where('id',$row->organization_id)->first();?> {{ $org->company}}</td>
                            <td>{{$row->assignee}}</td>
                            <!-- <td>{{$row->root_cause}}</td> -->
                            <td>
                            <td>
                              {{-- <a href="" class="btn btn-primary btn-sm nowrap_btn"  data-toggle="modal" data-val="{{$row->root_cause}}"  data-target='#practice_modal' ><i class="fa fa-eye mr-2"></i>See Detail</a> --}}
                              <button type="button" data-toggle="modal" data-val="{{$row->root_cause}}"  data-target='#practice_modal' class="btn seet_detail_btn"><i class='bx bx-show-alt'></i> {{ __('See Detail') }}</button>

                            </td>
                                <!-- {{$row->date_discovered}} -->

                            {{date('d', strtotime($row->date_discovered))}} {{date(' F', strtotime($row->date_discovered))}} {{date('Y  ', strtotime($row->date_discovered))}}
                            </td>
                            <td>
                                 {{date('d', strtotime($row->deadline_date))}} {{date(' F', strtotime($row->deadline_date))}} {{date('Y  ', strtotime($row->deadline_date))}}
                            </td>
                            <td>{{$row->incident_status}}</td>
                            <td class="{{$row->incident_severity}}"><strong>{{$row->incident_severity}}</strong></td>
                            <td>
                                <!-- {{$row->created_at}} -->
                                 {{date('d', strtotime($row->created_at))}} {{date(' F', strtotime($row->created_at))}} {{date('Y  h:i ', strtotime($row->created_at))}}
                            </td>
              <td class="text-center">
                <div class="action_icons">
                                   <a href="{{url('edit_incident/' . $row->id)}}"><i class='bx bx-edit'></i></a>
                                   <a href="javascript:void(0)" class="removePartner" data-id="{{$row->id}}"><i class='bx bxs-trash' ></i></a>
                                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
                    @else
                    

                        @foreach($incident_front as $row)
                        <tr>
                            <td>{{ $row->name}}</td>
                            <td><?php $incident  = DB::table('incident_type')->where('id',$row->incident_type)->first();?>
                              
                             @if(session('locale') == 'fr' && isset($incident->name_fr) && $incident->name_fr != null)
                                    {{ $incident->name_fr}}
                                @else
                                {{ __($incident->name)}}
                                @endif
                             
                             </td>
                            <td><?php $org  = DB::table('users')->where('id',$row->organization_id)->first();?> {{ $org->company}}</td>
                            <td>{{$row->assignee}}</td>
                            <!-- <td>{{$row->root_cause}}</td> -->
                            <td>
                              {{-- <a href="" class="btn btn-primary btn-sm nowrap_btn"  data-toggle="modal" data-val="{{$row->root_cause}}"  data-target='#practice_modal' ><i class="fa fa-eye mr-2"></i>See Detail</a></td> --}}
                             <button type="button" data-toggle="modal" data-val="{{$row->root_cause}}"  data-target='#practice_modal' class="btn seet_detail_btn"><i class='bx bx-show-alt'></i> {{ __('See Detail') }}</button>

                            <td>
                                <!-- {{$row->date_discovered}} -->

                            {{date('d', strtotime($row->date_discovered))}} {{date(' F', strtotime($row->date_discovered))}} {{date('Y  ', strtotime($row->date_discovered))}}
                            </td>
                            <td>
                                 {{date('d', strtotime($row->deadline_date))}} {{date(' F', strtotime($row->deadline_date))}} {{date('Y  ', strtotime($row->deadline_date))}}
                            </td>
                            <td>
                          {{ __($row->incident_status)}}
                            </td>
                             <td class="{{$row->incident_severity}}">
                           <strong>{{ __($row->incident_severity)}}</strong>
                             </td>
                            <td>{{date('d', strtotime($row->created_at))}} {{date(' F', strtotime($row->created_at))}} {{date('Y  h:i ', strtotime($row->created_at))}}</td>
                            <td class="text-center">
                                {{-- <div class="actions-btns dule-btns">
                                    <a href="{{url('edit_incident/' . $row->id)}}" class="btn btn-sm btn-info"><i class="fa fa-edit"></i></a>
                                    <a href="javascript:void(0)" data-id="{{$row->id}}" class="btn btn-sm btn-danger removePartner"><i class="fa fa-trash"></i></a>
                                </div> --}}

                                <div class="action_icons">
                                   <a href="{{url('edit_incident/' . $row->id)}}"><i class='bx bx-edit'></i></a>
                                   <a href="javascript:void(0)" class="removePartner" data-id="{{$row->id}}"><i class='bx bxs-trash' ></i></a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    
                    @endif
                        
                        

                      </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </section>


<div class="modal fade" id="practice_modal" tabindex="-1" role="dialog" aria-labelledby="my-modal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">{{ __('Root Cause') }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close')}}</button>
      </div>
    </div>
  </div>
</div>
<script>
    $('#practice_modal').on('show.bs.modal', function (event) {
  var myVal = $(event.relatedTarget).data('val');
  $(this).find(".modal-body").html(myVal);
});
</script>


<script type="text/javascript">



    $(document).ready(function(){
        
        

        $(document).ready(function() {

            $('#orgs').DataTable( {

                "order": [[ 10, "desc" ]]

            } );

        } );
        
    	$( "body" ).on( "click", ".removePartner", function () {
    		var task_id = $( this ).attr( "data-id" );
    		var form_data = {
    			id: task_id
    		};
    		swal( {
    				title: "{!! __('Delete Incident') !!}",
    				text: "{!! __('Incident Deleted Successfully!') !!}",
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
    					url: '<?php echo url("incident/delete"); ?>',
    					data: form_data,
    					success: function ( msg ) {
    						swal( "@lang('Incidet.successfully delete')", '', 'success' )
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