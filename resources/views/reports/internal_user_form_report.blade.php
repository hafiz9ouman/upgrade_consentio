@extends(($user_type=='admin')?('admin.layouts.admin_app'):('admin.client.client_app'))
@section('content')
<div class="app-title">
	@if (Auth::user()->role == 1)
	<!--
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i>
		</li>
		<li class="breadcrumb-item"><a href=""></a>
		</li>		
	</ul>
	-->
	@endif
</div>

@if (session('alert'))
    <div class="alert alert-primary">
        {{ session('alert') }}
    </div>
@endif

<div class="row" style="padding-left:20px; padding-right:12px;">
	<div class="col-md-12">
		<div class="tile">
			<h3 class="tile-title"></h3>
			<div class="table-responsive">
				<table class="table" id="report_table">
					<thead class="back_blue">
						<tr>
						<?php $user_list_count = count($user_list); ?>
						<th>Question</th>
						@foreach($user_list as $user)
							<th>{{$user->name}} </th>
						@endforeach
						</tr>
					</thead>
					<tbody>
						@foreach($questions as $question)
						<tr>
							<td>{{$question->question}}</td>
							<?php
								$i = 0;
								for ($i = 0; $i < $user_list_count; $i++):		
							?>
								<td>-</td>
							<?php
								endfor;
							?>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<script>
$(document).ready(function() {
	
    // Setup - add a text input to each footer cell
    $('#report_table thead tr').clone(true).appendTo('#report_table thead');	
    $('#report_table thead tr:eq(1) th').each(function (i) {
        var title = $(this).text();
        $(this).html('<input type="text" placeholder="Search '+title+'" />');
 
        $('input', this ).on('keyup change', function () {
            if (table.column(i).search() !== this.value) {
                table
                    .column(i)
                    .search( this.value )
                    .draw();
            }
        });
		
    });
	
	var table = $('#report_table').DataTable({
        orderCellsTop: true,
        //fixedHeader: true
    });	
 
	//$('#report_table').DataTable();

});
</script>
@endsection