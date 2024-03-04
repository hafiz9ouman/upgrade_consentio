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
		<li class="breadcrumb-item"><a href="{{url('/company')}}">Organizations </a>
		</li>
		<li class="breadcrumb-item"><a href="{{url('users/edit_company/'.$user->id)}}">Update Organization </a>
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
<div class="row">

	<div class="col-md-12">

		<form class="form-horizontal" method="POST" action="{{ url('users/editCompany_store/'. $user->id) }}" autocomplete="off" enctype="multipart/form-data">

			{{ csrf_field() }}

			<div class="tile">

				<h3 class="tile-title">Update Organization</h3>

					<div class="row">

						<div class="col-sm-6 col-md-4">

							<div class="form-group">

								<label class="form-control-label">Organization Name</label>

								<input id="name" type="text" class="form-control" name="name" value="{{ $user->name }}" required autofocus>

							</div>

							<div class="form-group">

								<label class="form-control-label">Phone</label>

								<input id="name" type="text" class="form-control" name="phone" value="{{ $user->phone }}" required autofocus>

							</div>                                                    
 
							<div class="form-group">

								<label class="form-control-label">Website</label>

								<input id="website_" type="text" class="form-control change_color" onkeyup="isValidURL(this.value)" name="website" value="{{ $user->website }}" required autofocus>
								<div class="show_error"  style="color: red; display: none;">
										<small>Please provide proper web address</small>
									</div>	
							</div> 

						</div>

						<div class="col-sm-6 col-md-4">

							<div class="form-group">	
                                @if($user->role ==3)
								<label class="form-control-label">Profile Image</label>
								@else
								<label class="form-control-label">Logo</label>
								@endif

								<div class="img_dlt">

									<!-- <a class="btn btn-primary" onclick="document.getElementById('clickabkle').click()" id="add_images" href="#">Browse image</a>
										<div id="size">Max Resolution 300 x 180</div>
										<div id="size">Max Size 1 MB</div>

										<label style="display: none;">

										<input id="images" type="file"  name="images">

										<input name="profile_image" value="{{ $user->image_name }}">

									</label> -->
											<!-- croper start -->
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

										  <img  src=" <?php  if($user->image_name != null){  echo url("img/$user->image_name");} else{  echo  URL::to('_organisation.png'); } ?>" alt="" id="preview" class="gambar img-responsive img-thumbnail"   />
										</div>

{{-- END OF NEW CROPPRE --}}

										<input type="hidden" name="base_string" value="" id="destination"> 

									<!-- croepr edn -->


								

						
								</div>

							</div>

						</div>



					<input id="file" type="hidden" class="form-control" name="id" value="{{$user->id}}">

					<div class="tile-footer col-sm-12 text-right">

						<a href="{{url('company')}}" class="btn btn-default" style="border:solid 1px">@lang('general.cancel')</a>

						<button type="submit" id="sub_button" class="btn btn-primary">Update</button>


<button type="button" data-id="{{$user->id}}" class="btn btn-danger removePartner">Delete</button>




					</div>

				</form>

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

		<script type="text/javascript">
			function isValidURL(string) {
					  // var string = $('#website_').val();
					  var result;
					  var res = string.match(/(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g);
					   result =  (res != null);
					   if(result == false){
					   	$('#sub_button').prop('disabled',true);
					   	 $('.show_error').show();
					   	 $('.website_error').css('border', '2px solid red');
					   	 $('.change_color').css('color', 'red');
					   }
					   else{
					   	$('#sub_button').prop('disabled',false);
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

$('#password, #rpassword').on('keyup', function () {

  if ($('#password').val() == $('#rpassword').val()) {

    $('#message').html('<h5>Password is Matched</h5>').css('color', 'green');

  } else 

    $('#message').html('<h5>Password is Not Matching</h5>').css('color', 'red');

});



</script>



@endsection