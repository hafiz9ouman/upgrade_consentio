@extends( 'admin.layouts.admin_app' )
@section( 'content' )

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

<div class="app-title">

	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i>
		</li>
		<li class="breadcrumb-item"><a href="{{url('/company')}}">Organizations</a>
		</li>
	</ul>
</div>


<div class="row">
	<div class="col-md-12">
		<div class="tile">
			<h3 class="tile-title">Organizations 			
				<a href="{{url('client/add')}}" class="btn btn-sm btn-success pull-right cust_color" style="margin-right: 10px;"><i class="fa fa-plus" ></i> Add Organization</a>
			</h3>
			<div class="table-responsive">
				<table class="table" id="orgs">
					<thead class="back_blue">
						<tr>
							<th>Organization Name</th>
						    <th>Users</th>
						    <th>Add Admins</th>
							<th width="130" class="text-center">Actions</th>
						</tr>
					</thead>
					<tbody>
						@foreach($users as $row)
						<tr>
							<td>
							<a target="_blank" title="View User" href="{{url('client/user/'.$row->id)}}"> <i class="fa fa-users"></i> {{$row->company}}</a>
							</td>
						    <td>
						    <?php
						        if (isset($row->users_count) && !empty($row->users_count)):
						            foreach ($row->users_count as $user_type => $user_count): 
						                echo $user_type." : <b>".$user_count."</b><br>";
						            endforeach;
						        endif;
						    ?>
						    </td>
						    <td><a href="{{url('users/add/'.$row->id)}}" class="btn btn-sm btn-success cust_color"><i class="fa fa-plus" ></i> Add Administrator</a></td>
							<td class="text-center">
								<div class="actions-btns dule-btns">
									<!-- <a href="javascript:void(0)" data-id="{{$row->id}}" data-status="{{$row->status}}" id="change_status" class="btn btn-sm btn-primary"> <i class="fa fa-eye"> </i></a>  -->
									<a href="{{url('users/edit_company/' . $row->id)}}" class="btn btn-sm btn-info"><i class="fa fa-edit"></i></a>
									
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
    $(document).ready(function(){
        
        
        $('#orgs').DataTable();
        
    	
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
    
    
    	});        
        
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