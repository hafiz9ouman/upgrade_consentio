@extends (($user_type == 'admin')?('admin.layouts.admin_app'):('admin.client.client_app'))

@section('content')

<?php if ($user_type == 'admin'): ?>
<div class="app-title">

  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i>
    </li>
    <li class="breadcrumb-item"><a href="{{ url('Forms/AdminFormsList/audit') }}">Manage Audit Forms </a>
    </li>
    <li class="breadcrumb-item"><a href="{{ url('Audit/Assignees/'.$form_id) }}">Form Assignees </a>
    </li>   
  </ul>
</div>
<?php endif; ?>
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title">Form Assignees for <span style="color:#0f75bd">{{$selected_form->title}}</span>
      </h3>
      <div class="table-responsive">
	  	  <button class="btn btn-primary"  onclick="getall_checkbox()" id="assign">Assign / Unassign</button>
        <table class="table" id="assignees-table">
          <thead class="back_blue">
    <tr>
<!--<th><input type="checkbox" id="checkAll"/></th>-->
      <!--<th scope="col">Select</th>-->
      <th>Select</th>

      <th>Organization</th>

    </tr>

  </thead>

  <tbody>
	
  	<?php foreach ($client_list as $client): ?>

    <tr>
		@if(DB::table('sub_forms')->where('parent_form_id', $form_id)->where('client_id', $client->id)->count() > 0)
	
			<td><input type="checkbox" onclick="checkfu({{$client->id}})" class="cbox" name="row{{$client->id}}" id="row{{$client->id}}" value="{{$client->id}}" <?php if (in_array($client->id, $assigned_client_ids)) echo 'checked'; ?> disabled></td>
			
			<td>{{ $client->company }}</td>
		@else

			<td><input type="checkbox" onclick="checkfu({{$client->id}})" class="cbox" name="row{{$client->id}}" id="row{{$client->id}}" value="{{$client->id}}" <?php if (in_array($client->id, $assigned_client_ids)) echo 'checked'; ?> ></td>
			
			<td>{{ $client->company }}</td>

	  	@endif

    </tr>

	<?php endforeach; ?>

  </tbody>

</table>

</div>
    </div>
  </div>
</div>
<script>
	var asgn_ids = [];
		
	var del_ids  = [];

	$(document).ready(function(){
	    
	   $('#assignees-table').DataTable({
	       "pageLength": 10
	   });
	});
	

function checkfu(id){
	
	
	if ($('#row'+id).prop('checked')){
								
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
			
			$('#asgn_ids').val(asgn_ids);
			$('#del_ids').val(del_ids);
			console.log("asg->"+asgn_ids);
			console.log("del->"+del_ids);
		
			$('#asgn_ids').val(asgn_ids);
			$('#del_ids').val(del_ids);




}
	 function getall_checkbox(){
        checkbox = [];
        $("input:checkbox[class=cbox]:checked").each(function () {
            checkbox.push(Number($(this).val()));
        });

		<?php foreach ($assigned_client_ids as $client_id): ?>
			if (checkbox.indexOf(<?php  echo $client_id; ?>) == -1) {
				checkbox.push(Number(<?php echo $client_id; ?>));
			}		
		<?php endforeach; ?>
		
        var post_data        = {};
		post_data['ids']     = $('#asgn_ids').val();
		post_data['_token']  = '{{csrf_token()}}';
		post_data['form_id'] = {{$form_id}};
		post_data['del_ids'] = $('#del_ids').val();
		post_data['asg_ids'] = $('#asgn_ids').val();
		
		$.ajax({
			url:'{{route('assign_form_to_client')}}',
			method:'POST',
			// headers: {
			// 	'X-CSRF-TOKEN': $( 'meta[name="csrf-token"]' ).attr( 'content' )
			// },
			data: post_data,
			success: function(response) {
				//console.log(response);
			
				swal('Information Updated', 'Forms assigned/un-assigned to company', 'success');
				setTimeout(function () { location.reload(); }, 1000);
			}
		});
    }
</script>
<input type="hidden" id="asgn_ids">
<input type="hidden" id="del_ids">
@endsection