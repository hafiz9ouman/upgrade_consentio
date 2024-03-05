
@extends( 'admin.layouts.admin_app' )

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


@if (session('alert'))
    <div class="alert alert-danger">
        {{ session('alert') }}
    </div>
@endif
	<!--</div>-->
<div class="row">

	<div class="col-md-12">

		<form class="form-horizontal" method="POST" action="{{ url('update_evaluation') }}">

			{{ csrf_field() }}

			<div class="tile">

				<h3 class="tile-title">Update Evaluation Rating</h3>
					@foreach($data as $val)
						<div class="row">
						<div class="col-sm-6 col-md-4">
                			<input id="id"   type="hidden" class="form-control" name="id"  value="{{$val->id}}">
										<div class="form-group">
											<label class="form-control-label">Assessment</label>
											<input id="assessment" type="text"   class="form-control" name="assessment" value="{{$val->assessment}}" required autofocus disabled>
	               		</div>
			               <div class="form-group">
											<label class="form-control-label">Rating</label>
											<input id="rating" type="text"   class="form-control" name="rating" value="{{$val->rating}}" required autofocus disabled>
	               		</div>
				       		  <div class="form-group">
											<label class="form-control-label">Background Color</label>
											<input id="color" type="text"   class="form-control" name="color" value="{{$val->color}}" required autofocus>
	               		</div>
                     <div class="form-group">
											<label class="form-control-label">Text Color</label>
											<input id="text_color" type="text"   class="form-control" name="text_color" value="{{$val->text_color}}" required autofocus>
	               		</div>
						</div>
					@endforeach
					<div class="tile-footer col-sm-12 text-right">

						<button type="button" class="btn btn-primary" id="updateButton">Update</button>
						<a href="{{ url('evaluation_rating') }}" class="btn btn-secondary">Cancel</a>

					</div>

				</form>

			</div>

		</form>

	</div>

</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>


<script>
$(document).ready(function () {
    // Attach a click event handler to the "Update" button
    $('#updateButton').click(function () {
        // Display a SweetAlert confirmation dialog
        Swal.fire({
            title: 'Are you sure?',
            text: 'You are about to update the evaluation rating.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, update it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // If the user confirms, submit the form
                Swal.fire('Evaluation Rating is Updated', '', 'success');
                setTimeout( function () {
    							$('form').submit();
    						}, 2000 );
                
            }
        });
    });
});
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