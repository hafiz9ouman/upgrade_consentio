@extends( 'admin.layouts.admin_app' )
@section( 'content' )

@if (session('alert'))
    <div class="alert alert-danger">
        {{ session('alert') }}
    </div>
@endif


<!-- Breadcrumb-->
<div class="breadcrumb-holder container-fluid">
	<ul class="breadcrumb">
		<li class="breadcrumb-item">
			<a href="{{url('/admin')}}">Home</a>
		</li>
		<li class="breadcrumb-item active">Add New</li>
	</ul>
</div>
<!-- Forms Section-->
<section class="forms">
	<div class="container-fluid">

		<div class="row">
			<div class="col-lg-12">

				<div class="card">

					<div class="card-header d-flex align-items-center">
						<h3 class="h4">Add New User</h3>
					</div>
					<div class="card-body">
						<form class="form-horizontal" method="POST" action="{{ url('users/store') }}" enctype="multipart/form-data">
						{{ csrf_field() }}

							<div class="form-group row">
								<label class="col-sm-2 form-control-label">Name</label>
								<div class="col-sm-10">
									<input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus> 
								</div>
							</div>
							<div class="line"></div>

							<div class="form-group row">
								<label class="col-sm-2 form-control-label">Company Name</label>
								<div class="col-sm-10">
									<input id="company" type="text" class="form-control" name="company" value="{{ old('company') }}" required autofocus> 
								</div>
							</div>
							<div class="line"></div>

							<div class="form-group row">
								<label class="col-sm-2 form-control-label">Email</label>
								<div class="col-sm-10">
									<input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
								</div>
							</div>
							<div class="line"></div>
							
							
							<div class="form-group row">
								<label class="col-sm-2 form-control-label">Password</label>
								<div class="col-sm-10">
									<input id="password" type="password" class="form-control" name="password" required>
								</div>
							</div>
							<div class="line"></div>

							<div class="form-group row">
								<label class="col-sm-2 form-control-label">Repeat Password</label>
								<div class="col-sm-10">
									<input id="rpassword" type="password" class="form-control" name="rpassword" required>
								</div>
							</div>
							<div class="line"></div>


							<div class="form-group row">
								<label class="col-sm-2 form-control-label">User Role</label>
								  <div class="radio">
								      <label><input type="radio" name="optradio" checked value=0>User</label>
								    </div>&emsp; 
								    <div class="radio"> 
								      <label><input type="radio" name="optradio" value=2>Client</label>
								    </div>
							</div>
							<div class="line"></div>

							

							<div class="form-group row">
								<label class="col-sm-2 form-control-label">Profile image</label>
								<div class="img_dlt">
									<a class="btn btn-primary" id="add_images" href="javascipt:void(0)">Browse image</a>
										<label style="display: none;">
										<input id="images" type="file"  name="images">
									</label><br>
									<img id="blah" src="<?php echo url("dummy.jpg");?>" style=" width: 325px; height: 270px; padding: 9px;" />
								</div>		
							</div>
							

							<div class="form-group row">
								<div class="col-sm-12 text-right">
									<a href="{{url('users')}}" class="btn btn-sm btn-secondary">@lang('general.cancel')</a>
									<button type="submit" class="btn btn-sm btn-primary">@lang('general.save') </button>
								</div>
							</div>


					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<script>
	
	$('#add_images').click(function(){
	$('#images').click();
	});

function readURL(input) {

  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function(e) {
      $('#blah').attr('src', e.target.result);
    }

    reader.readAsDataURL(input.files[0]);
  }
}

$("#images").change(function() {
  readURL(this);
});


</script>

@endsection