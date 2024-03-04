@extends('users.ex_user_app', ['load_admin_css' => true])

@section('content')
  	<link rel="stylesheet" type="text/css" href="{{ url('custom_form/css/style.css') }}">
<style>
.section-heading-edit {
    display:none;
}
.change-heading-btn {
	display:none;
}
/***************************/
.main-panel{
	background-color: #fff;
	-webkit-box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 1px 5px 0 rgba(0, 0, 0, 0.12), 0 3px 1px -2px rgba(0, 0, 0, 0.2);
    box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 1px 5px 0 rgba(0, 0, 0, 0.12), 0 3px 1px -2px rgba(0, 0, 0, 0.2);
    -webkit-transition: all 0.3s ease-in-out;
    -o-transition: all 0.3s ease-in-out;
    transition: all 0.3s ease-in-out;
}
.margin{
	background-color: #fff;
	padding: 20px;
	margin: 0 !important; 
    box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 1px 5px 0 rgba(0, 0, 0, 0.12), 0 3px 1px -2px rgba(0, 0, 0, 0.2);
}
.head{
	background-color:#0f75bd;
}
input[type="text"] {
	color:#0f75bd;
}
.head h3,.fork i,.btn:hover{
	color:#FCFCFC;
}
.head ul{
	background-color:#f26924;
}
#easySelectable li.es-selected, #easySelectable li:hover {
    background: #f26924 !important;
	color:#fff;
}
textarea{
	border-radius:6px;
}
.change-heading-btn{
	background-color:#f26924;
	color:#fff;
}

</style>	
<body>
	<div class="container-fluid">
		<!---------------main-panel----------->
		<div class="main-panel" >
		
		<?php if (!empty($questions)):
				if (isset($questions[0])):
		?>
			<form style="display:none">
				<input type="hidden" name="form-id"      id="form-id"      value="{{ $questions[0]->form_id }}"> 
				<input type="hidden" name="form-link-id" id="form-link-id" value="{{ $questions[0]->form_link }}"> 
				<input type="hidden" name="subform-id"   id="subform-id"   value="{{ $accoc_info['subform_id'] }}"> 
				<input type="hidden" name="user-id"      id="user-id"      value="{{ $accoc_info['user_id'] }}"> 
				<input type="hidden" name="client-id"    id="client-id"    value="{{ $accoc_info['client_id'] }}"> 
				<input type="hidden" name="date-time"    id="date-time"    value="{{ $accoc_info['date_time'] }}">
				<input type="hidden" name="email"        id="email"        value="{{ $accoc_info['user_email'] }}">
			</form>
		<?php
				endif;
			endif;
		?>
			<div id="heading">
				<div class="head" id="form-heading">
					<ul>
						<li><strong>&starf;</strong></li>
						<li><i class="fa fa-chevron-up"></i></li>
					</ul>
					<?php if (isset($questions[0]) && !empty($questions[0])): ?>
					<h3>
						<?php echo $questions[0]->title; ?>	
					</h3>
					<?php endif; ?>
				</div>
			</div>

			<div class="collapseZero " id="form-body">
			
			<?php 
			$heading_recs = [];
			$section      = 0;
			$display_body_sec_div = 0;
			$close_body_sec_div   = 0;			
			foreach ($questions as $key => $question): 
				$sec_id          = $question->afs_sec_id;
				$heading         = $question->admin_sec_title;

				if ((isset($question->client_sec_title) && !empty($question->client_sec_title))) 
				{
					//$sec_id          = $question->cfs_sec_id;
					$heading         = $question->client_sec_title;
				}

			    if ($heading != null && !in_array($heading, $heading_recs)):
					$heading_recs[] = $heading;
					$section++;
					$display_body_sec_div = 1;				
			?>
			<?php if ($close_body_sec_div): // close 
					$close_body_sec_div = 0;
			?>
				</div>
			<?php endif; ?>			
				<div class="head sec-heading"  id="{{ "section-title-".$sec_id }}" num="{{ $section }}">
					<ul>
						<li><span>Section {{ $section }}</span></li>
						<li><i class="fa fa-chevron-up"></i></li>
					</ul>
					<div class="section-heading" id="{{ "section-heading-".$sec_id }}" num="{{ $section }}">
						<h3>{{ $heading }}</h3>
					</div>
				</div>
			<?php 
				endif; 
			?>
			<?php if ($display_body_sec_div): 
				$display_body_sec_div = 0;
				$close_body_sec_div = 1;
			?>
				<div class="margin" id="section-{{ $section }}-body" num="{{ $section }}" class="sec-heading-detail" style="margin: 20px 30px; display: block;">
			<?php
			endif;
			?>			
					<div class="content">
						<p></p>
						<h6>{{ $question->question_num.' '.$question->question }}</h6>
						<?php if ($question->question_comment != null && $question->question_comment != ''): ?>
							<small>{{-- $question->question_comment --}}</small>
						<?php endif; ?>						
						
					</div>
									
					<div id="wrap" class="wrap-content">						
						<?php if ($question->question_comment != null && $question->question_comment != ''): ?>
							<h6>{{ $question->question_comment }}</h6>				
						<?php endif; ?>						
						
						<?php
							$type = $question->type;	
							switch ($type): 
								case('sc'):
								case('mc'):
									$options = explode(', ', $question->options); 
									if (!empty($options)):
						?>
									<section class="options">
										<ul id="easySelectable" class="easySelectable">

						<?php
										foreach ($options as $option):
											$selected_class = '';
											if (isset($filled[$question->form_key]))
											{
												if ($type == 'sc' && trim($filled[$question->form_key]['question_response']) == trim($option))
												{
													$selected_class = 'es-selected';
												}
												if ($type == 'mc' && in_array(trim($option), $filled[$question->form_key]['question_response']))
												{
													$selected_class = 'es-selected';			
												}
											}
															
						?>
											<li class="es-selectable {{ $selected_class }}" name="{{ $question->form_key.'_'.$question->q_id.(($type=='mc')?('[]'):('')) }}" value="{{ $option }}" type="{{ $type }}">{{ $option }}</li>
						<?php
										endforeach;
						?>
										</ul>
									</section>
						<?php
									endif;	
								break;
								case ('bl'):
								case ('qa'):
						?>
								<div>
									<form>
										<label></label>
										<textarea  name="{{ $question->form_key.'_'.$question->q_id }}"  rows="4" cols="50"><?php echo (isset($filled[$question->form_key]))?($filled[$question->form_key]['question_response']):('') ?></textarea>
									</form>
								</div>	
						<?php	
								break;									
							endswitch; 
						?>
					</div>					
				<!--</div>-->
			<?php endforeach; ?>
			</div>
		</div>
	</div>
<script type="text/javascript" src="{{ url('custom_form/js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ url('custom_form/js/popper.min.js') }}"></script>
	{{-- <script type="text/javascript" src="{{ url('custom_form/js/bootstrap.min.js') }}"></script> --}}
<script src="{{ url('custom_form/js/easySelectable.js') }}"></script>
	{{-- <script src="js/index.js"></script> --}}

<script type="text/javascript" src="{{ url('custom_form/js/cust_js.js') }}"></script>

<script>

	function update_form_data_request (data)
	{
		$.ajax({
			url:'{{route('ajax_ext_user_submit_form')}}',
			method:'POST',
			/*

			headers: {

				'X-CSRF-TOKEN': $( 'meta[name="csrf-token"]' ).attr( 'content' )

			},
			*/
			data: data,
			success: function(response) {
				console.log(response);
			}
		});		
	}
	
	// get array of selected checkbox options
	function get_selected_mc_options (options_key)
	{
		var vals = [];
		
		$('li[name="'+options_key+'"]').each(function(){
			
			if ($(this).hasClass('es-selected')) {
				vals.push($(this).attr('value'));
			}
			
		});	

		return vals;
	}

	$(document).ready(function(){
		//$('#form-body').slideToggle("slow");
		
		var post_data 			  = {};
		
		var form_id               = $('input[name="form-id"]').val();

		var form_link_id          = $('input[name="form-link-id"]').val();

		var email                 = $('input[name="email"]').val();

		var user_id               = $('input[name="user-id"]').val();

		post_data['_token']       = '{{csrf_token()}}';

		post_data['form-id']      = form_id;	

		post_data['user-id']      = user_id;

		post_data['form-link-id'] = form_link_id;		

		post_data['email']        = email;			

		$('.easySelectable').easySelectable({
			filter: "li",	
			cancel: ".not-unselectable",
			onSelecting: function(el) {
				$(el).addClass('not-unselectable');															
				if ($(el).attr('type') == 'sc') {
					el.siblings().each(function(index, option) {
						if (el != option) {
							$(option).removeClass('not-unselectable');											
							$(option).removeClass("es-selected");
						}
					});
				}		
			},
			onSelected: function (el) {		
				var type                  = el.attr('type');
				var question_key          = el.attr('name');
								 
				if (type == 'mc') {					
					post_data[question_key] = get_selected_mc_options(question_key);
				}
				else {
					post_data[question_key] = el.attr('value');
				}
				
				console.log(post_data);
				
				update_form_data_request(post_data);	
				
			},
			onUnSelected: function(el) {		
				var vals 				  = [];	
				var type                  = el.attr('type');
				var question_key          = el.attr('name');
				
				if ($(el).attr('type') == 'sc') {
					$(el).addClass("es-selected");	
				}
				else if ($(el).attr('type') == 'mc') {	
				
					post_data[question_key] = get_selected_mc_options(question_key);
				
				
/* 					$('li[name="'+question_key+'"]').each(function(){
						if ($(this).hasClass('es-selected')) {
							vals.push($(this).attr('value'));
						}

						post_data[question_key] = vals;	
					}); */
					
					console.log(post_data);					
					update_form_data_request(post_data);	
				}
			}
		});
		
		
		$('textarea').change(function(){
				var type                  = $(this).attr('type');
				var question_key          = $(this).attr('name');
			
				post_data[question_key] = $(this).val();
				
				update_form_data_request(post_data);	
			
				//console.log(post_data);
		});	
				
		$('.sec-heading').click(function(e){

			var tag = e.target.tagName.toLowerCase();	
			console.log(tag);			
			var num = $(this).attr('num');
			
			var up   = $(this).find($('i.fa-chevron-up')).length;
			var down = $(this).find($('i.fa-chevron-down')).length;

			if (tag == 'div' || tag == 'span' || tag == 'h3') {
				$("#section-"+num+"-body").slideToggle("slow");	
				if (up) {
					$("i.fa-chevron-up", this).toggleClass("fa-chevron-up fa-chevron-down");
				}
				if (down) {
					$("i.fa-chevron-down", this).toggleClass("fa-chevron-down fa-chevron-up");
				}
			}
		});
				
	});
</script>
@endsection