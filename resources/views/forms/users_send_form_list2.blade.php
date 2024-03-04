@extends('admin.client.client_app')
@section('content')
<style>
  #act-msg {
	display:none; 
	margin-left:5px;
  }
</style>
<div style="margin-left:30px">
<button class="btn btn-primary" id="send-email" style="margin-bottom:20px;">Add and Send Email</button>
<span id="act-msg">Please wait while request is processed...</span>
<table class="table">
  <thead>
    <tr>
      <th scope="col">Email </th>
	        <th scope="col">Form Links </th>

    </tr>
  </thead>
  <tbody>
    <?php foreach ($form_user_list as $form_info): ?>
    <tr>
      <td>{{ $form_info->user_email }}</td>
	  <td><a href="{{ url('Forms/ExtUserForm/'.$form_info->form_link) }}"> Open</a></td>
    </tr>
	<?php endforeach; ?>
  </tbody>
</table>
<div>
<textarea rows="4" cols="50" id="email-list" name="email-list"></textarea>
</div>
<div>

</div>
<script>
$(document).ready(function (){

	$('#send-email').click(function(){
		var emails;	
		
		emails = $('#email-list').val();
		
		emails = emails.split(',');
		
		for (i = 0; i < emails.length; i++) {
			//console.log(emails[i]);
			
			var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			if (re.test(String(emails[i].trim()).toLowerCase())) {
				//console.log('t');	
			}
			else {	
				//alert("Failed. "+emails[i]+" is not valid email");
				swal("Invalid Email", emails[i]+" is not valid email. Please enter email in correct format", "error");
				return 0;
			}
			
		}
		
		var post_data                 = {};
		
		post_data['_token']           = '{{csrf_token()}}';
		post_data['emails']           = emails;
		post_data['subform_id']       = {{ $subform_id }};
		post_data['client_id']        = {{ Auth::user()->client_id }}; 
		post_data['parent_form_id']   = {{ $parent_form_id }};
		
		$('#act-msg').show();
		
		$.ajax({
			url:'{{ route('assign_subforms_to_external_users') }}',
			method:'post',
			data:post_data,
			success: function (response) {
				
				//response = JSON.parse(response);
				//console.log(response);
				//console.log(response.msg);
				$('#act-msg').hide();
				swal('Send Sub-Form', response.msg, status);
				
				setTimeout( function () {
							location.reload();
						}, 4000 );
				
				//console.log(response);
			}
		});
		
		
		
	});
});
</script>
@endsection