		<!-- <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css"> -->
		<!-- <link rel="stylesheet" type="text/css" href="https://foliotek.github.io/Croppie/croppie.css"> -->
		<!-- <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"> -->
		<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script> -->
		<!-- <script src="https://foliotek.github.io/Croppie/croppie.js"></script> -->
		<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script> -->
@extends( 'admin.layouts.admin_app' )
@section( 'content' )


<style>

.main_croppir_img {
    position: relative;
    overflow: hidden;
}
.main_croppir_img .main_cropie_icon {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    color: #fff;
    background: #0000008f;
}
	

	.uploadcare--jcrop-holder>div>div, #preview {
			  /*border-radius: 50%;*/
			}

	.uploadcare--menu__item_tab_facebook, .uploadcare--menu__item_tab_gdrive, .uploadcare--menu__item_tab_gphotos, .uploadcare--menu__item_tab_dropbox, .uploadcare--menu__item_tab_instagram, .uploadcare--menu__item_tab_evernote, .uploadcare--menu__item_tab_flickr, .uploadcare--menu__item_tab_onedrive, .uploadcare--dialog__powered-by {
	display: none !important;
}

    label.cabinet{
	display: block;
	cursor: pointer;
	}

	label.cabinet input.file{
		position: relative;
		height: 100%;
		width: auto;
		opacity: 0;
		-moz-opacity: 0;
	  filter:progid:DXImageTransform.Microsoft.Alpha(opacity=0);
	  margin-top:-30px;
	}

#upload-demo{
    width: 100%;
    height: 265px;
    padding-bottom: 25px;
	}
	.img-thumbnail{
		background-color:#000 !important;
	}

	/*end of croper style*/
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.size{
    margin-left: 58px;
    width: 168px;
    padding: 3px;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
.max-spec {
    font-size:14px;
}
.dummi-img {
    width: auto;
    height: 60px;
}
.show_error {
	color: red;
	display: none;
}
.card-body-form , .OrganizationUser {
	width: 100%;
}
</style>
<div class="app-title">
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i>
		</li>
		<li class="breadcrumb-item"><a href="{{url('/company')}}">Organizations </a>
		</li>
		<li class="breadcrumb-item"><a href="{{url('/client/add')}}">Add Organization </a>
		</li>		
	</ul>
</div>
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

<!-- Breadcrumb-->
<div class="breadcrumb-holder container-fluid">
	<!--<ul class="breadcrumb">-->
	<!--	<li class="breadcrumb-item">-->
	<!--		<a href="{{url('dashboard')}}">Home</a>-->
	<!--	</li>-->
	<!--	<li class="breadcrumb-item active">Add New</li>-->
	<!--</ul>-->
</div>
<!-- Forms Section-->
<section class="forms">
	<div class="container-fluid">

		<div class="row">
			<div class="col-lg-12">

				<div class="card">

					<div class="card-header d-flex align-items-center">
						<h3 class="h4">Add New Organization</h3>
					</div>
					<div class="card-body" id="org-form">
						<div class="col-sm-6 col-md-4">
							<div class="card-body-form">
						<form class="form-horizontal" method="POST" action="{{ url('client/store') }}" autocomplete="off"    enctype="multipart/form-data">
						{{ csrf_field() }}
						
						<div class="form-group row">
						        <!--
								<label class="form-control-label">Administrator Name</label>
								<div class="">
									<input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus> 
								</div>-->
							</div>
							<div class="line"></div>

							<div class="form-group row">
								<label class="form-control-label">Organization Name<span style="color:red;">*</span></label>
								<div class="">
									<input id="company_" type="text" class="form-control" name="company" value="{{ old('company') }}" required autofocus> 
								</div>
							</div>
							<div class="line"></div>
                                                        <div class="form-group row">
								<label class="form-control-label">Phone<span style="color:red;">*</span></label>
								<div class="">
									<input id="phone_" type="number" class="form-control" name="phone" value="{{ old('phone') }}" required autofocus> 
								</div>
							</div>
							<div class="line"></div>
                            <div class="form-group row">
								<label class="form-control-label change_color">Website</label>
								<div class="">
									<input id="website_" type="text" class="form-control website_error" name="website" onkeyup="isValidURL(this.value)" value="{{ old('website') }}" autofocus> 
									<div class="show_error">
										<small>Please provide proper web address</small>
									</div>
										
								</div>

							</div>

							<!--<div class="form-group row">-->
							<!--	<label class="form-control-label">Email</label>-->
							<!--	<div class="">-->
							<!--		<input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>-->
							<!--	</div>-->
							<!--</div>-->
							<!--<div class="line"></div>-->
							
							
							<!--<div class="form-group row">-->
							<!--	<label class="form-control-label">Password</label>-->
							<!--	<div class="">-->
							<!--		<input id="password" type="password" class="form-control" name="password" required>-->
							<!--	</div>-->
							<!--</div>-->
							<!--<div class="line"></div>-->

							<!--<div class="form-group row">-->
							<!--	<label class="form-control-label">Repeat Password</label>-->
							<!--	<div class="">-->
							<!--		<input id="rpassword" type="password" class="form-control" name="rpassword" required>-->
							<!--	</div>-->
							<!--</div>-->
							<!--<div class="line"></div>-->

							<!--<div class="form-group row">-->
							<!--<label class="col-sm-2 form-control-label">2FA</label>-->
							<!--<label class="switch">-->
							<!--  <input name="slider" type="checkbox">-->
							<!--  <span class="slider round"></span>-->
							<!--</label>-->
							<!--</div>-->
							<!--<div class="line"></div>-->
						
						</div>
						</div>
											
						<div class="col-sm-6 col-md-4" style="    padding-left: 50px;">
							<div class="OrganizationUser">
								<div class="form-group row">
									<label class="col-sm-6 form-control-label">Logo<span style="color:red;">*</span></label>
									<div class="img_dlt">
											

										<script>
  										   UPLOADCARE_PUBLIC_KEY = "demopublickey";
										</script>
										<script src="https://ucarecdn.com/libs/widget/3.x/uploadcare.full.min.js" charset="utf-8"></script>
										<input type="hidden" role="uploadcare-uploader" data-crop="free, 5:1 ,16:9, 4:3, 5:4, 1:1" data-images-only >

										<!-- Your preview will be put here -->
										<div class="main_croppir_img"  style="width: fit-content;" >
											
											<div class="main_cropie_icon " style="display: none;" id="loader">
						                        <i class="fa fa-circle-o-notch fa-spin fa-5x fa-fw"></i>
						                      </div>

										  <img src="<?php  echo  URL::to('_organisation.png');  ?>" alt="" id="preview" class="gambar img-responsive img-thumbnail"  />
										</div>

										<input type="hidden" name="base_string" value="" id="destination"> 
										<span style="color: red;">Recommended Size: 200*47px</span>
									</div>		
								</div>
								<div class="form-group row form-btn" style="margin-left: 52px;">
								<div class="col-sm-12 text-left">
									
									<a href="{{url('company')}}" class="btn btn-sm btn-secondary">@lang('general.cancel')</a>
									<button type="submit" id="sub_button" class="btn btn-sm btn-primary">@lang('general.save') </button>

								</div>
							</div>
							</div>
						</div>
							
							
							
							
                    </form>
							


					</div>
				</div>
			</div>
		</div>
	</div>


	<!-- croper model -->
	<div class="modal fade" id="cropImagePop" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">
						</h4>
					</div>
					<div class="modal-body">
						<div id="upload-demo" class="center-block"></div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="button" id="cropImageBtn" onclick="abc()" class="btn btn-primary">Crop</button>
					</div>
				</div>
			</div>
		</div>
</section>

	<script type="text/javascript">
		$(document).ready(function() {
		$('#sub_button').prop('disabled',true);	
		});
	</script>


<script>
	// Getting an instance of the widget.
const widget = uploadcare.Widget('[role=uploadcare-uploader]');
// Selecting an image to be replaced with the uploaded one.
const preview = document.getElementById('preview');
// "onUploadComplete" lets you get file info once it has been uploaded.
// "cdnUrl" holds a URL of the uploaded file: to replace a preview with.
widget.onUploadComplete(fileInfo => {
	//alert('1');
	$('#sub_button').hide();
    $('#loader').show();
  preview.src = fileInfo.cdnUrl;
  // alert(fileInfo.cdnUrl);

  const toDataURL = url => fetch(url)
  .then(response => response.blob())
  .then(blob => new Promise((resolve, reject) => {
  	//alert('2');
  	$('#sub_button').hide();
    $('#loader').show();
    const reader = new FileReader()
    reader.onloadend = () => resolve(reader.result)
    reader.onerror = reject
    reader.readAsDataURL(blob)
  }))


toDataURL(fileInfo.cdnUrl)
  .then(dataUrl => {
  	//alert('3');
  	$('#sub_button').show();
    $('#sub_button').prop('disabled',false);
    $('#loader').hide();
    // console.log(dataUrl)
    preview.src = dataUrl
    $('#destination').val(dataUrl);
  })

})




</script>

		<script type="text/javascript">
			function isValidURL(string) {
					  // var string = $('#website_').val();
					  var result;
					  var res = string.match(/(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g);
					   result =  (res != null);
					   if(result == false){
					   	//$('#sub_button').prop('disabled',true);
					   	 $('.show_error').show();
					   	 $('.website_error').css('border', '2px solid red');
					   	 $('.change_color').css('color', 'red');
					   }
					   else{
					   	//$('#sub_button').prop('disabled',false);
					   	 $('.show_error').hide();
					   	  	$('.website_error').css('border', '2px solid green');
					   	  	$('.change_color').css('color', 'green');
					    }
						};


		</script>
		<script type="text/javascript">
			$(document).on("keydown", "form", function(event) { 
    return event.key != "Enter";
});
		</script>

<script>
	
function readURL(input) {

  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function(e) {
      $('#blah').attr('src', e.target.result);
    }

    reader.readAsDataURL(input.files[0]);
  }
}

	




</script>

@endsection