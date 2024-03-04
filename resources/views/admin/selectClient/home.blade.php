@extends( 'admin.layouts.admin_app' )
@section( 'content' )




<div class="app-title">
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i>
		</li>
		<li class="breadcrumb-item"><a href="{{url('/admin')}}">Dashboard</a>
		</li>		
	</ul>
</div>

@if (session('alert'))
    <div class="alert alert-primary">
        {{ session('alert') }}
    </div>
@endif

<div class="row">
	<div class="col-md-12">
		<div class="tile">
			<h3 class="tile-title">Select Client</h3>
			<div class="table-responsive">
				<table class="table">
					<thead class="back_blue">
						<tr>
							<th>user</th>
							<th>Client</th>
							<th width="130" class="text-center">Actions</th>
						</tr>
					</thead>
					<tbody>
						@foreach($user as $row)
						<tr>
							<form method="post" action="{{ url('saveClient/'. $row->id)}}">
								{{ csrf_field() }}
							<td>{{$row->name}}</td>
							<td> 
							<select name="team" class="form-control" style="width: 50%">
							@foreach($client as $data)
							 <option value="{{ $data->id }}">{{$data->name }}</option> 
							 @endforeach
							</td>
							<td>
							<input type="submit" value="Submit" class="btn btn-primary">
							</td>
						</form>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<!-- <script src="{{url('backend/sweetalerts/sweetalert2.all.js')}}"></script>
<script type="text/javascript">
	$( "body" ).on( "click", ".delete", function () {
		var task_id = $( this ).attr( "data-id" );
		var form_data = {
			id: task_id
		};
		swal({
			title: "Do you want to delete this product",
			text: "@lang('packages.delete_package_msg')",
			type: 'info',
			showCancelButton: true,
			confirmButtonColor: '#F79426',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes',
			showLoaderOnConfirm: true
		}).then( ( result ) => {
			if ( result.value == true ) {
				$.ajax( {
					type: 'POST',
					headers: {
						'X-CSRF-TOKEN': $( 'meta[name="csrf-token"]' ).attr( 'content' )
					},
					url: '<?php echo url("packages/delete"); ?>',
					data: form_data,
					success: function ( msg ) {
						swal( "@lang('packages.success_delete')", '', 'success' )
						setTimeout( function () {
							location.reload();
						}, 1000 );
					}
				} );
			}
		} );

	} );
</script> -->
<style>
	.sweet-alert h2 {
		font-size: 1.3rem !important;
	}
	
	.sweet-alert .sa-icon {
		margin: 30px auto 35px !important;
	}
</style>
@endsection