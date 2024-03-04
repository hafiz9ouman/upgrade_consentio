@extends('admin.client.client_app')
@section('content')
<div style="margin-left:30px">
<button class="btn btn-primary" id="send-email" style="margin-bottom:20px;"
onclick="document.location.href='{{route('send_form_to_users', ['id' => $subform_id])}}';">Send Email</button>
<table class="table">
  <thead>
    <tr>
      <th scope="col">Select All</th>
      <th scope="col">User </th>
      <th scope="col">Email </th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($form_user_list as $form_info): ?>
    <tr>
      <td><input type="checkbox" checked disabled></input></td>
      <td>{{ $form_info->name  }}</td>
      <td>{{ $form_info->email }}</td>
    </tr>
	<?php endforeach; ?>
  </tbody>
</table>
</div>
<script>

</script>
@endsection