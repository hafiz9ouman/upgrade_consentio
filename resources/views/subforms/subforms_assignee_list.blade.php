@extends('admin.client.client_app')
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
		.align_button {
			display: flex;
			justify-content: space-between;
		}
	</style>
	<link href="{{ url('frontend/css/jquery.mswitch.css')}}"  rel="stylesheet" type="text/css">
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

        @section('page_title')
			@if (Request::is('audit/*'))
				{{ __('AUDIT FORM ASSIGNEES') }}
			@else
				{{ __('SAR FORM ASSIGNEES') }}
			@endif
        @endsection

          <div class="over_main_div">

              <button class="btn btn-primary" id="assign" style="margin-top: 16px;float:right; margin-right: 22px;">{{ __('Assign') }} / {{ __('Unassign') }}</button>
      </h3>
            <table class="table table-striped text-center paginated" >
    <thead>
        <tr style = "text-transform:uppercase;">
        	  <th scope="col">{{ __('Sr No') }}.</th>
		      <th scope="col">{{ __('User') }}</th>
		      <th scope="col">{{ __('Email') }}</th>
		      <th scope="col">{{ __('Image') }}</th>
		      <th scope="col">{{ __('Number of Forms Assigned') }}</th>
		      <th width="120" scope="col">{{ __('Select') }}</th>
     	</tr>
     </thead>
     <tbody>
     	
  	<?php foreach ($company_users as $index => $user): ?>

    <tr style="">
        <td>{{$index+1}}</td>
    <td>{!! $user->id==Auth::user()->id?'<i class="fas fa-fw fa-user"></i>':'' !!} {{ $user->name }}</td>
    <td>{{ $user->email }}</td>
    <td>
        @if($user->image_name=="")
								<img id="blah" src="{{url('dummy.jpg')}}" style=" border-radius: 14%;height: 40px;width: 48px;" />
								@else
								<img id="blah" class="img-fluid" src="<?php 
										 if($user->role == 2){echo url("img/$user->image_name"); }
											 else{ echo url("public/img2/$user->image_name");}
															           ?>
																	  " name="profile_image" style="border-radius: 14%;height: 40px;width: 48px;">
								@endif
    </td>
    <td>{{$user->forms_count}}</td>
    <td>
		<input type="checkbox" value="{{ $user->id}}" class="assign-users" id="user-{{ $user->id}}" <?php if (in_array($user->id, $assigned_users)) echo 'checked'; ?> >
	  </td>

    </tr>

	<?php endforeach; ?>


     </tbody>
      
            </table>
            <div class="table_footer">
              <p>{{ __('Showing 1 to 9 of 9 entries')}}</p>
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



<!-- </div> -->

<script>
	$(document).ready(function(){
	    
	    $('#sb-assignees-table').DataTable();
		
		var asgn_ids = [];
		
		var del_ids  = [];
		
// 	    $('.assign-users').each(function(){
// 	        var val = $(this).val();
// 			if ($(this).prop('checked')){
// 				if (asgn_ids.indexOf(val) == -1) {
// 					asgn_ids.push(val);
// 				}
// 			}
// 			else {
// 			    del_ids.push(val);
// 			}
// 	    });
		
		$('.assign-users').change(function(){

			var id = $(this).val();

			if ($(this).prop('checked')){
								
				if (asgn_ids.indexOf(id) == -1) {
					asgn_ids.push(id);
				}
				else {

				}

				var dind = del_ids.indexOf(id);
				if (dind > -1) {
					del_ids.splice(dind, 1);
				}					
				
			}
			else {
				var aind = asgn_ids.indexOf(id);
				if (aind > -1) {
					asgn_ids.splice(aind, 1);
				}
				
				if (del_ids.indexOf(id) == -1) {
					del_ids.push(id);
				}
				else {

				}					
				
				
			}
			
			console.log(asgn_ids);
			console.log(del_ids);
			
			//$('#assign').prop('disabled', (ids.length > 0)?(false):(true));										
		});
		
		$('#assign').click(function(){
		    
		    var button_text = $(this).text();
			
			var post_data        = {};
			post_data['asgn_ids']      = asgn_ids;
			post_data['del_ids']       = del_ids;
			post_data['_token']        = '{{csrf_token()}}';
			post_data['subform_id']    = {{$subform_info->id}};
			post_data['subform_title'] = '{{$subform_info->title}}';
			
			if (asgn_ids.length > 0 || del_ids.length > 0) {
				$.ajax({
					url:'{{route('assign_subform_to_users')}}',
					method:'POST',
					/*
					headers: {
						'X-CSRF-TOKEN': $( 'meta[name="csrf-token"]' ).attr( 'content' )
					},
					*/
					data: post_data,
					beforeSend:function(){
					    $(this).prop('disabled', true);
					    $('#assign').text('Processing...')
					},
					success: function(response) {
						//console.log(response);
						//alert('Sub-form assigned to user');
						
						$('#assign').text(button_text);
						
						if (response.status == 'success') {
							swal("{!! __('Success') !!}", response.msg, 'success');
						}
						else {
							swal("{!! __('Something went wrong') !!}", "{!! __('Please try again later') !!}", 'error');
						}
					}
				});
			}
			

		});

		
	});
</script>

@endsection