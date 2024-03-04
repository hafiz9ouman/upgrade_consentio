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
    #forms-list_wrapper {
        white-space: nowrap;
        padding-top: 15px;
    }
</style>
<!-- <link href="{{ url('frontend/css/jquery.mswitch.css')}}"  rel="stylesheet" type="text/css">
<div class="container-fluid">
    <h3 class="tile-title">{{ __('In-Completed SAR Forms') }}</h3>
</div>
<div style="margin-left:30px; overflow-x: scroll;background-color: #fff;">
<div class="container-fluid">

<table id="forms-list" class="table sending_table big-table-width">
  <thead class="back_blue">
    <tr>
      <th scope="col">Form Links </th>
      <th scope="col">Email </th>
      <th>User Type</th>
      <th>Form Name</th>
      <th>Sent Date</th>
      <th>Total Days</th>
      <th>Remaining Days</th>      
      <th>Expiry Date</th>
      <th>Submission Status</th>
      <th>Lock/Unlock</th>
      <th>Change Access</th>
	  
    </tr>
  </thead>
  <tbody style="background-color: #fff;">
    <?php $i = 0; $form_link = ''; 
          $forms = 'Forms';
          if (Request::segment(1) =='SAR')
          {
              $forms = 'SAR';
          }
    ?>
    <?php foreach ($form_user_list as $form_info):
        if($form_info->is_locked == 1)
        {
            continue;
        }
        if(isset($form_info->internal))
        {
            $form_link = $form_info->form_link_id;
            $url = $forms.'/CompanyUserForm/'.$form_link;
            $org_user = __('Organization User');
            $user_type = '<span style="color:#5bc858">'.$org_user.'</span>';
            $lu_utype  = 'in';
        }
        
        if(isset($form_info->external))
        {
            $form_link = $form_info->form_link;
            $url = $forms.'/ExtUserForm/'.$form_link;
            $ex_user = __('External User');
            $user_type = '<span style="color:#f88160">'.$ex_user.'</span>';
            $lu_utype  = 'ex';
            
        }        
    ?>  
     <?php
        $now = time(); 
        $expiry = strtotime($form_info->uf_expiry_time);
        $datediff = $expiry - $now;
        $rem_days  = round($datediff / (60 * 60 * 24));
        $expired = '';
        if ($rem_days < 0)
            $expired = 'expired';
      ?>       
        
    <tr>
      <td><a class="{{$expired}}" href="{{ url($url) }}" target="_blank"> Open</a></td>
      <td>{{ isset($form_info->email)?($form_info->email):($form_info->user_email) }}</td>
      <td>{!! $user_type !!}</td>
      <td>{{ $form_info->title }}</td>
      <td>{{ date('Y-m-d', strtotime($form_info->created)) }}</td>
      <?php
        $created = strtotime($form_info->uf_created);
        $expiry = strtotime($form_info->uf_expiry_time);
        $datediff = $expiry - $created;
        $total_days  = round($datediff / (60 * 60 * 24));
      ?>      
      <td>{{ $total_days }}</td>
           
      <td><span class="{{$expired}}">{{$rem_days }}</span></td>
      <td>{{ date('Y-m-d', strtotime($form_info->uf_expiry_time)) }}</td> 
      <td><span style="color:#<?php echo ($form_info->is_locked)?('7bca94'):('f26924'); ?>">{{($form_info->is_locked)?('Submitted'):('Not Submitted') }}</span></td>
      <td><input type="checkbox" data-toggle="tooltip" title="{{($form_info->is_locked)?('Locked'):('Unlocked')}}" class="unlock-form" value="{{!$form_info->is_locked}}" u-type="{{$lu_utype}}" link="{{$form_link}}"></td>
      <td><button class="change-access btn btn-<?php echo ($form_info->is_accessible)?("danger"):("success") ?>" type="{{$lu_utype}}" link="{{$form_link}}" action="<?php echo ($form_info->is_accessible)?(0):(1) ?>" ><?php echo ($form_info->is_accessible)? __("Remove"): __("Allow") ?></button></td>
    </tr>
    <?php $i++; ?>
	<?php endforeach; ?>
  </tbody>
</table>
</div> -->

<section class="assets_list">
      <div class="main_custom_table">
        <div class="table_filter_section">
          <div class="select_tbl_filter">
            <div class="main_filter_tbl">
              <p>{{ __('Show') }}</p>
              <select>
                <option>10</option>
                <option>20</option>
                <option>30</option>
              </select>
              <p>{{ __('Entries') }}</p>
            </div>
          </div>
        </div>
        <div class="main_table_redisign">
          {{-- <div class="table_breadcrumb">
            <h3></h3>
          </div> --}}
          @section('page_title')
         {{ __('IN-COMPLETED SAR FORMS') }}
          @endsection
          <div class="over_main_div">
            <table class="table table-striped text-center paginated">
              <thead>
                <tr>
                  <th scope="col">{{ __('FORM LINKS') }}</th>
                  <th scope="col">{{ __('EMAIL') }}</th>
                  <th scope="col">{{ __('USER TYPE') }}</th>
                  <th scope="col">{{ __('FORM NAME') }}</th>
                  <th scope="col">{{ __('SENT DATE') }}</th>
                  <th scope="col">{{ __('TOTAL DAYS') }}</th>
                  <th scope="col">{{ __('REMAINING DAYS') }}</th>
                  <th scope="col">{{ __('EXPIRY DATE') }}</th>
                  <th scope="col">{{ __('SUBMISSION') }} <br> {{ __('STATUS') }}</th>
                  <th scope="col">{{ __('LOCK/') }} <br> {{ __('UNLOCK') }}</th>
                  <th scope="col">{{ __('CHNAGE') }}</th>
                </tr>
              </thead>
              <tbody>
                 <?php $i = 0; $form_link = ''; 
          $forms = 'Forms';
          if (Request::segment(1) =='SAR')
          {
              $forms = 'SAR';
          }
    ?>
    <?php foreach ($form_user_list as $key=>$form_info):
        if($form_info->is_locked == 1)
        {
            continue;
        }
        if(isset($form_info->internal))
        {
            $form_link = $form_info->form_link_id;
            $url = $forms.'/CompanyUserForm/'.$form_link;
            $org_user = __('Organization User');
            $user_type = '<span style="color:#5bc858">'.$org_user.'</span>';
            $lu_utype  = 'in';
        }
        
        if(isset($form_info->external))
        {
            $form_link = $form_info->form_link;
            $url = $forms.'/ExtUserForm/'.$form_link;
            $ex_user = __('External User');
            $user_type = '<span style="color:#f88160">'.$ex_user.'</span>';
            $lu_utype  = 'ex';
            
        }        
    ?>  
     <?php
        $now = time(); 
        $expiry = strtotime($form_info->uf_expiry_time);
        $datediff = $expiry - $now;
        $rem_days  = round($datediff / (60 * 60 * 24));
        $expired = '';
        if ($rem_days < 0)
            $expired = 'expired';
      ?>       
        
    <tr>
      <td><a class="{{$expired}} btn rounded_button td_round_btn" href="{{ url($url) }}" target="_blank"> {{ __('Open') }}</a></td>
      <td>{{ isset($form_info->email)?($form_info->email):($form_info->user_email) }}</td>
      <td>{!! $user_type !!}</td>
      <td>{{ $form_info->title }}</td>
      <td>{{ date('Y-m-d', strtotime($form_info->created)) }}</td>
      <?php
        $created = strtotime($form_info->uf_created);
        $expiry = strtotime($form_info->uf_expiry_time);
        $datediff = $expiry - $created;
        $total_days  = round($datediff / (60 * 60 * 24));
      ?>      
      <td>{{ $total_days }}</td>
           
      <td><span class="{{$expired}}">{{$rem_days }}</span></td>
      <td>{{ date('Y-m-d', strtotime($form_info->uf_expiry_time)) }}</td> 
      <td><span style="color:#<?php echo ($form_info->is_locked)?('7bca94'):('f26924'); ?>">{{($form_info->is_locked)? __('Submitted'): __('Not Submitted') }}</span></td>
      <td>
               <label class="switch switch-green">
                    <input type="checkbox"   class="switch-input"  onclick="lock_unlock('the_toggle_button-{{$key}}')"  @if(!$form_info->is_locked) checked="" @endif>
                    <span class="switch-label" data-toggle="tooltip" title="{{($form_info->is_locked)?('Locked'):('Unlocked')}}" data-on="{{ __('on')}}" data-off="{{ __('Off')}}"></span>
                    <span class="switch-handle" data-toggle="tooltip" title="{{($form_info->is_locked)?('Locked'):('Unlocked')}}"></span>
                  </label>

        <input style="display: none;" id="the_toggle_button-{{$key}}" type="checkbox"  title="{{($form_info->is_locked)?('Locked'):('Unlocked')}}" class="unlock-form" value="{{!$form_info->is_locked}}" u-type="{{$lu_utype}}" link="{{$form_link}}">
      </td>

      <td><button class="change-access btn-sm btn btn-<?php echo ($form_info->is_accessible)?("danger"):("success") ?>" type="{{$lu_utype}}" link="{{$form_link}}" action="<?php echo ($form_info->is_accessible)?(0):(1) ?>" ><?php echo ($form_info->is_accessible)? __("Remove"): __("Allow") ?></button></td>
    </tr>
    <?php $i++; ?>
  <?php endforeach; ?> 



                {{-- <tr>
                  <td> 
                    <button type="button" class="btn rounded_button td_round_btn">Open</button>
                  </td>
                  <td>canada@d3cod.com</td>
                  <td>Orgnaization User</td>
                  <td>USER INCIDENT REPORT FORM 1</td>
                  <td>2020-12-08</td>
                  <td>10</td>
                  <td>-123</td>
                  <td>2020-12-18</td>
                  <td>Not Submitted</td>
                  <td>
                      <label class="switch switch-green">
                    <input type="checkbox" class="switch-input" checked>
                    <span class="switch-label" data-on="{{ __('On')}}" data-off="{{ __('Off')}}"></span>
                    <span class="switch-handle"></span>
                  </label>
                  </td>
                </tr> --}}
              </tbody>
            </table>
            <div class="table_footer">
              <p>{{ __('Showing') }} 1 to 9 of 9 {{ __('Entries') }}</p>
              <div class="table_custom_pagination">
                <p class="active_pagination">1</p>
                <p>2</p>
                <p>3</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>


<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel">{{ __('Separate emails with comma (,) or new line (by pressing Enter key) for multiple emails') }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      <div class="modal-body">
        <textarea id="email-list" class="form-control"></textarea>
      </div>
      <div class="modal-footer">
        <span id="wait-msg" class="text-primary" style="display:none">{{ __('Please wait while request is being processed') }}...</span>  
        <button type="button" id="send-email" class="btn btn-primary">
            <div class="spinner-border text-light" id="spinner" role="status" style="display:none">
              <span class="sr-only">{{ __('Loading') }}...</span>
            </div>
            <span id="send-email-span">{{ __('Send Email') }}</span>
        </button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script src="{{url('frontend/js/jquery.mswitch.js')}}"></script>
<script>
function lock_unlock(val){
$('#'+val).click();
// alert(val);

   // if ($(this).is(':checked'))
   //   alert("Checked");
   // else
   //   alert("Unchecked");
}

$(document).ready(function (){
    <?php
        $search_filter = '';
        $search = '';
        if (app('request')->input('search_filter')) 
        {
            $search_parameter = app('request')->input('search_filter');
            $search = '"search": {"search": "'.$search_parameter.'"},';
        }
    ?>

    $('#forms-list').DataTable({
            "order": [[ 3, "desc" ]],
            {!!$search!!}
            "drawCallback": function( settings ) {
                                  
            }            
    });
    
    $('[data-toggle="tooltip"]').tooltip();
     $(".unlock-form:checkbox").mSwitch({
                    onRender:function(elem){
                        if (elem.val() == '1') {
                            $.mSwitch.turnOn(elem);
                        }
                        else {
                            $.mSwitch.turnOff(elem);
                        }
                    },            
                    onTurnOn:function(elem){
                        changeFormLockStatus(elem,0);  
                        elem.attr('data-original-title', 'Unlocked');
                    },
                    onTurnOff:function(elem){
                        changeFormLockStatus(elem,1); 
                        elem.attr('data-original-title', 'Locked');
                    }
                }); 
    function changeFormLockStatus (elem, lockStatus)
    {
		var post_data                 = {};
		
		post_data['_token']           = '{{csrf_token()}}';		    
	    post_data['action']           = elem.attr('action');
	    post_data['user_type']        = elem.attr('u-type');
	    post_data['link']             = elem.attr('link');
	    post_data['lock_status']      = lockStatus;    
	 
		$.ajax({
			url:'{{ route('unlock_form') }}',
			method:'post',
			data:post_data,
			beforeSend:function () {

			},
			data:post_data,
			success: function (response) {
    			swal('Lock Status', response.msg, response.status);
    			var color;
    			var status;
    			if (lockStatus) {
    			    color  = '7bca94';
    			    status = 'Submitted'; 
    			}
    			else {
    			    color  = 'f26924';
    			    status = 'Not Submitted';    			    
    			}
    			
    			var new_lock_status_html = '<span style="color:#'+color+'">'+status+'</span>';
    			elem.parent().parent().prev().html(new_lock_status_html);
          setTimeout(function() 
                           {
      location.reload();  //Refresh page
    }, 900);
			}
		});	
    }        
    
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
	

	
// 	$('.unlock-form').click(function(){
	    
// 		var post_data                 = {};
// 		post_data['_token']           = '{{csrf_token()}}';		    
// 	    post_data['action']           = $(this).attr('action');
// 	    post_data['user_type']        = $(this).attr('type');
// 	    post_data['link']             = $(this).attr('link');
	 
// 		$.ajax({
// 			url:'{{ route('unlock_form') }}',
// 			method:'post',
// 			data:post_data,
// 			beforeSend:function () {

// 			},
// 			data:post_data,
// 			success: function (response) {
//     			location.reload();
// 			}
// 		});		    
// 	});

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
			    
                swal({
                  title: "Form Access Status Updated",
                  text: "Form Access Changed!",
                  type: "info",
                  showCancelButton: false,
                  confirmButtonClass: "btn-primary",
                  confirmButtonText: "OK!",
                  closeOnConfirm: true,
                },
                function(){
                  //swal("Deleted!", "Your imaginary file has been deleted.", "success");
    			location.reload();
                });			    
			    
			    
			    
			}
		});		    
	});

	
    
    
});
</script>
@endsection