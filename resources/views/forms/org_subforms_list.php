@extends(($user_type=='admin')?('admin.layouts.admin_app'):('admin.client.client_app'))
@section('content')
<div style="margin-left:30px; overflow-x: scroll;background-color: #fff;">

<div class="">
    <h3 class="tile-title">User Forms</h3>
<table class="table sending_table big-table-width">
  <thead class="back_blue">
    <tr>
      <th>Sr No.</th>
      <th scope="col">Email </th>
      <th>Assessment Form</th>
      <th>Sent Time</th>
      <th>Total Days</th>
      <th>Remaining Days</th>      
      <th>Expiry Time</th>
      <th>Submitted</th>
	  <th scope="col">Form Links </th>
    </tr>
  </thead>
  <tbody style="background-color: #fff;">
    <?php $i = 0; ?>
    <?php foreach ($form_user_list as $form_info): ?>
    <tr>
      <td>{{ $i + 1 }}</td>
      <td>{{ (isset($form_info->user_email)?($form_info->user_email):($form_info->email)) }}</td>
      <td>{{ $form_info->title }}</td>
      <td>{{ $form_info->created }}</td>
      <?php
        //$now = time(); // or your date as well
        $created = strtotime($form_info->created);
        $expiry = strtotime($form_info->expiry_time);
        $datediff = $expiry - $created;
        $total_days  = round($datediff / (60 * 60 * 24));
      ?>      
      <td>{{ $total_days }}</td>
      <?php
        $now = time(); // or your date as well
        $expiry = strtotime($form_info->expiry_time);
        $datediff = $expiry - $now;
        $rem_days  = round($datediff / (60 * 60 * 24));
      ?>       
      <td>{{$rem_days }}</td>
      <td>{{ $form_info->expiry_time }}</td>
      <td>{{(($form_info->is_locked)?('Yes'):('No'))}}</td>
	  <td><a href="{{ url('Forms/CompanyUserForm/'.$form_info->form_link_id) }}"> Open</a></td>
    </tr>
    <?php $i++; ?>
	<?php endforeach; ?>
  </tbody>
</table>
</div>
<div>

</div>
<script>
$(document).ready(function (){

	
		
		
		
});
</script>
@endsection