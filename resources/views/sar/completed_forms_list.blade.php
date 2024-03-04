@extends ((isset($user_type) && $user_type == 'admin')?('admin.layouts.admin_app'):('admin.client.client_app'))

@section('content')
<style>
    .fs-12 {
        font-size:10px;
    }
</style>
<link href="{{ url('frontend/css/jquery.mswitch.css')}}"  rel="stylesheet" type="text/css">

<?php if (isset($user_type) && $user_type == 'admin'): ?>
<div class="app-title">
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i>
		</li>
		<li class="breadcrumb-item"><a href="{{route('admin_forms_list')}}">Manage Assessment Forms</a>
		</li>
	</ul>
</div>
<?php endif; ?>
<div class="row" style="margin-left:10px;">
  <div class="col-md-12">
    <div class="tile">

      <div class="table-responsive cust-table-width">
                <h3 class="tile-title">Completed SAR Forms
      </h3>
        <table class="table" id="forms-table">
          <thead class="back_blue">
    <tr>
	<th scope="col">User Email</th>
	<th scope="col">Form Link</th>
        <th scope="col">Form Name</th>
        <th scope="col">Request Status</th>
        <th scope="col">Change Status</th>
        <th scope="col">Submission Date</th> 
        <th scope="col">Due Date</th>                
    </tr>

  </thead>

  <tbody>

  	<?php foreach ($completed_forms as $key => $form_info): ?>
    <tr>
        <td>{{$form_info->email}}</td>
        <td>
            <?php
                $form_link = ''; 
                if ($form_info->user_type == 'Internal')
                    $form_link = url('Forms/CompanyUserForm/'.$form_info->form_link);
                if ($form_info->user_type == 'External')
                    $form_link = url('Forms/ExtUserForm/'.$form_info->form_link);
                    
            ?>
            <a href="{{$form_link}}" target="_blank">Open</a>
        </td>
        <td>{{$form_info->form_title}}</td>
        <td><span id="request-status-{{$key}}" style="color:#{{($form_info->status)?('29bb25'):('d65c25')}}">{{($form_info->status)?('Completed'):('Pending')}}</span></td>
        <td><input type="checkbox" class="m_switch_check" id="change-status-{{$key}}" request-num="{{$form_info->request_id}}" num="{{$key}}" value="{{$form_info->status}}"></td>        
        <td>{{date('Y-m-d', strtotime($form_info->submission_date))}}</td>
        <td>{{date('Y-m-d', strtotime($form_info->due_date))}}</td>
    </tr>
	<?php endforeach; ?>

  </tbody>

</table>

</div>
    </div>
  </div>
</div>
<script src="{{url('frontend/js/jquery.mswitch.js')}}"></script>
<script>
    $(document).ready(function(){
        
        $(".m_switch_check:checkbox").mSwitch({
            onRender:function(elem){
                if (elem.val() == '1') {
                    $.mSwitch.turnOn(elem);
                }
                else {
                    $.mSwitch.turnOff(elem);                    
                }
            },            
            onTurnOn:function(elem){  
                sendChangeStatusRequest(elem,1);   
            },
            onTurnOff:function(elem){
                sendChangeStatusRequest(elem,0);
            }
        });
        
        function sendChangeStatusRequest (elem, update_status, warn_user = 1)
        {
           var request_num          = elem.attr('request-num');
           var num                  = elem.attr('num');                
           var text                 = (update_status)?('Completed'):('Pending');
           var color                = (update_status)?('#29bb25'):('#d65c25');
           var post_data            = {};

           post_data['_token']      = '{{csrf_token()}}';
           post_data['request_num'] = request_num;
           post_data['status']      = update_status;
           post_data['warn']        = warn_user;
           
            
           $.ajax({
               url:'{{url("SAR/ChangeRequestStatus")}}',
               method:'POST',
               data: post_data,
               success: function (request_response) {
                   
                   console.log(request_response);
                   console.log(warn_user);
                   
                   if (request_response.status == 'warning' && warn_user == 1) {
                        swal({
                          title: "Date Expired",
                          text:  request_response.msg,
                          type:  "warning",
                          showCancelButton:    true,
                          confirmButtonClass: "btn-primary",
                          confirmButtonText:  "OK!",
                          closeOnConfirm:      true,
                        },
                        function(isConfirm) {
                            if (isConfirm) {
                                sendChangeStatusRequest (elem, update_status, 0);
                            } 
                            else {
                               if (update_status) {
                                    $.mSwitch.turnOff(elem);                                              
                               }
                               else {
                                    $.mSwitch.turnOn(elem);                                              
                               }                            
                            }
                        });                        
                   }
                   else if (request_response.status == 'success') {
                       elem.val(update_status);
                       $('#request-status-'+num).text(text).css('color',color);                           
                   }
                   else {
                       swal('Error', 'Something went wrong while updating status.', 'error');
                       if (update_status) {
                            $.mSwitch.turnOff(elem);                                              
                       }
                       else {
                            $.mSwitch.turnOn(elem);                                              
                       }
                   }
               }
           });            
        }
        
        <?php
            $search_filter = '';
            $search = '';
            if (app('request')->input('search_filter')) 
            {
                $search_parameter = app('request')->input('search_filter');
                $search = '"search": {"search": "'.$search_parameter.'"},';
            }
        ?>
        
        var check_col_index = 9;
        var table = $('#forms-table').DataTable({
            "order": [[ 5, "desc" ]],
            {!!$search!!}
            "rowCallback": function(row, data) {
                if (data[check_col_index] == "0") {
                    $(row).hide();
                }
            }
        });
        
        table.column(check_col_index).visible(false);
    })
</script>

@endsection