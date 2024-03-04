@extends( 'admin.layouts.admin_app' )
@section( 'content' )

<div class="app-title">

	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i>
		</li>
		<li class="breadcrumb-item"><a href="{{url('/admin')}}">Organization Admins</a>
		</li>
	</ul>
</div>

@if (session('alert'))
    <div class="alert alert-danger">
        {{ session('alert') }}
    </div>
@elseif(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif    


<div class="row">
	<div class="col-md-12">
		<div class="tile">
			<h3 class="tile-title">Organization Admins 			
				<a href="{{url('users/add')}}" class="btn btn-sm btn-primary pull-right cust_color"><i class="fa fa-plus" ></i> Add Organization Administrator</a>
			</h3>
			<div class="table-responsive">
				<table class="table" id="org-users">
					<thead class="back_blue">
						<tr>
							<th>Name</th>
							<th>Email</th>
							<th>Organization</th>
							<th>Status</th>
							<th>Blocked</th>
							<th>User Type</th>
                            <th>Add/Remove Permissions</th>
							<th width="130" class="text-center">Actions</th>
						</tr>
					</thead>
					<tbody>
						@foreach($users as $row)
						
						<tr>
							<td style="text-transform:none;"> {{$row->name}} <?php 
							if($row->is_email_varified==0) echo '<span style="color:green;">(2FA)</span>';
							 ?> </td>
							<td style="text-transform:none;">{{$row->email}}</td>
							<?php $client_id = $row->client_id ?>
							@if($row->company =="")
							<?php $user_company = DB::table('users')->where('id',$client_id)->first() ?>
							<td> <?php if($user_company) echo $user_company->company; ?>  </td>
							@else
							<td>{{$row->company}}</td>
							@endif
							@if($row->status == 0)
							<td> Inactive </td>
							@elseif($row->status == 1)
							<td> Registered </td>
							@else($row->status == 2)
							<td> Active </td>
							@endif
							<td> {{$row->is_blocked}} </td>
							<td> Administrator </td>
							   
<!-- permissions -->
                               <td class="text-center">
                                     
                                     <a href="{{url('users/permissions/' . $row->id)}}" class="btn btn-sm btn-warning"><i class="fa fa-eye"></i></a>

                               </td>
<!-- end of permissions -->
                            <td class="text-center">
								<div class="actions-btns dule-btns">
									<!-- <a href="javascript:void(0)" data-id="{{$row->id}}" data-status="{{$row->status}}" id="change_status" class="btn btn-sm btn-primary"> <i class="fa fa-eye"> </i></a>  -->
									<a href="{{url('users/edit/' . $row->id)}}" class="btn btn-sm btn-info"><i class="fa fa-edit"></i></a>
									
								</div>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        
       $('#org-users').DataTable();


    	
    	
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