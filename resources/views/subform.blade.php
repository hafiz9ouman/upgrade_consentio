@extends(($user_type=='admin')?('admin.layouts.admin_app'):('admin.client.client_app'))

@section('content')
<?php if ($user_type == 'admin'): ?>

            <style>
                table {
                    white-space: nowrap;
                }
            </style>



<div class="app-title">
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i>
		</li>
		<li class="breadcrumb-item"><a href="{{route('admin_forms_list')}}">Manage Assessment Forms</a>
		</li>
		<li class="breadcrumb-item"><a href="{{ route('subforms_list', ['id' => $form_info->id]) }}">Show Sub Forms</a>
		</li>		
	</ul>
</div>
<?php endif; ?>
<style>
  #sub-form-area {
      width:100%;
  }
  .sub-form {
    margin-top:20px;
  }
  .row.sub-form{
    margin-left: 0;
  }
  #sub-form-area .sub-form{
    display: flex;
    flex-direction: row;
        align-items: baseline;
  }
  #sub-form-area .sub-form input,#sub-form-area .sub-form select{
    max-width: 80%;
    min-width:30%;
    margin-right: 2%;
  }
  #sub-form-area .sub-form button{
    width: 15%;
  }
  .form-control:focus{
    outline: 0;
    box-shadow: none;
  }
  .btn-primary.focus, .btn-primary:focus,.btn-primary:not(:disabled):not(.disabled).active:focus, .btn-primary:not(:disabled):not(.disabled):active:focus, .show>.btn-primary.dropdown-toggle:focus{
    box-shadow: none;
    outline: 0;
  }
  @media screen and (max-width: 998px){
    #sub-form-area {
      width:100%;
    }
    #sub-form-area .sub-form button{
    width: auto;
    }
  }
  #act-msg {
	display:none; 
  }
  .fs-14 {
      font-size:14px;
  }
  .fs-12 {
      font-size:12px;
  }
  .zdd {
     width: 100% !important; 
  }
</style>
<div class="modal" id="edit-modal">
    <div class="modal-dialog">
      <div class="modal-content">
      
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Edit Subform</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        
        <!-- Modal body -->
        <div class="modal-body">
          <label for="usr">Name :</label>
            <input type="text" name="sb-name" id="sb-name" sb-id="" class="form-control zdd">
        </div>
        
        <!-- Modal footer -->
        <div class="modal-footer">
        <div style="text-align:center;">
          <button id="edit-form" type="button" class="btn btn-primary">Edit</button>
          </div>
        </div>
        
      </div>
    </div>
  </div>
<h3 class="tile-title" style="margin-left: 25px;">Sub Forms <button class="btn btn-primary" id="add" style="float:right; margin-right: 17px;">Add Sub Form</button></h3>
<div class="" style="margin-left:30px; ">
  <?php if ($user_type=='client'): ?>  
<span id='act-msg'><i>Please wait while your action is processed</i></span>
<?php endif; ?>
<form method="POST" action="{{ route('gen_subform') }}">
  {{ csrf_field() }}
  <input type="hidden" name="form-id" value="">
  <br>
  <!-- <button type="submit" class="btn btn-primary">Submit</button> -->
</form>
<?php
      $options_list  = '<option value="all">All</option>';

	
      foreach ($client_users as $name)
      {
          $options_list .= '<option value="'.$name.'">'.$name.'</option>';
      }
?>
<div id="sub-form-area"></div>
<?php if ($user_type == 'admin'): ?>
<div class="row">
<div class="col-md-12">
<div class="tile">
<h3  class="tile-title">SubForms List</h3>
<div class="table-responsive small-table-width">
<?php endif; ?>
     <table class="table subformTwo" id="subforms">
         <?php if ($user_type == 'admin'): ?>
		<thead class="back_blue">
		<?php else: ?>
		<thead>
        <?php  endif; ?>
          @if (!empty($sub_forms))
          <tr>
            <th colspan="{{(Auth::user()->role == 2 || Auth::user()->user_type == 1)?(6):(3)}}" style="text-align:center">{{$form_info->title}} Sub Forms</th>  
          </tr>
          @endif
          <tr>
            <th scope="col">Form Name</th>
			@if (Auth::user()->role == 2 || Auth::user()->user_type == 1): 
			<th scope="col">External Users</th>			
			<th scope="col">Assign to Internal Users</th>			
            <th scope="col">Sub Form Users</th>
            <th scope="col" style="width:100px">Actions</th>
			@endif
            <!--<th scope="col">External Users Forms</th>-->
          </tr>
        </thead>
        <tbody>
    @if (!empty($sub_forms))
      @for ($i = 0; $i < count($sub_forms); $i++)          
          <tr>
            <td><span class="fs-14">{{ $sub_forms[$i]->title }}</span></td> 
            @php 
				$ex_link_title = '<i class="fas fa-link"></i> Open / <i class="fas fa-arrow-right"></i> Send';
				$in_link_title = 'Send / Show Forms';
			@endphp
			@if (Auth::user()->role == 2 || Auth::user()->user_type == 1):
			<td>
			    <?php
			        $count = 0;
			        if (isset($sub_forms[$i]->external_users_count))
			            $count =  $sub_forms[$i]->external_users_count;
			        //echo $count;
			        
			        if ($count):
			    ?>
			        <a class="fs-14" href="{{url('/Forms/OrgSubFormsList/'.$sub_forms[$i]->id.'/?ext_user_only=1')}}"> <span style="vertical-align: middle;">{{$count}}</span> <img src="{{url('image/arrow_right.png')}}"></a>
			    <?php
			        else:
			    ?>
			        <span class="fs-14">0</span>
			    <?php endif; ?>
			</td>			
			
			<td><a class="fs-14" href="{{url('Forms/SubFormAssignees/'.$sub_forms[$i]->id)}}">  <span style="vertical-align: middle;"><?php echo (isset($sub_forms[$i]->internal_users_count) && !empty($sub_forms[$i]->internal_users_count))?($sub_forms[$i]->internal_users_count):(0); ?></span> <img src="{{url('image/arrow_right.png')}}"></a></td>
			<!--<td><a href="{{url('/Forms/CompanyUsersSubFormsList/'.$sub_forms[$i]->id)}}" > <i class="far fa-eye"></i> {{ $in_link_title }}</a></td>	-->
			<td><a class="fs-14" href="{{url('/Forms/OrgSubFormsList/'.$sub_forms[$i]->id)}}">  Show / Send Forms</a></td>
			<td><a data-toggle="modal" data-target="#edit-modal" class="fs-14 edit-sb" sb-id="{{$sub_forms[$i]->id}}" sb-name="{{ $sub_forms[$i]->title }}" href="#"> <i class="fas fa-pencil-alt"></i></a> <a class="fs-14 delete-sb" sb-id="{{$sub_forms[$i]->id}}" href="#"><i class="fas fa-minus-circle"></i></a></td>
			@endif
          </tr>
      @endfor
    @endif          
        </tbody>
      </table>
</div>
<?php if ($user_type == 'admin'): ?>
</div>
</div>
</div>
<?php endif; ?>
<script>

$(document).ready(function() {
    
    $('#subforms').DataTable();
    
    $('body').on('DOMNodeInserted', 'div', function () {
        console.log('event trig')
        $('div.sub-form input.form-control').each(function(){
            var val = $(this).val();
            $(this).css({'width': val.length+'%'});
        
        });
    });
    
    $('body').on('keyup', 'input.form-control', function () {
            var val = $(this).val();
            $(this).css({'width': val.length+'%'});
    });    

    
    var assign_list = '<select class="form form-control" disabled>'+'<?php echo $options_list; ?>'+'</select>';
    var counter = {{ $i }};

    $('#add').click(function(){
        counter++;
        console.log("test");
        //$('#sub-form-area').append('<div class="row sub-form" style=""><input type="text" class="form-control" id="subform-title-'+counter+'" value="<?php echo $form_info->title; ?>">'+assign_list+'<br><button style="margin-top:10px;margin-left:2px; margin-bottom:30px;" class="btn btn-primary create-subform" id="subform-'+counter+'">Create</button></div>');
    
        $('#sub-form-area').append('<div class="row sub-form" style=""><input type="text" class="form-control" id="subform-title-'+counter+'" value="<?php echo $form_info->title; ?>"><br><button style="margin-top:10px;margin-left:2px; margin-bottom:30px;" class="btn btn-primary create-subform" id="subform-'+counter+'">Create</button></div>');        
    });

    var subform_info = {};

    subform_info['_token'] = '{{ csrf_token() }}';

    $(document).on('click', '.create-subform', function (){
        subform_info['users']         = $(this).prev().prev().val();
        subform_info['subform_title'] = $('#subform-title-'+counter).val();
        subform_info['form_id']       = {{ $form_info->id }};
        subform_info['client_id']     = {{ Auth::id() }};
		
		$('#add').prop('disabled', true);
		$('.create-subform').prop('disabled', true);
		$('#act-msg').show();
		
        $.ajax({
          url: '{{ route('gen_subform') }}',
          method: 'POST',
          data: subform_info,
          success: function (response) {
			  console.log(response);
              console.log(response.msg);
              //alert(response.msg);
			  $('#add').prop('disabled',false);
			  $('.create-subform').prop('disabled', false);	
			  $('#act-msg').hide();
			  
			  if (response.status == 'success') {
				location.reload();
			  }
          }
        });
    });
    
    $('.edit-sb').click(function(e){
        e.preventDefault();
        
        $('.modal-body #sb-name').val($(this).attr('sb-name'));
        $('.modal-body #sb-name').attr('sb-id', $(this).attr('sb-id'));
        
        
        var name = $('#sb-name').val();
          
    });
    
    $('#edit-form').click(function(){
 
        var sb_id    = $('#sb-name').attr('sb-id');        
        var sb_name  = $('#sb-name').val();
        
        console.log('sb_id '+sb_id);
        console.log('sb_name '+sb_name);
        
        $.ajax({
            url:'<?php echo url('Forms/EditSubform');  ?>',
            data: {
                'sb-id':sb_id,
                'name':sb_name
            },
            success:function (response) {
                if (response.status == 'success') {
                    
                    swal({
                      title:              "Sub-form Updated",
                      text:               "The sub-form information was successfully updated",
                      type:               "success",
                      showCancelButton:    false,
                      confirmButtonClass: "btn-success",
                      confirmButtonText:  "OK",
                      closeOnConfirm:      true
                    },
                    function(){
                      //swal("Deleted!", "Your imaginary file has been deleted.", "success");
                      location.reload();
                    });                     
                }
                else {
                    var error_msg = 'Something went wrong while updating sub-form';
                    if (response.status) {
                        error_msg = response.msg;
                    }
                    swal('Error', error_msg, 'error');
                }
            }
        });         
        
        
    });
    
    $('.delete-sb').click(function(e){
        e.preventDefault();
        
        //swal('Delete Confirmation')
        
        var sb_id = $(this).attr('sb-id');
        
        swal({
          title:              "Are you sure?",
          text:               "All associated information including users filled data will be deleted!",
          type:               "warning",
          showCancelButton:    true,
          confirmButtonClass: "btn-danger",
          cancelButtonClass:  "btn-primary",
          cancelButtonText:   "No",
          confirmButtonText:  "Yes, delete it!",
          closeOnConfirm:     true
        },
        function(){
            $.ajax({
                url:'<?php echo url('Forms/DeleteSubform');  ?>',
                data: {
                    'sb-id':sb_id
                },
                success:function (response) {
                    console.log(response);
                    if (response.status == 'success') {
                        swal({
                          title: "Sub-form removed",
                          text: response.msg,
                          type: "success",
                          showCancelButton: false,
                          confirmButtonClass: "btn-primary",
                          confirmButtonText: "OK",
                          closeOnConfirm: true
                        },
                        function(){
                            location.reload();
                        });                     
                    }
                    else {
                        swal('Error', 'Something went wrong while deleting form', 'error');
                    }
                }
            });          
    
        });         
        
        
        

    });
});
</script>
@endsection
