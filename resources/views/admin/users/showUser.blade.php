@extends( 'admin.layouts.admin_app' )

@section( 'content' )

<div class="app-title">

	<ul class="app-breadcrumb breadcrumb">

		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i>

		</li>

		<li class="breadcrumb-item"><a href="{{url('/company')}}">Organizations </a></li>
		<li class="breadcrumb-item"><a href="{{url('/client/user/'.$user_id)}}">Organization Users List </a></li>


	</ul>

</div>



<div class="row">

	<div class="col-md-12">

		<div class="tile">

			<h3 class="tile-title"> Organization Users List

			</h3>

			<div class="table-responsive">

				<table class="table">

					<thead class="back_blue">

						<tr>

							<th>Name</th>
							
							<th>Email</th>

							<th>Company</th>
							
							<th>Role</th>
							
							<th>Status</th>

							<th width="130">Image</th>

						</tr>

					</thead>

					<tbody>

						@foreach($user as $row)

						<tr>

							<td>{{$row->name}}</td>
							
							<td> {{$row->email}} </td>

							<?php $client_id = $row->client_id ?>
							@if($row->company =="")
							<?php $user_company = DB::table('users')->where('id',$client_id)->first() ?>
							<td> {{$user_company->company}} </td>
							@else
							<td>{{$row->company}}</td>
							@endif
							<td>
							    <?php 
							        $role = '';
							        switch ($row->role)
							        {
							            case 2:
							                $role = 'Administrator';
							                break;
							            case 3:
							                $role = 'User';
							                break;
							            default:							            
							        }

							         echo $role;
							    ?>
							</td>
							
							@if($row->status == 0)

							<td> Inactive </td>

							@elseif($row->status == 1)

							<td> Registered </td>

							@else($row->status == 2)

							<td> Active </td>

							@endif

						 <td>
                        @if($row->image_name=="")
								<img id="blah" src="{{url('dummy.jpg')}}" style=" width: 100px;; height: 90px; " />
								@else
									@if($row->role == 2)
										<img id="blah" class="img-fluid" src="<?php echo url("img/$row->image_name");?>" name="profile_image" style=" height: 90px; width: 100px;">
									@else
										<img id="blah" class="img-fluid" src="<?php echo url("public/img2/$row->image_name");?>" name="profile_image" style=" height: 90px; width: 100px;">
									@endif
								@endif
                        </td>

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

			title: "Do you want to delete this Blog",

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

					url: '<?php echo url("/blog/delete"); ?>',

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