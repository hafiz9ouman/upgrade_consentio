@extends( 'admin.layouts.admin_app' )

@section( 'content' )

<style>
	.uploadcare--jcrop-holder>div>div, #preview {

			}

	.uploadcare--menu__item_tab_facebook, .uploadcare--menu__item_tab_gdrive, .uploadcare--menu__item_tab_gphotos, .uploadcare--menu__item_tab_dropbox, .uploadcare--menu__item_tab_instagram, .uploadcare--menu__item_tab_evernote, .uploadcare--menu__item_tab_flickr, .uploadcare--menu__item_tab_onedrive, .uploadcare--dialog__powered-by {
	display: none !important;
}

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

#size{
    margin-left: 8px;
    font-size: 18px;
    width: 168px;
    padding: 3px;
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

.size{
    margin-left: 58px;
    width: 168px;
    padding: 3px;
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
    margin-left: 8px;
    font-size: 18px;
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
</style>

<div class="app-title">
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i>
		</li>
		<li class="breadcrumb-item"><a href="{{url('site_admins')}}">Site Admins </a>
		</li>
		<li class="breadcrumb-item"><a href="{{url('/edit_admin/'.$user->id)}}">Update Site Administrator</a>
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
	<!--<div class="app-title">-->
	<!--	<ul class="app-breadcrumb breadcrumb">-->
	<!--		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i>-->
	<!--		</li>-->
	<!--		<li class="breadcrumb-item"><a href="{{url('/admin')}}">Users</a>-->
	<!--		</li>-->
	<!--		<li class="breadcrumb-item">Update Organization Administrator</li>-->
	<!--	</ul>-->
	<!--</div>-->
<div class="row">

	<div class="col-md-12">

		<form class="form-horizontal" method="POST" action="{{ url('edit_admin/'. $user->id) }}" autocomplete="off" enctype="multipart/form-data">

			{{ csrf_field() }}

			<div class="tile">

				<h3 class="tile-title">Update Site Administrator</h3>

					<div class="row">

						<div class="col-sm-6 col-md-4">

							<div class="form-group">

								<label class="form-control-label">Name</label>

								<input id="name" type="text" class="form-control" name="name" value="{{ $user->name }}" required autofocus>

							</div>

						</div>

						<div class="col-sm-6 col-md-4">

							<div class="form-group">

								<label class="form-control-label">Email</label>

								<input id="email" type="email" class="form-control is-valid" name="email" value="{{ $user->email }}" readonly>

							</div>

						</div>

						<div class="col-sm-6 col-md-4">

							<div class="form-group">

								<label class="form-control-label">Password</label>

								<input id="password" type="password" class="form-control" name="password">

							</div>

						</div>

						

						<div class="col-sm-6 col-md-4">

							<div class="form-group">

								<label class="form-control-label">Repeat Password</label>

								<input id="rpassword" type="password" class="form-control" name="rpassword">

								<span id='message'></span>

							</div>

						</div>

					

						</div>
							<div class=" row col-sm-6 col-md-4">

							<div class="form-group">

<script>
  										   UPLOADCARE_PUBLIC_KEY = "demopublickey";
										</script>
										<script src="https://ucarecdn.com/libs/widget/3.x/uploadcare.full.min.js" charset="utf-8"></script>
										<input type="hidden" role="uploadcare-uploader" data-crop="5:4" data-images-only >

										<!-- Your preview will be put here -->
										<div class="main_croppir_img" style="width: fit-content;">
											<div class="main_cropie_icon " style="display: none;" id="loader">
						                        <i class="fa fa-circle-o-notch fa-spin fa-5x fa-fw"></i>
						                      </div>



										  <img src="<?php  if($user->image_name != null){  echo url("img/$user->image_name");} else{  echo  URL::to('/dummy.jpg'); } ?>" alt="" id="preview" width=300 height=300 />
										</div>

{{-- END OF NEW CROPPRE --}}

										<input type="hidden" name="base_string" value="" id="destination"> 



	
                              <!-- croper start -->
								{{-- <label class="cabinet center-block">
										<figure>
											<img src="" class="gambar img-responsive img-thumbnail" name="base_64_image" id="item-img-output" />
											<figcaption id="clickabkle"><i class="fa fa-camera d-none"></i></figcaption>
										</figure>
										<input type="file" class="item-img file center-block d-none" accept="image/x-png,image/jpeg,image/jpg" id="ch_br_button" name="images"/>
										<a style="margin-top: 20px; margin-left: 52px;" onclick="document.getElementById('ch_br_button').click()"class="btn btn-primary" href="#">Browse image</a>

										
									</label>
									<input type="hidden" name="base_string" value="" id="destination">
									<script type="text/javascript">
										function abc(){
										     setTimeout(function(){ 
										     document.getElementById("destination").value = document.getElementById("item-img-output").src;
										      }, 350);
										}
										
									</script> --}}

									<!-- croepr edn -->

								
								</div>

							</div>



					<input id="file" type="hidden" class="form-control" name="id" value="{{$user->id}}">

					<div class="tile-footer col-sm-12 text-right">

						<a href="{{url('admin')}}" class="btn btn-default" style="border: solid 1px;">@lang('general.cancel')</a>

						<button type="submit" id="sub_button" class="btn btn-primary">Update</button>

					</div>
				</div>

				</form>

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
    // console.log(dataUrl)
    preview.src = dataUrl
    $('#destination').val(dataUrl);
  })

})




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







// $( "#rpassword" )

//   .focusout(function() {

//   	var pasword,rpasword;

//  pasword = $("#password").val();

//  rpasword = $("#rpassword").val();

//    if(pasword!=rpasword){

//    	alert('password did not match')

//    }

//   })



$('#password, #rpassword').on('keyup', function () {

  if ($('#password').val() == $('#rpassword').val()) {

    $('#message').html('<h5>Password is Matched</h5>').css('color', 'green');

  } else 

    $('#message').html('<h5>Password is Not Matching</h5>').css('color', 'red');

});



</script>



@endsection