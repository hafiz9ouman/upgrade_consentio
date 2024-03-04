@extends('admin.client.client_app')

@section('content')
@section('page_title')
EDIT PROFILE
@endsection

        <!-- Begin Page Content -->
<!--  -->
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
    #size{
    margin-left: 8px;
    font-size: 18px;
    width: 174px;
    padding: 3px;
}
.img_dlt{
        display: flex;
    flex-direction: column;
    align-items: flex-end;
       align-items: center;
    text-align: center;
}
.h1_sample{
        position: fixed;
            top: 20%;
}

@media screen and (max-width: 576px){
   .h1_sample{
        position: initial;
} 
}
#add_images {
    margin-top: 11px;
}
.uploadcare--widget__button{
  background: #0f75bd !important;
}
.uploadcare--widget__button_type_open{
  background: #0f75bd !important;
}
.uploadcare--button_primary{
  background: #0f75bd !important;
  border-color: #0f75bd !important;
}
</style>
  

      <div class="card" style="border-radius: 30px;margin-left: 15px; margin-right: 15px">
        <div class="card-body">
        <section class="user_profile">
             
          <div class="left-side-bar">
            
            <form method="post" action="{{url('profile/edit')}}" enctype="multipart/form-data">
            {{ csrf_field() }}
            
            <div class="row">
            <div class="col-md-8 right-side">
            <form>
                
            <input type="hidden" name="id" value="{{$client->id}}">
                <div class="form-group row">
                  <label for="" class="col-sm-3 col-form-label">{{ __('Name')}}</label>
                  <div class="col-sm-8">
                    <input type="text" class="form-control" id="name" name="name" value="{{$client->name}}">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="" class="col-sm-3 col-form-label">{{ __('Email')}}</label>
                  <div class="col-sm-8">
                    <input type="text" class="form-control" id="email" name="email" value="{{$client->email}}" readonly>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="" class="col-sm-3 col-form-label">{{ __('Company Name')}}</label>
                  <div class="col-sm-8">
                        <input type="text" class="form-control" id="company" name="company" value="{{$company_name->company}}" readonly>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="" class="col-sm-3 col-form-label">{{ __('Password')}}</label>
                  <div class="col-sm-8">
                    <input type="password" class="form-control" name="password" id="password" >
                  </div>
                </div>
                 <div class="form-group row">
                  <label for="" class="col-sm-3 col-form-label">{{ __('Repeat Password')}}</label>
                  <div class="col-sm-8">
                    <input type="password" class="form-control" name="rpassword" id="rpassword" >
                    <a href="{{url('dashboard')}}" class="btn btn-default" style="color: #4e73df; border: solid 1px;">{{ __('Cancel') }}</a>
                    <button class="btn btn-primary" id ="sub_button"  type="submit" style="margin: 20px 0;">{{ __('Update')}}</button>
                    <span id='message'></span>
                  </div>
                </div>
                
              </form>
              </div>
              <div class="profile_info">
             
          

      <div class="row col-sm-4">

        <div class="form-group">  
          
           
                    <script>
                         UPLOADCARE_PUBLIC_KEY = "demopublickey";
                    </script>
                    <script src="https://ucarecdn.com/libs/widget/3.x/uploadcare.full.min.js" charset="utf-8"></script>
                    <input type="hidden" role="uploadcare-uploader" data-crop="5:4" data-images-only >

                    <!-- Your preview will be put here -->
                    <div class="main_croppir_img">
                      
                      <div class="main_cropie_icon " style="display: none;" id="loader">
                        <i class="fa fa-circle-o-notch fa-spin fa-5x fa-fw"></i>
                      </div>
                                          
                      <img src="<?php if( $client->image_name=="" ){ echo url("dummy.jpg"); } else{ if(auth()->user()->role == 3){ echo url("public/img2/$client->image_name"); } else{ echo url("img/$client->image_name"); } }?>" alt="" id="preview" width="300" style="height: auto;" />
                    </div>

{{-- END OF NEW CROPPRE --}}

                    <input type="hidden" name="base_string" value="" id="destination"> 
        </div>
      </div>
      </div>
            </div>
            </div>
          </section>
        </div>
      </div>

      <!-- End of Main Content -->

      
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
  if ($('#password').val() == $('#rpassword').val()){

      $('#message').html('<h5>{!! __('Password is Matched') !!}</h5>').css('color', 'green');
      // $('#sub_button').prop('disabled' , 'false');
      document.getElementById("sub_button").disabled = false;
  } else 
  {
    $('#message').html('<h5>{!! __('Password not matched') !!}</h5>').css('color', 'red');
    // $('#sub_button').prop('disabled' , 'true');
      document.getElementById("sub_button").disabled = true;

  }

});

  </script>
  <!-- Page level plugins -->
  <!-- <script src="{{url('frontend/js/Chart.min.js')}}"></script> -->

  <!-- Page level custom scripts -->
  <!-- <script src="{{url('frontend/js/chart-area-demo.js')}}"></script> -->
  <!-- <script src="{{url('frontend/js/chart-pie-demo.js')}}"></script> -->

@endsection
