@extends (('admin.client.client_app'))

@section('content')

    
    <style>
        #blah:hover {
        transform: scale(2.5);
        transition: 0.5s;
    }
    </style>





<div class="row" style="margin-left:20px">
  <div class="col-md-12">
    <div class="tile" >
      <h3 class="tile-title" style="margin-bottom:33px;">{{$subform_info->title}} Assignes
              <button class="btn btn-primary" id="assign" style="float:right; margin-right: 22px;">Assign / Unassign</button>
      </h3>
      <div>
      </div>


      <div class="table-responsive">
	  	  
        <table class="table" id="sb-assignees-table">
          <thead class="back_blue">
    <tr>
       <th scope="col">Sr No.</th>
      <th scope="col">User</th>
      <th scope="col">Email</th>
      <th scope="col">Image</th>
      <th scope="col">Num. of Forms Assigned</th>
      <th width="120" scope="col">Select</th>
       <!--<th scope="col">Select</th>-->

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
								<img id="blah" src="{{url('dummy.jpg')}}" style="    height: 30px; width: 40px;" />
								@else
								<img id="blah" class="img-fluid" src="<?php echo url("img/$user->image_name");?>" name="profile_image" style="     height: 30px; width: 40px;">
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

</div>
    </div>
  </div>
</div>
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
							swal('Success', response.msg, 'success');
						}
						else {
							swal('Something went wrong', 'Please try again later', 'error');
						}
					}
				});
			}
			

		});

		
	});
</script>

@endsection