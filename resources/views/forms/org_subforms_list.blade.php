@extends(($user_type=='admin')?('admin.layouts.admin_app'):('admin.client.client_app'))
@section('content')
<style>
    .row-btn {
        margin-bottom:10px;
        display:flex;
        flex-direction:row;
        
    justify-content: flex-end;
    }
    .expired {
        color:#d73b3b;
    }
</style>
<div class="container">
    <h3 class="tile-title">User Forms {{app('request')->input('ext_user_only')?'(External Users Only)':'(Internal and External Users)'}}</h3>
    <div class="row-btn">
        <button class="btn btn-primary" data-toggle="modal" data-target="#myModal">Send Link to External Users</button>
    </div>
</div>
<div style="margin-left:30px; overflow-x: scroll;background-color: #fff;">
<div class="container-fluid">

<table id="forms-list" class="table sending_table big-table-width">
  <thead class="back_blue">
    <tr>
      <th>Sr No.</th>
      <th scope="col">Email </th>
      <th>User Type</th>
      <th>Assessment Form</th>
      <th>Sent Time</th>
      <th>Total Days</th>
      <th>Remaining Days</th>      
      <th>Expiry Time</th>
      <th>Submitted</th>
      <th>Unlock</th>
      <th>Change Access</th>
	  <th scope="col">Form Links </th>
    </tr>
  </thead>
  <tbody style="background-color: #fff;">
    <?php $i = 0; $form_link = ''; 
          $forms = 'Forms';
    ?>
    <?php foreach ($form_user_list as $form_info):
        if(isset($form_info->internal))
        {
            $form_link = $form_info->form_link_id;
            $url = $forms.'/CompanyUserForm/'.$form_link;
            $user_type = 'Organization User';
            $lu_utype  = 'in';
        }
        
        if(isset($form_info->external))
        {
            $form_link = $form_info->form_link;
            $url = $forms.'/ExtUserForm/'.$form_link;
            $user_type = 'External User';
            $lu_utype  = 'ex';
            
        }        
    ?>        
        
    <tr>
      <td>{{ $i + 1 }}</td>
      <td>{{ isset($form_info->email)?($form_info->email):($form_info->user_email) }}</td>
      <td>{{ $user_type }}</td>
      <td>{{ $form_info->title }}</td>
      <td>{{ $form_info->created }}</td>
      <?php
        //$now = time(); // or your date as well
        $created = strtotime($form_info->uf_created);
        $expiry = strtotime($form_info->uf_expiry_time);
        $datediff = $expiry - $created;
        $total_days  = round($datediff / (60 * 60 * 24));
      ?>      
      <td>{{ $total_days }}</td>
      <?php
        $now = time(); // or your date as well
        $expiry = strtotime($form_info->uf_expiry_time);
        $datediff = $expiry - $now;
        $rem_days  = round($datediff / (60 * 60 * 24));
        $expired = '';
        if ($rem_days < 0)
            $expired = 'expired';
      ?>       
      <td><span class="{{$expired}}">{{$rem_days }}</span></td>
      <td>{{ $form_info->expiry_time }}</td> 
      <td><span style="color:#<?php echo ($form_info->is_locked)?('7bca94'):('f26924'); ?>">{{($form_info->is_locked)?('Yes'):('No') }}</span></td>
      <td><button class="unlock-form btn btn-primary" type="{{$lu_utype}}" link="{{$form_link}}" <?php echo ($form_info->is_locked)?(""):("disabled") ?>>Unlock</button></td>
      <td><button class="change-access btn btn-<?php echo ($form_info->is_accessible)?("danger"):("success") ?>" type="{{$lu_utype}}" link="{{$form_link}}" action="<?php echo ($form_info->is_accessible)?(0):(1) ?>" ><?php echo ($form_info->is_accessible)?("Remove"):("Allow") ?></button></td>
	  <td><a class="{{$expired}}" href="{{ url($url) }}" target="_blank"> Open</a></td>
    </tr>
    <?php $i++; ?>
	<?php endforeach; ?>
  </tbody>
</table>
</div>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel">Separate emails with comma (,) or new line (by pressing Enter key) for multiple emails</h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      <div class="modal-body">
        <textarea id="email-list" class="form-control"></textarea>
      </div>
      <div class="modal-footer">
        <span id="wait-msg" class="text-primary" style="display:none">Please wait while request is being processed...</span>  
        <button type="button" id="send-email" class="btn btn-primary">
            <div class="spinner-border text-light" id="spinner" role="status" style="display:none">
              <span class="sr-only">Loading...</span>
            </div>
            <span id="send-email-span">Send Email</span>
        </button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
$(document).ready(function (){
    $('#forms-list').DataTable();
    
	$('#send-email').click(function(){
		var emails;	
		emails = $('#email-list').val();
        var new_line_match = /\r|\n/.exec(emails);
        if (new_line_match) {
            console.log('new line pattern');
		    emails = emails.split('\n');
		    for (i = 0; i < emails.length; i++) {
		        var comma_emails = emails[i].split(',');
		        if (comma_emails.length > 1) {
		            for (j = 0; j < comma_emails.length; j++) {
		                if (j) {
		                    emails.splice(i,0,comma_emails[j]);
		                }
		                else {
		                    emails.splice(i,1,comma_emails[j]);
		                }
		            }
		        }
		    }
		 }
        else {

		    emails = emails.split(',');
        }
        
        var emails = emails.filter(function(el) { return el; });
		
        //console.log(emails);

		for (i = 0; i < emails.length; i++) {
			//console.log(emails[i]);
			
			var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			if (re.test(String(emails[i].trim()).toLowerCase())) {
				console.log('t');	
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
		
		
		$.ajax({
			url:'{{ route('assign_subforms_to_external_users') }}',
			method:'post',
			beforeSend:function () {
			    $('#send-email').prop('disabled', true);
			    $('#send-email-span').hide();
			    $('#wait-msg').show();
			    $('#spinner').show();
			},
			data:post_data,
			success: function (response) {
				//response = JSON.parse(response);
				//console.log(response);
				//console.log(response.msg);

				$('#send-email').prop('disabled', false);
			    $('#send-email-span').show();
			    $('#wait-msg').hide();
			    $('#spinner').hide();				
                $('#myModal').modal('hide')
				
				$('#act-msg').hide();
                if (response.status == 'success') {
				swal('Sub-Form(s) Sent', response.msg, response.status);
    				setTimeout( function () {
    							location.reload();
    						}, 4000 );				    
				}
				else if (response.status == 'fail') {
				    response.status = 'error';
				    swal('Error', response.msg, response.status);
				}
				else {
				    swal('Error', 'Something went wrong. Please try again later', 'error');
				}
				


			}
		});
		
	});    
	

	
	$('.unlock-form').click(function(){
	    
		var post_data                 = {};
		post_data['_token']           = '{{csrf_token()}}';		    
	    post_data['action']           = $(this).attr('action');
	    post_data['user_type']        = $(this).attr('type');
	    post_data['link']             = $(this).attr('link');
	 
		$.ajax({
			url:'{{ route('unlock_form') }}',
			method:'post',
			data:post_data,
			beforeSend:function () {

			},
			data:post_data,
			success: function (response) {
    			location.reload();
			}
		});		    
	});

	$('.change-access').click(function(){
	    
		var post_data                 = {};
		post_data['_token']           = '{{csrf_token()}}';		    
	    post_data['action']           = $(this).attr('action');
	    post_data['user_type']        = $(this).attr('type');
	    post_data['link']             = $(this).attr('link');
	 
		$.ajax({
			url:'{{ route('change_form_access') }}',
			method:'post',
			data:post_data,
			beforeSend:function () {

			},
			data:post_data,
			success: function (response) {
    			location.reload();
			}
		});		    
	});

	
    
    
});
</script>
@endsection