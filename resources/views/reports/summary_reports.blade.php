@extends('admin.client.client_app')
@section('content')
<style>
    .orange_c0lor{
  background-color: #f26924;
  color: #fff;
}
.blue_c0lor,.active{
  background-color: #0f75bd;
  color: #fff;
}

.tablinks {
	cursor:pointer;
}

/* Style the tab content */
.tabcontent {
  display: none;
  border: 1px solid #ccc;
  border-top: none;
}
#cust-data-table{
    overflow:auto;
}
#cust-data-table table td:first-child,#cust-data-table table th:first-child{
  width:33%;
  text-align: left !important;
}
#cust-data-table .table td,#cust-data-table  .table th{
  text-align: center;
  border: 0;
}
.table thead th{
  border-bottom: 0;
  padding: 4px 12px;
}
.table tbody tr{
  border-bottom: 1px solid #eee;
}
.table{
  margin: 0;
  font-size: 13px;
  white-space: nowrap;
}

.fa-check {
	color:#67b39e;
}

</style>
<?php
 
/* 	$user_header_list = '';
	
	foreach ($user_list as $user)
	{
		$user_header_list .= '<th>'.$user->name.'</th>'; 
	} */

	$user_count = count($user_list);
	
	$user_headings = '';

	foreach ($user_list as $user)
	{
		$user_headings .= '<th>'.$user->name.'</th>';
	}

	$empty_headings = '';	
	$empty_cells    = '';
	for ($num = 0; $num < $user_count; $num++)
	{
		$empty_headings .= '<th></th>';		
		$empty_cells    .= '<td></td>';
	}
	
	
?>
<div class="row" style="padding-left:20px; padding-right:12px;">
	<div class="col-md-12">
		<div class="tile">
			<h3 class="tile-title">Summary Reports</h3>
		</div>
		
		<div id="cust-data-table">
		    <div class="table-responsive">
                <table class="table">
                  <thead>
                    <!-------------------main-head--------->
                    <tr class="orange_c0lor">
                      <th>Department</th>
					<?php
						foreach ($subform_list as $subform):
					?>	
                      <th class="tablinks" onclick="{{"show_sub_form(event, 'subform-$subform->id')"}}" >{{$subform->title}}</th>
                      <!--<th class="tablinks" onclick="show_sub_form(event, 'user2')" id="defaultOpen">HR</th>-->
                    <?php
						endforeach;
					?>
					
                    </tr>
					
                    <tr>
                      <th class="orange_c0lor">Person who completed the Form</th>
					<?php
						echo $user_headings;
					?>
                    </tr>
                    <!------------------------------------>
                  </thead>
                </table>
              </div>
			  <?php 
				foreach ($subform_list as $subform):
			  ?>
              <div id="{{"subform-".$subform->id}}" class="tabcontent table-responsive">
                <table class="table ">
                  <tbody>

                    <tr>
                      <td>Type of private data processed</td>
					<?php
						echo $empty_cells;
					?>
                    </tr>
					<?php
						$curr_question_key = '';
						$subform_id = $subform->id;
						foreach ($questions as $question):
							if ($curr_question_key != $question->form_key && $question->question_assoc_type == 2): 
					?>					
					
                    <!---------head------->
                    <tr>
                      <th class="orange_c0lor">{{ $question->question }}</th>
					<?php
						echo $empty_headings;
					?>
                    </tr>
					<?php 
						if (!empty($question_options = explode(', ', $question->options))):
							foreach ($question_options as $option):					
					?>
                    <!---------------------->
                    <tr>
                      <td>
					<?php
								echo $option;
					?>		
					  </td>
					<?php
								foreach ($user_list as $user):
									if (isset($question->user_responses[$subform_id][$user->id]) && trim($option)):
										if ($question->type == 'mc'):
											$user_responses = explode(', ', $question->user_responses[$subform_id][$user->id]['response']);
											if (in_array(trim($option), $user_responses)):
					?>						
					  <td><i class="fas fa-check"><span></span></i></td>
					<?php
					                        else:
					?>
					  <td>-</td>
					<?php
											endif; // if (in_array(trim($option), $user_responses)
											
										elseif ($question->type == 'sc' && trim($option) == trim($question->user_responses[$subform_id][$user->id]['response'])):											
					?>
					  <td><i class="fas fa-check"><span></span></i></td>	
					<?php
										else:// else of if ($question->type == 'mc')
					?>
						<td>-</td>
					<?php		
										endif; // if ($question->type == 'mc')
									else: // else of if (isset($question->user_responses[$subform_id][$user->id]) && trim($option) == trim($question->user_responses[$subform_id][$user->id]['response'])
					?>
                      <td>-</td>
					<?php 
									endif; // if (isset($question->user_responses[$subform_id][$user->id]) && trim($option) == trim($question->user_responses[$subform_id][$user->id]['response']))
								endforeach; // foreach ($user_list as $user)
					?>	
                    </tr>
					<?php		
							endforeach; // foreach ($question_options as $option)
						endif; // if (!empty($question_options = explode(', ', $question->options)))
					?>
					<?php
							endif;   // if ($curr_question_key != $question->form_key && $question->question_assoc_type == 2)
						endforeach; // foreach ($questions as $question)
					?>
                  </tbody>
                </table>
              </div>
			  <?php
				endforeach; 
			  ?>
            </div>   
	</div>
</div>
<script>
function show_sub_form(evt, tabName) {

  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(tabName).style.display = "block";
  evt.currentTarget.className += " active";
}

// Get the element with id="defaultOpen" and click on it


$(document).ready(function(){
	$(".tablinks:first").attr('id', 'defaultOpen');
	document.getElementById("defaultOpen").click();	
 
 //show_sub_form(event, 'user1');

});





</script>
@endsection