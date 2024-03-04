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
    /*
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
    */

    .uploadcare--jcrop-holder>div>div, #preview {
          /*border-radius: 50%;*/
        }

    .uploadcare--menu__item_tab_facebook, .uploadcare--menu__item_tab_gdrive, .uploadcare--menu__item_tab_gphotos, .uploadcare--menu__item_tab_dropbox, .uploadcare--menu__item_tab_instagram, .uploadcare--menu__item_tab_evernote, .uploadcare--menu__item_tab_flickr, .uploadcare--menu__item_tab_onedrive, .uploadcare--dialog__powered-by {
    display: none !important;
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
  .slider.round {
    border-radius: 34px;
  }

  .slider.round:before {
    border-radius: 50%;
  }
  .max-spec {
      font-size:14px;
  }
</style>

<div class="app-title">
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i>
    </li>
    <li class="breadcrumb-item"><a href="{{url('/Forms/AdminFormsList')}}">Form List </a>
    </li>
    <li class="breadcrumb-item"><a href="{{url('Forms/Add-new-form')}}">Add New Form</a>
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

<section class="forms">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header d-flex align-items-center">
            <h3 class="h4">Add New Form</h3>
          </div>
          <div class="card-body">
            <div class="card-body-form">
              <form class="form-horizontal" method="POST" action="{{ url('/Forms/Add-new-form') }}" enctype="multipart/form-data" autocomplete="off">
                {{ csrf_field() }}
                <div class="row">
                  <div class="col-md-6 py-2">
                    <label class="form-control-label">Form Name English</label>
                    <input id="title" type="text" class="form-control" name="title" value="{{ old('title') }}" autocomplete="off" required autofocus> 
                  </div>
                  <div class="col-md-6  py-2">
                    <label class="form-control-label">Form Name French</label>
                    <input id="title_fr" type="text" class="form-control" name="title_fr" value="{{ old('title_fr') }}" autocomplete="off" autocomplete="nope" required>
                  </div>
                  <div class="col-md-6  py-2">
                    <label class="form-control-label">Type</label>
                    <select class="form-control" name="type" onchange="getquestiongroups(this.value)">
                      <option value="assessment">Assessment</option>
                      <option value="sar">SAR</option>
                      <option value="audit">Audit</option>
                    </select>
                  </div>
                  <div class="col-md-6  py-2" id="question_groups"></div>
                </div>
                <div class="card-footer" style="">
                  <div class="col-sm-12 text-left">
                      <a href="{{url('/Forms/AdminFormsList')}}" class="btn btn-sm btn-secondary">@lang('general.cancel')</a>
                      <button type="submit" id="sub_button" class="btn btn-sm btn-primary">@lang('general.save') </button>
                  </div>
                </div> 
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script>
  function getquestiongroups(val) {
    if (val == "audit") {
      $.ajax({
          url: "{{ route('group_list') }}",
          method: 'GET',
          success: function(response) {
              console.log(response);
              let html = '<label class="form-control-label">Question Group</label> <select class="form-control" name="group_id" >';
              $.each(response, function(i, data) {
                  html += `<option value="${data.id}">${data.group_name}</option>`;
              });
              html += ` </select>`;
              $('#question_groups').html(html);
          }
      }); 
    }else{
      $('#question_groups').html("");
    }
  }
</script>
@endpush