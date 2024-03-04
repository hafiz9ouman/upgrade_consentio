@extends('admin.client.client_app')
@section('content')
<style>
.orange_c0lor{
  background-color: #f26924;
  color: #fff;
}
.grey_bg {
  background-color: #bebdbd;
  color: #fff;
}

.blue_c0lor,.active{
  background-color: #0f75bd;
  color: #fff;
}

.yellow_c0lor {
    background-color:#ffc41d;
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

.dept-name {
	color:#f26924;
}
.print_exp {
    text-align: right;
}
.print_exp button {
    border: none;
    background: #0f75bd;
    padding: 5px 20px;
    margin-right: 10px;
    border-radius: 4px;
    color: #fff;
}


</style>
<div class="row" style="padding-left:20px; padding-right:12px;">
	<div class="col-md-12">
		<div class="tile">
			<h3 class="tile-title">Global Data Inventory</h3>
		</div>
		
		<!-- <div class="print_exp">

		    <a href="{{url('/Reports/AssetsReportsEx/2')}}" > <button type="button" class="Export">Export</button></a>
		</div> -->
		
		<?php if (count($user_form_list)): ?>
		<div id="cust-data-table">
		    <div class="table-responsive">
                <table class="table">
                  
                </table>
              </div>
			  <?php 
				//foreach ($subform_list as $subform):
			  ?>
              <div id="" class="table-responsive">
                <table class="table">
				  <thead>
                    <!-------------------main-head--------->
                    
                    <!------------------------------------>
                  </thead>				
                  <tbody>
					<?php
						$curr_question_key = '';
						$q_count = 0;
						$cell_color = '';
						$check_previous[] = '';
						$user_responses[] = '';
						foreach ($questions as $question):
							if ($curr_question_key != $question->form_key && $question->question_assoc_type == 2): 
					?>					
					
                    <!---------head------->
                    <!-- <tr> -->
                    <?php
                        if ($q_count < 2):
                            $cell_color = 'grey';
                    ?>

                      <!-- <th class="blue_c0lor" q-id="{{$question->id}}">{{ $question->question }}</th> -->
                      
                    <?php
                        elseif ($q_count == 2):
                    ?>
                    <!-- type of pvt data processed new -->
                    <!-- </tr> -->
                    <tr>
                      <td class="yellow_c0lor"><strong>TYPE OF PRIVATE DATA PROCESSED</strong></td>
					<?php
						// empty cells
						$u_count = count($user_form_list);
						// for ($i = 0; $i < $u_count; $i++):
					?>
						
					<?php 
						// endfor;
					?>
                    </tr>
                    <!-- type of pvt data processed new -->                   
                    <tr>
                    <th class="grey_bg" q-id="{{$question->id}}">{{ $question->question }}</th>
                    <?php
                        else:
                            $cell_color = 'orange';
                        ?>
                      <th class="grey_bg" q-id="{{$question->id}}">{{ $question->question }}</th>
					<?php
					    endif; // if ($q_count < 2)
					    $q_count++;
						//echo $empty_headings;
					?>
					<?php 
								if (($question->type == 'sc' || $question->type == 'mc') && !empty($question_options = explode(', ', $question->options))):
					?>
					</tr> <!--end of question row -->
					<?php
									foreach ($question_options as $qo_key => $option):
					?>
                    <!---------------------->
                    <tr r-id="{{$question->id.'_'.$qo_key}}">
                      <td op-id="{{$question->id.'_'.$qo_key}}">
					<?php
										echo $option;
					?>		
					  </td>

					<?php
										foreach ($user_form_list as $user):
											$user_type  =  $user->type;
											$user_name  =  $user->user;
											$subform_id =  $user->sub_form_id;
											$user_id    = ($user_type == 'ex')?($user->user):($user->id);
											
											if (isset($question->user_responses[$user_type][$subform_id][$user_id]) && trim($option)):
												if ($question->type == 'mc'):
													$user_responses = explode(', ', $question->user_responses[$user_type][$subform_id][$user_id]['response']);
													if (in_array(trim($option), $user_responses)):
					?>						
					 <!--  <td op-id="{{$question->id.'_'.$qo_key}}"><i class="fas fa-check"><span></span></i></td> -->
					<?php
					if(!in_array($question->id.$option, $check_previous)){
					?>	<td op-id="{{$question->id.'_'.$qo_key}}"><i class="fas fa-check"><span></span></i></td> <?php
					}
                        $check_previous[] = $question->id.$option;
													else:// else of if (in_array(trim($option), $user_responses))
					?>
					  <!-- <td op-id="{{$question->id.'_'.$qo_key}}">-</td> -->
					<?php
													endif; // if (in_array(trim($option), $user_responses)										
												elseif ($question->type == 'sc' && trim($option) == trim($question->user_responses[$user_type][$subform_id][$user_id]['response'])):											
					?>
					  <td op-id="{{$question->id.'_'.$qo_key}}"><i class="fas fa-check"><span></span></i></td>
					<?php
												else:// else of if ($question->type == 'mc')
					?>
                      <!-- <td op-id="{{$question->id.'_'.$qo_key}}"></td> -->
					<?php		
												endif; // if ($question->type == 'mc')
											else: // else of if (isset($question->user_responses[$user_type][$subform_id][$user[$id_key]]) && trim($option))
					?>
						<!-- <td op-id="{{$question->id.'_'.$qo_key}}">-</td> -->
					<?php 
											endif; // if (isset($question->user_responses[$user_type][$subform_id][$user[$id_key]]) && trim($option))
										endforeach; // foreach ($user_form_list as $user):
					?>	
                    </tr>
					<?php		
									endforeach; // foreach ($question_options as $option)
								elseif ($question->type == 'qa'):
									foreach ($user_form_list as $user):
									$user_type  =  $user->type;
									$user_name  =  $user->user;
									$subform_id =  $user->sub_form_id;
									$user_id    = ($user_type == 'ex')?($user->user):($user->id);
					?>
						<!-- <td q-id="{{$question->id}}"> -->
					<?php

										if (isset($question->user_responses[$user_type][$subform_id][$user_id]['response']) && !empty($question->user_responses[$user_type][$subform_id][$user_id]['response'])):					
					?>
					<?php 					
					// echo $question->user_responses[$user_type][$subform_id][$user_id]['response']; 
					
										else: // else of if (isset($question->user_responses[$user_type][$subform_id][$user[$id_key]]['response']) && !empty($question->user_responses[$user_type][$subform_id][$user[$id_key]]['response'])):
					?>
						
					<?php
										endif; // if (isset($question->user_responses[$u_type][$subform_id][$user[$id_key]]['response']) && !empty($question->user_responses[$u_type][$subform_id][$user[$id_key]]['response'])):
					?>
						<!-- </td> -->
					<?php
									endforeach; // foreach ($user_form_list as $user):
					?>
					</tr> <!--end of question row -->
					<?php	
								endif; // 	if (($question->type == 'sc' || $question->type == 'mc') && !empty($question_options = explode(', ', $question->options))):
					?>
					<?php
							endif;   // if ($curr_question_key != $question->form_key && $question->question_assoc_type == 2)
						endforeach; // foreach ($questions as $question)
					?>
                  </tbody>
                </table>
                
              </div>
			  <?php
				//endforeach; 
			  ?>
        </div>
        <?php else: ?>
        <div class="well">
            No sub-form of {{$form_name}} is assigned to any user
        </div>
        <?php endif; ?>
	</div>
</div>
<script>
$(document).ready(function(){
    $('th.grey_bg').each(function(){
        var hide_parent_row = true;

        var q_id = $(this).attr('q-id');
        var qa_cells = $('td[q-id="'+q_id+'"]');
        var parent_row;
        for (i = 0; i < qa_cells.length; i++) {
            parent_row = $(this).parent();
            if (($(qa_cells[i]).text()).trim() != '-') {
                hide_parent_row = false;
                break;
            }
        }

        if (hide_parent_row) {
            //console.log(parent_row);
            $(parent_row).hide();
        }

        var i = 0;

        var question_not_filled = true;
        while ($('td[op-id="'+q_id+'_'+(i)+'"]').length) {
            hide_parent_row = true;
            var opt_row = $('td[op-id="'+q_id+'_'+(i)+'"]');
            for (index = 1; index < opt_row.length; index++) {

                if ($.trim($(opt_row[index]).text()) != "-") {
                    hide_parent_row = false;
                    parent_row = $(this).parent();
                    question_not_filled = false;
                    break;
                }
                
            }

            if (hide_parent_row) {
                $('tr[r-id="'+q_id+'_'+i+'"]').hide();
            }

            i++;

        }
        
        if (question_not_filled) {
            $('th[q-id="'+q_id+'"]').parent().hide();
        }

    });

});
</script>
@endsection










