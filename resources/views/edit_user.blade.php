@extends('admin.client.client_app')
@section('page_title')
	EDIT USER
@endsection
@section('content')
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

		#size{
			font-size: 18px;
			width: 174px;
			/*padding: 3px;*/
			padding: 5px 4px;
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

		#add_images {
			margin-top: 5px;
		}

		.widget__button_type_open{
			background: #0f75bd;
		}
		.uploadcare--button_primary{
			background: #0f75bd;
			border-color: #0f75bd;
		}
		@media screen and (max-width: 580px) {
			.main_croppir_img img {
				margin-top: 10px;
				width: 100% !important;
			}
		}
	</style>
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



	

<div class="card" style="border-radius: 20px; margin-left: 30px !important;margin-right: 30px !important;">
	<div class=" card-body">
		<form class="form-horizontal" method="POST" action="{{ url('store_edit/'. $user->id) }}" enctype="multipart/form-data">
			{{ csrf_field() }}
			<div class="tile">
				<h3 class="tile-title"></h3>
					<div class="row">
						<div class="col-md-8 right-side">
							<div class="form-group row">
								<label class="col-sm-3 col-form-label form-control-label">{{ __('Name')}}</label>
                                <div class="col-md-8">
									<input id="name" type="text" class="form-control" name="name" value="{{ $user->name }}" required autofocus>
                                </div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label form-control-label">{{ __('Email')}}</label>
                                <div class="col-md-8">
								<input id="email" type="email" class="form-control is-valid" name="email" value="{{ $user->email }}" readonly>
                                </div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-labe form-control-label">{{ __('Password')}}</label>
                                <div class="col-md-8">
									<input id="password" type="password" class="form-control" name="password">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-labe form-control-label">{{ __('Repeat Password')}}</label>
                                 <div class="col-md-8">
									<input id="rpassword" type="password" class="form-control" name="rpassword">
									<span id='message'></span>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-labe form-control-label">{{ __('Enable Email Verification')}}</label><br>
								 <div class="col-md-8">
									<label class="switch"> 
										@if($user->is_email_varified==0)
							  				<input name="mail_verification" type="checkbox" checked>
							  			@else
							  				<input name="mail_verification" type="checkbox">
							  			@endif
										<span class="slider round"></span>
									</label>
									<span id='message'></span>
								</div>
							</div>
						</div>
						<div class="col-md-8">
                            <div class="profile_info">
								<div class="form-group">	
									<script>
										UPLOADCARE_PUBLIC_KEY = "demopublickey";
									</script>
									<input type="hidden" role="uploadcare-uploader" data-crop="free,1:1,5:4" data-images-only >
									<div class="main_croppir_img">
										<div class="main_cropie_icon " style="display: none;" id="loader">
											<i class="fa fa-circle-o-notch fa-spin fa-5x fa-fw"></i>
										</div>
										<img  src="<?php  if($user->image_name != null){  echo url("public/img2/$user->image_name");} else{  echo  URL::to('/dummy.jpg'); } ?>" alt="" id="preview" width=300  />
									</div>
									<input type="hidden" name="base_string" value="" id="destination">
								</div>
								<input id="file" type="hidden" class="form-control" name="id" value="{{$user->id}}">
							</div>

							<div class="tile-footer text-right">
								<a href="{{url('users_management')}}" class="btn btn-default" style="color: #4e73df; border: solid 1px;">{{ __('Cancel') }}</a>
								<button type="submit" id="sub_button" class="btn btn-primary">{{ __('Save')}}</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	<!-- </div>
</div> -->



<!-- croper model -->

	<div class="modal fade" id="cropImagePop" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel"></h4>
				</div>
				<div class="modal-body">
					<div id="upload-demo" class="center-block"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Close')}}</button>
					<button type="button" id="cropImageBtn" onclick="abc()" class="btn btn-primary">{{ __('Crop')}}</button>
				</div>
			</div>
		</div>
	</div>
	<script src="https://ucarecdn.com/libs/widget/3.x/uploadcare.full.min.js" charset="utf-8"></script>
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
			}
			))
			toDataURL(fileInfo.cdnUrl)
			.then(dataUrl => {
				$('#sub_button').show();
				$('#loader').hide();
    			// console.log(dataUrl)
    			preview.src = dataUrl
    			$('#destination').val(dataUrl);
			})
		});
	</script>

	<script>
		// Start upload preview image
		$(".gambar").attr("src", "<?php  if($user->image_name != null){  echo url("img/$user->image_name");} else{  echo  URL::to('/dummy.jpg'); } ?>");
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
				swal("{!! __('Sorry - you are browser does not support the FileReader API')!!}");
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

		$('#password, #rpassword').on('keyup', function () {
			if ($('#password').val() == $('#rpassword').val()) {
				$('#message').html('<h5>{!! __('Password is Matched') !!}</h5>').css('color', 'green');
			} else {
				$('#message').html('<h5>{!! __('Password is Not Matching')!!}</h5>').css('color', 'red');
			}
		});
	</script>

@endsection
