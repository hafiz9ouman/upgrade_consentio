
@extends('admin.client.client_app')

@section('content')
@section('page_title')
		{{ __('ADD ORGANISATIONAL USER') }}
@endsection
  <link rel="stylesheet" type="text/css" href="https://foliotek.github.io/Croppie/croppie.css">

    <script src="https://foliotek.github.io/Croppie/croppie.js"></script>

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

<style>
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
		/*background-color:#000 !important;*/
	}

.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

#size{
    font-size: 17px;
    width: 174px;
    padding-top: 9px;
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

.uploadcare--widget__button {
    background: #0f75bd !important;
    color: #fff !important;
}
.uploadcare--button_primary{
	background: #0f75bd !important;
    border-color: #0f75bd !important;
    color: #fff;
}
@media screen and (max-width: 580px) {
	.main_croppir_img img {
		margin-top: 10px;
		width: 100% !important;
	}
}
</style>


<!-- Breadcrumb-->

<!-- Forms Section-->
<section class="forms">
	<div class="container-fluid">

		<div class="row">
			<div class="col-lg-12">

				<div class="card" style="border-radius: 20px;">

					{{-- <div class="card-header d-flex align-items-center">
						<h3 class="h4"></h3>
					</div> --}}
					<div class="card-body">
						<form class="form-horizontal" method="POST" action="{{ url('store_user') }}" enctype="multipart/form-data">
						{{ csrf_field() }}

							<div class="form-group row">
								<label class="col-sm-2 form-control-label">{{ __('Name') }}</label>
								<div class="col-sm-10">
									<input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus> 
								</div>
							</div>
							<div class="line"></div>

							<div class="line"></div>

							<div class="form-group row">
								<label class="col-sm-2 form-control-label">{{ __('Email') }}</label>
								<div class="col-sm-10">
									<input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
								</div>
							</div>
							<div class="line"></div>
							
							
							<div class="form-group row">
								<label class="col-sm-2 form-control-label">{{ __('Password') }}</label>
								<div class="col-sm-10">
									<input id="password" type="password" class="form-control" name="password" required>
								</div>
							</div>
							<div class="line"></div>

							<div class="form-group row">
								<label class="col-sm-2 form-control-label">{{ __('Repeat Password') }}</label>
								<div class="col-sm-10">
									<input id="rpassword" type="password" class="form-control" name="rpassword" required>
								</div>
							</div>
							<div class="line"></div>
                            
                           
							<!-- <div class="form-group row" style="display:none;">
							<label class="col-sm-2 form-control-label">2FA</label>
								<label class="switch">
								    @if(old('slider') == "on")
									  <input name="slider" type="checkbox" checked>
									  @else
									  <input name="slider" type="checkbox">
									 @endif
									  <span class="slider round"></span>
								</label>
							</div>
							<div class="line"></div> -->
							
							<div class="form-group row">
							<label class="col-sm-2 form-control-label">{{ __('Enable Email Verification')}}</label>
								<label class="switch" style="margin-left: 10px;">
								    @if(old('slider') == "on")
									  <input name="mail_verification" type="checkbox" checked>
									  @else
									  <input name="mail_verification" type="checkbox" >
									 @endif
									  <span class="slider round"></span>
								</label>
							</div>
							<div class="line"></div>

							

							{{-- <div class="form-group row"> --}}
								{{-- <label class="col-sm-2 form-control-label">Profile image</label>
								<div class="img_dlt">
									<a class="btn btn-primary" id="add_images" href="#">Browse image</a>
									<div id="size" style="font-size:14px;">Max Resolution 800 x 600</div>
									<div id="size" style="font-size:14px;">Max Size 1 MB</div>
										<label style="display: none;">
										<input id="images" type="file"  name="images">
									</label> --}}

											<!-- croper start -->
										{{-- <figure>
											<img src="" class="gambar img-responsive img-thumbnail" name="base_64_image" id="item-img-output" style="width: 500px; height: 200px;" />
											<figcaption id="clickabkle"><i class="fa fa-camera d-none"></i></figcaption>
										</figure>
										<input type="file" class="item-img file center-block d-none" accept="image/x-png,image/jpeg,image/jpg" id="ch_br_button" name="images"/>
										<a style="margin-top: 20px; margin-left: 52px;" onclick="document.getElementById('ch_br_button').click()"class="btn btn-primary" href="#">Browse image</a>

									
									<input type="hidden" name="base_string" value="" id="destination">
									<script type="text/javascript">
										function abc(){
										     setTimeout(function(){ 
										     document.getElementById("destination").value = document.getElementById("item-img-output").src;
										      }, 350);
										}
										
									</script> --}}

									<!-- croepr edn -->
										<script>
  										   UPLOADCARE_PUBLIC_KEY = "demopublickey";
										</script>
										<script src="https://ucarecdn.com/libs/widget/3.x/uploadcare.full.min.js" charset="utf-8"></script>
										<input type="hidden" role="uploadcare-uploader" data-crop="free, 5:4, 1:1" data-images-only >

										<!-- Your preview will be put here -->
										<div class="main_croppir_img" style="width: fit-content;">
											<div class="main_cropie_icon " style="display: none;" id="loader">
						                        <i class="fa fa-circle-o-notch fa-spin fa-5x fa-fw"></i>
						                      </div>
										  <img src="<?php  echo  URL::to('/dummy.jpg');  ?>" alt="" id="preview" width=300  />
										</div>

{{-- END OF NEW CROPPRE --}}

										<input type="hidden" name="base_string" value="" id="destination"> 



									<br>
									
								{{-- </div>		 --}}
							

							<div class="form-group row">
								<div class="col-sm-12 text-right">
									<a href="{{url('users_management')}}" class="btn btn-sm btn-secondary"> {{ __('Cancel')}}</a>
									<button type="submit" id="sub_button" class="btn btn-sm btn-primary">{{ __('Save')}} </button>
								</div>
							</div>
				
			
		</form>
	</div>
</section>
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
						<button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Close') }}</button>
						<button type="button" id="cropImageBtn" onclick="abc()" class="btn btn-primary">{{ __('Crop') }}</button>
					</div>
				</div>
			</div>
		</div>

		
<script>
	// Getting an instance of the widget.
const widget = uploadcare.Widget('[role=uploadcare-uploader]');
// Selecting an image to be replaced with the uploaded one.
const preview = document.getElementById('preview');
// "onUploadComplete" lets you get file info once it has been uploaded.
// "cdnUrl" holds a URL of the uploaded file: to replace a preview with.
widget.onUploadComplete(fileInfo => {
	$('#sub_button').hide();
    $('#loader').show();
  preview.src = fileInfo.cdnUrl;
  // alert(fileInfo.cdnUrl);

  const toDataURL = url => fetch(url)
  .then(response => response.blob())
  .then(blob => new Promise((resolve, reject) => {
  	$('#sub_button').hide();
    $('#loader').show();
    const reader = new FileReader()
    reader.onloadend = () => resolve(reader.result)
    reader.onerror = reject
    reader.readAsDataURL(blob)
  }))


toDataURL(fileInfo.cdnUrl)
  .then(dataUrl => {
  	$('#sub_button').show();
    $('#loader').hide();
    preview.src = dataUrl
    $('#destination').val(dataUrl);
  })

})




</script>

<script>

			// Start upload preview image
                        $(".gambar").attr("src", "<?php  echo  URL::to('/dummy.jpg');  ?>");
						var $uploadCrop,
						tempFilename,
						rawImg,
						imageId;
						function readFile(input) {
				 			if (input.files && input.files[0]) {
				              var reader = new FileReader();
					            reader.onload = function (e) {
									$('.upload-demo').addClass('ready');
									$('#cropImagePop').modal('show');
						            rawImg = e.target.result;
					            }
					            reader.readAsDataURL(input.files[0]);
					        }
					        else {
						        swal("Sorry - you're browser doesn't support the FileReader API");
						    }
						}

						$uploadCrop = $('#upload-demo').croppie({
							viewport: {
								width: 500,
								height: 200,
							},
							enforceBoundary: true,
							enableExif: true
						});
						$('#cropImagePop').on('shown.bs.modal', function(){
							// alert('Shown pop');
							$uploadCrop.croppie('bind', {
				        		url: rawImg
				        	}).then(function(){
				        		console.log('jQuery bind complete');
				        	});
						});

						$('.item-img').on('change', function () { imageId = $(this).data('id'); tempFilename = $(this).val();
							 $('#cancelCropBtn').data('id', imageId); readFile(this); });
						$('#cropImageBtn').on('click', function (ev) {
							$uploadCrop.croppie('result', {
								type: 'base64',
								format: 'jpeg',
								size: {width: 800, height: 600}
							}).then(function (resp) {
								$('#item-img-output').attr('src', resp);
								$('#cropImagePop').modal('hide');
							});
						});
				// End upload preview image
		</script>
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