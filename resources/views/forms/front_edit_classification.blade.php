@extends('admin.client.client_app')

@section( 'content' )
  <style>
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

      #size {
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

      input:checked+.slider {
          background-color: #2196F3;
      }

      input:focus+.slider {
          box-shadow: 0 0 1px #2196F3;
      }

      input:checked+.slider:before {
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
  @section('page_title')
  {{ __('Edit Data Classification') }}
  @endsection
  @if (session('alert'))
  <div class="alert alert-danger">
      {{ session('alert') }}
  </div>
  @endif
<div class="row">
  <div class="col-md-10 offset-1 card shadow p-4">
    <form class="form-horizontal" method="POST" action="{{ url('/front/edit-classification/'. $form->id) }}" enctype="multipart/form-data">
      {{ csrf_field() }}

      <div class="row">

        <div class="col-sm-6 col-md-4">
          <input id="id" type="hidden" class="form-control" name="id" value="{{ $form->id }}" required autofocus>
          <div class="form-group">
            <label class="form-control-label">Classification Name English</label>
            <input id="name" type="text" class="form-control" name="classification_name_en" value="{{ $form->classification_name_en }}" required autofocus>
          </div>

          <div class="form-group">
            <label class="form-control-label">Nom de la classification French </label>
            <input id="name_fr" type="text" class="form-control" name="classification_name_fr" value="{{ $form->classification_name_fr }}" required autofocus>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="tile-footer col-sm-12 text-right">
          <a href="{{ url('front/data-classification') }}" class="btn btn-secondary bg-dark p-2 px-5" style="border-radius:35px;"><b>{{__('Cancel')}}</b></a>					
          <button type="submit" class="btn btn-info p-2 px-5 ml-1" style="border-radius:35px;background:#0f75bd;">{{__('Update')}}</button>
        </div>
      </div>

    </form>
  </div>
</div>

<script>
$('#add_images').click(function() {
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

$('#password, #rpassword').on('keyup', function() {
  if ($('#password').val() == $('#rpassword').val()) {
    $('#message').html('<h5>Password is Matched</h5>').css('color', 'green');
  } else{
    $('#message').html('<h5>Password is not Matched</h5>').css('color', 'red');
  }
});
</script>



@endsection