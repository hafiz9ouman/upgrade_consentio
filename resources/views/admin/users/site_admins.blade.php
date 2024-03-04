@extends( 'admin.layouts.admin_app' )
@section( 'content' )
<style>
    .no-txt-transform {
        text-transform: none;
    }
</style>
<div class="app-title">

	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i>
		</li>
		<li class="breadcrumb-item"><a href="{{url('/admin')}}">Site Admins</a>
		</li>
	</ul>
</div>

@if (session('alert'))
    <div class="alert alert-danger">
        {{ session('alert') }}
    </div>
@endif



<div class="row">
	<div class="col-md-12">
		<div class="tile">
			<h3 class="tile-title">Site Admins 			
				<a href="{{url('/add_admin')}}" class="btn btn-sm btn-primary pull-right cust_color"><i class="fa fa-plus" ></i> Add Site Administrator</a>
			</h3>
			<div class="table-responsive">
				<table class="table" id="org-users">
					<thead class="back_blue">
						<tr>
							<th>Name</th>
							<th>Email</th>
							<th class="text-center">Actions</th>
						</tr>
					</thead>
					<tbody>
						@foreach($users as $row)
						<tr>
							<td class="no-txt-transform"> {{$row->name}} </td>
							<td class="no-txt-transform">{{$row->email}}</td>
							<td class="text-center">
								<div class="actions-btns dule-btns">
									<!-- <a href="javascript:void(0)" data-id="{{$row->id}}" data-status="{{$row->status}}" id="change_status" class="btn btn-sm btn-primary"> <i class="fa fa-eye"> </i></a>  -->
									<a href="{{url('edit_admin/' . $row->id)}}" class="btn btn-sm btn-info"><i class="fa fa-edit"></i></a>
									<a href="javascript:void(0)" data-id="{{$row->id}}" class="btn btn-sm btn-danger removePartner"><i class="fa fa-trash"></i></a>
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


    	$( "body" ).on( "click", ".removePartner", function () {
    		var task_id = $( this ).attr( "data-id" );
    		var form_data = {
    			id: task_id
    		};
    		swal( {
    				title: "@lang('users.delete_user')",
    				text: "@lang('users.delete_user_msg')",
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
    					url: '<?php echo url("users/delete"); ?>',
    					data: form_data,
    					success: function ( msg ) {
    						swal( "@lang('users.success_delete')", '', 'success' )
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