@extends('admin.client.client_app', ['load_admin_css' => true])
<?php 
	if (!empty($questions) && isset($questions[0])){		
		$showcomment = $questions[0]->is_locked  == 1 && $form_type == 'audit' ? true : false ;
		if (isset($questions[0]) && !empty($questions[0])){  ?>
			@section('title' , $questions[0]->title)
			<?php  
		} 
	}
	$ratings = DB::table('evaluation_rating')->get();
?>
@section('content')
	<link rel="stylesheet" type="text/css" href="{{ url('public/custom_form/css/style.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ url('public/bar-filler/css/style.css') }}" />
	<link rel="stylesheet" type="text/css" href="{{ url('backend/css/jquery.datetimepicker.css') }}" />
	<!-- <link rel="stylesheet" type="text/css" href="{{ url('public/dropify/css/dropify.min.css') }}" /> -->
	<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css">
	<style>	
		.custbtm button {

			display: initial !important;

		}

		.arrow {
			cursor: pointer;
			display: inline-block;
			height: 40px;
			margin-left: 40px;
			margin-right: 40px;
			position: relative;
			line-height: 2.5em;
			padding-left: 1em;
			padding-right: 2em;
			background: white;
			color: #6f7e72;
		}

		.arrow-red {
			cursor: pointer;
			display: inline-block;
			height: 40px;
			margin-left: 40px;
			margin-right: 40px;
			position: relative;
			line-height: 2.5em;
			padding-left: 1em;
			padding-right: 2em;
			background: #cc0000;
			color: #fff;
		}

		.arrow-green {
			cursor: pointer;
			display: inline-block;
			height: 40px;
			margin-left: 40px;
			margin-right: 40px;
			position: relative;
			line-height: 2.5em;
			padding-left: 1em;
			padding-right: 2em;
			background: #336600;
			color: #fff;
		}

		.arrow:after {
			content: "";
			position: absolute;
			border-bottom: 20px solid transparent;
			border-top: 20px solid transparent;
			height: 0px;
			width: 0px;
			margin-right: -20px;
			border-left: 20px solid white;
			right: 0;
		}

		.arrow-red:after {
			content: "";
			position: absolute;
			border-bottom: 20px solid transparent;
			border-top: 20px solid transparent;
			height: 0px;
			width: 0px;
			margin-right: -20px;
			border-left: 20px solid #cc0000;
			right: 0;
		}

		.arrow-green:after {
			content: "";
			position: absolute;
			border-bottom: 20px solid transparent;
			border-top: 20px solid transparent;
			height: 0px;
			width: 0px;
			margin-right: -20px;
			border-left: 20px solid #336600;
			right: 0;
		}

		.arrow:hover,
		.arrow:active {
			background: #438e8c;
			text-decoration: none;
			color: #fff;
		}

		.arrow-red:hover,
		.arrow-red:active {
			background: #f3695f;
			text-decoration: none;
			color: #fff;
		}

		.arrow-green:hover,
		.arrow-green:active {
			background: #9fcc00;
			text-decoration: none;
			color: #fff;
		}

		.arrow:hover:after,
		.arrow:active:after {
			border-left: 20px solid #438e8c;
		}

		.arrow-red:hover:after,
		.arrow-red:active:after {
			border-left: 20px solid #f3695f;
		}

		.arrow-green:hover:after,
		.arrow-green:active:after {
			border-left: 20px solid #9fcc00;
		}

		.section-heading-edit {
			display: none;
		}

		.change-heading-btn {
			display: none;
		}

		.legend-red {
			color: #cc0000;
			font-size: 24px;
		}

		.legend-green {
			color: #336600;
			font-size: 24px;
		}

		.user-guide {
			margin-left: 10px;
			color: #177e9d;
		}

		/***************************/
		.main-panel {
			background-color: #fff;
			-webkit-box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 1px 5px 0 rgba(0, 0, 0, 0.12), 0 3px 1px -2px rgba(0, 0, 0, 0.2);
			box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 1px 5px 0 rgba(0, 0, 0, 0.12), 0 3px 1px -2px rgba(0, 0, 0, 0.2);
			-webkit-transition: all 0.3s ease-in-out;
			-o-transition: all 0.3s ease-in-out;
			transition: all 0.3s ease-in-out;
		}

		.margin {
			background-color: #fff;
			padding: 20px;
			margin: 0 !important;
			box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 1px 5px 0 rgba(0, 0, 0, 0.12), 0 3px 1px -2px rgba(0, 0, 0, 0.2);
		}

		.head {
			background-color: #0f75bd;
		}

		input[type="text"] {
			color: #0f75bd;
		}

		.head h3,
		.fork i,
		.btn:hover {
			color: #FCFCFC;
		}

		.head ul {
			background-color: #73b84d;
		}

		/*
		#easySelectable li.es-selected, #easySelectable li:hover {
			background: #73b84d !important;
			color:#fff;
		}
		*/
		#easySelectable li.es-selected {
			background: #73b84d !important;
			color: #fff;
		}

		#easySelectable li:hover:not(.es-selected) {
			background: #0f75bd !important;
			color: #fff;
		}

		textarea {
			border-radius: 6px;
		}

		.change-heading-btn {
			background-color: #73b84d;
			color: #fff;
		}

		.form-section {
			display: none;
		}

		.btn-section {
			margin-top: 20px;
		}

		#perc-bar .tip {
			background: #73b84d;
		}

		.completed-questions {
			display: none;
		}

		.hidden {
			display: none;
		}

		li.es-selectable {
			font-size: 12px;
		}

		li.es-selectable input {
			background: transparent;
			border: none;
			text-align: center;
			font-weight: bold;
			height: 100%;
			width: 100%;
		}

		li.es-selected #date-picker {
			background: transparent;
			border: none;
			color: #fff;
			text-align: center;
			font-weight: bold;
		}

		li.es-selected .date-picker {
			background: transparent;
			border: none;
			color: #fff;
			text-align: center;
			font-weight: bold;
		}

		h6.question-comment {
			font-size: 16px;
		}
	</style>
	<script>
		var all_q_ids = [];
		var section_list = [];
		var sectionwise_q_keylist = [];
		var assoc_questions = [];
		var optional_sections = [];
	</script>
	<body>
		<div class="container-fluid">
			@if(!empty($expiry_note))
				<div class="alert alert-danger" role="alert">
					<?php echo $expiry_note; ?>
				</div>
			@endif
			<div id="perc-bar" class="barfiller">
				<div class="tipWrap">
					<span class="tip"></span>
				</div>
				<span class="fill" id="fill-bar" data-percentage="0"></span>
			</div>

			<!---------------main-panel----------->
			<div class="main-panel">
				@if(!empty($questions && isset($questions[0])))
					<input type="hidden" name="user-form-id" value="{{ $questions[0]->uf_id }}">
					<input type="hidden" name="form-id" value="{{ $questions[0]->form_id }}">
					<input type="hidden" name="form-link-id" value="{{ $questions[0]->form_link_id }}">
					<input type="hidden" name="user-id" value="{{ $questions[0]->user_id }}">
					<input type="hidden" name="subform-id" value="{{ $questions[0]->sub_form_id }}">
				@endif
				<!-- Form Heading -->
				<div id="heading">
					<div id="lengthy_length" style="display:none;"></div>
					<div id="not_filled" style="display:none;"></div>
					<div id="filled" style="display:none;"></div>
					<div class="head" id="form-heading">
						<ul>
							<li><strong>&starf;</strong></li>
							<li><i class="fa fa-chevron-up"></i></li>
						</ul>
						@if (isset($questions[0]) && !empty($questions[0]))
							<h3>
								@if(session('locale')=='fr') 
									{{ $questions[0]->title_fr }}
								@else
									{{$questions[0]->title }}
								@endif
							</h3>
						@endif
					</div>
				</div>
				<!-- End -->

				<div class="collapseZero" id="form-body">
					@if(isset($questions[0]) && !empty($questions[0]->comments))
						<div class="">
							<h5><?php //echo $questions[0]->comments; ?></h5>
						</div>
					@endif

					<?php 
						$heading_recs = [];
						$section      = 0;	
						$section_questions    = 0;
						$total_questions      = 0;
						$question_count       = 0;
						$date_picker_count    = 0;
						$display_body_sec_div = 0;
						$close_body_sec_div   = 0;
						foreach ($questions as $key => $question): 
							if($question->question_num)
								$parent_section = $question->question_num;
								
								if($parent_section=='1.8'){ 
									//echo '<pre>';print_r($question); 
								}
								$sec_id          = $question->afs_sec_num; 
								// use sec num instead of section id
								$heading         = $question->admin_sec_title;
								$question_count++;

								if ((isset($question->client_sec_title) && !empty($question->client_sec_title))){
									$heading         = $question->client_sec_title;
								}

								if ($heading != null && !in_array($heading, $heading_recs)):

									$heading_recs[] = $heading;
									$section++;
									$display_body_sec_div = 1;	

									if ($close_body_sec_div):
										$close_body_sec_div = 0; 
										?>
										<!--  -->
										<div class="alert alert-success hidden submit-msg" id="submit-msg" style="margin-top:20px;margin-bottom:10px">
											<h4>{{ __('Almost Done') }}!</h4>
											{{ __('Please review your answers before submitting form and click') }} 
											<button class="btn btn-success btn-lg submit">{{ __('Submit') }}</button> 
											{{ __('once finalized.') }}
										</div>
										<div class="alert alert-warning  danger-msg" id="danger-msg" style="margin-top:20px;margin-bottom:10px">
											<h4>{{ __('All fields are required to proceed') }}</h4>
										</div>
										<div class="alert alert-info" style="margin-top:10px;">
											<h4>{{ __('Section List') }}</h4>
											<div class="user-guide">
												<p><span class="legend-green">&#x25A0;</span> {{ __('Filled / Not Required Sections') }} </p>
												<p><span class="legend-red">&#x25A0;</span> {{ __('Not Filled Sections') }} </p>
												<p>{{ __('Please fill at least one question from each section in order to be considered filled. You can click the relevent section bullet to jump on that section') }}</p>
											</div>
											<div class="review-section-list"></div>
										</div>
										<!--  -->
										</div>
										<!-- end  <div class="margin sec-body" id="section-{{ $section }}-body" num="{{ $section }}" class="sec-heading-detail" style="margin: 20px 30px; display: block;"> --><!-- btn sec -->
										</div> 
										<!-- end <div id="section-n"> -->
										<?php 
									endif; 
									?>
									<div id="{{"section-".$sec_id}}" class="form-section">
										<div class="head sec-heading" id="{{ "section-title-".$sec_id }}" num="{{ $section }}">
											<ul>
												<li><span>{{ __('Section')}} {{ $section }}</span></li>
												<li><i class="fa fa-chevron-up"></i></li>
											</ul>
											<div class="section-heading" id="{{ "section-heading-".$sec_id }}" num="{{ $section }}">
												<h3>{{ $heading }}</h3>
											</div>
										</div>
										<span class="completed-questions" id="{{"completed-questions-".$sec_id}}">{{ $question_count - 1 }}</span>
										<script>
											var filled = 0;
											section_list.push({
												'id': '{{"section-".$sec_id}}',
												'filled': filled,
												'num': {{ $sec_id }}
											});
											<?php 
												if ($question->form_id == '2' && (strcasecmp($question->admin_sec_title,'Additional Information') == 0)): 
													?> 
														optional_sections.push("<?php echo "section - ".$sec_id ?>");
													<?php 
												endif; 
											?>
										</script>
									<?php  
								endif; 
								?>

								<?php 
									if ($display_body_sec_div): 
										$display_body_sec_div = 0;
										$close_body_sec_div = 1; ?>
										<div class="margin sec-body" id="section-{{ $section }}-body" num="{{ $section }}" class="sec-heading-detail" style="margin: 20px 30px; display: block;">
										<?php 
									endif; 
								?>
								<!-- block -->
								<div class="content">
									<p></p>
									<h6 id="{{'ques-'.$question->form_key.'_'.$question->q_id}}">{{ $question->question_num}}
										@if(session('locale')=='fr')
											{{ $question->question_fr?$question->question_fr:$question->question }}
										@else
											{{ $question->question }}
										@endif
									</h6>
									<script>
										all_q_ids.push(['<?php echo $question->q_id; ?>', '<?php echo $question->form_key.'_'.$question->q_id; ?>']);
									</script>
									<?php 
										if ($question->question_comment != null && $question->question_comment != ''): ?>
											<small>{{-- $question->question_comment --}}</small>
											<?php 
										endif; 
									?>
								</div>
								<!-- block-end -->
								<div id="wrap" class="wrap-content">
									<?php 
										if ($question->question_comment != null && $question->question_comment != ''): ?>
											<h6 class="question-comment"><?php echo $question->question_comment; ?></h6>
											<?php 
										endif; 
									?>
									<?php
										$attr="data-parentSection=$parent_section";
										$type = $question->type;
										switch ($type):
											case('dd'):
												$options = explode(', ', $question->options); 
												if (!empty($options)):
												?>
													<select {{ $attr }} class="form form-control select_for_js" name="{{ $question->form_key.'_'.$question->q_id}}" q-id="{{$question->q_id}}" style="margin-bottom:20px">
														<?php
															foreach ($options as $option):
																$selected_dd = '';
																if (isset($filled[$question->form_key]) && $filled[$question->form_key]['question_response'] == $option){
																	$selected_dd = 'selected';
																}
														?>
																<option value="{{$option}}" {{$selected_dd}}>{{$option}}</option>
														<?php
															endforeach;
														?>
													</select>
													@if($showcomment)
													<div class="col-md-12 d-flex justify-content-end py-3">
														<button type="button" class="open_add_comment_model_btn btn btn-primary mr-2" q-id="{{ $question->q_id }}" form_id="{{ $question->uf_id }}"> Add comment</button>
														<button type="button" class="open_add_remidation_model_btn btn btn-primary" q-id="{{ $question->q_id }}"> Add Remidation Plan</button>
													</div>
													@endif
												<?php
												$total_questions++;														
												endif;								
												break;	
											case('cc'):
												echo $question->custom_fields;
												$total_questions++;
												break;
											case('sc'):
											case('mc'):
												$total_questions++;
												$options = explode(', ', $question->options); 
												if (!empty($options)): ?>
													<section class="options"> 
														<ul id="easySelectable" class="easySelectable">
															<?php 
																if($question->is_assets_question == 1){
																	$client_id = Auth::user()->client_id;
																	$options =  DB::table('assets')->where('client_id' , $client_id)->pluck('name');
																	$options = $options->push('Not Sure');
																}
																foreach ($options as $option):
																	$selected_class = '';
																	if (isset($filled[$question->form_key])){
																		if ($type == 'sc' && trim($filled[$question->form_key]['question_response']) == trim($option))
																		{
																			$selected_class = 'es-selected';
																		}
																		if ($type == 'mc' && in_array(trim($option), $filled[$question->form_key]['question_response']))
																		{
																			$selected_class = 'es-selected';			
																		}
																	}
																	if (strtolower($option) == 'date picker option'):
																	?>
																		<li {{ $attr }} class="es-selectable {{ $selected_class }}" pickr-num-li="{{++$date_picker_count}}" name="li-{{ $question->q_id }}" id="date-picker-li-{{$date_picker_count}}">
																			<input id="date-picker-{{$date_picker_count}}" onclick="validate_date()" class="date-picker" pickr-num="{{$date_picker_count}}" name="d-{{ $question->q_id }}" q-id="{{$question->q_id}}" placeholder="@if(session('locale')=='fr')Sélectionner une date @else Date Picker Option @endif" value="<?php if (isset($filled[$question->form_key]) && !empty($filled[$question->form_key]['additional_resp'])) echo $filled[$question->form_key]['additional_resp']; else 'Date Picker Option'; ?>" type="{{ $type }}">
																		</li>
																	<?php
																	else:?>
																		<li {{ $attr }} class="es-selectable {{ $selected_class }}" name="{{ $question->form_key.'_'.$question->q_id.(($type=='mc')?('[]'):('')) }}" q-id="{{$question->q_id}}" value="{{ $option }}" type="{{ $type }}" {{($question->question_assoc_type == 2 && $question->form_id == '2')?("assoc=1"):('')}}> {{ ucfirst(strtolower(trim($option))) }}</li>
																		<?php
																		if ($question->question_assoc_type == '2' && $question->form_id == '2'):?>
																		<script>
																			var aq_key = '{{ $question->form_key.'_ '.$question->q_id.(($type=='mc')?(' []'):('')) }}';
																			if (assoc_questions.indexOf(aq_key) == -1) {
																				assoc_questions.push(aq_key);
																			}
																		</script>
																		<?php
																		endif;
																	endif;
																endforeach;
															?>
														</ul>
														<div id="{{"perc-bar-".$question->q_id}}" class="barfiller hidden">
															<div class="tipWrap">
																<span class="tip"></span>
															</div>
															<span class="fill" id="{{"fill-bar-".$question->q_id}}" data-percentage="0"></span>
														</div>
													</section>
													@if($showcomment)
													<div class="col-md-12 d-flex justify-content-end py-3">
														<button type="button" class="open_add_comment_model_btn btn btn-primary mr-2" q-id="{{ $question->q_id }}" form_id="{{ $question->uf_id }}"> Add comment</button>
														<button type="button" class="open_add_remidation_model_btn btn btn-primary" q-id="{{ $question->q_id }}"> Add Remidation Plan</button>
													</div>
													@endif
													<?php
												endif;	
												break;
											case ('dc'):
												$total_questions++;
												// 1 FOR DATA ELEMENTS
												// 2 FOR ORG ASSETS
												// 3 FOR COUNTRIES
												// 4 TYPE OF DATA CLASSIFICATION
												// 5 ASSETS COMBINED IMPACT
												// 6 ASSETS TIER
												$value_from = $question->dropdown_value_from;
												$dynmc_values_dropdown = [];
												switch ($value_from) {
													case '1':
														$dynmc_values_dropdown = DB::table("assets_data_elements")->where('owner_id', Auth::user()->client_id)->select('id', 'name')->get()->toArray();
														break;
													case '2':
														$dynmc_values_dropdown = DB::table("assets")->where('client_id', Auth::user()->client_id)->select('id', 'name')->get();
														break;
													case '3':
														$dynmc_values_dropdown = DB::table("countries")->select('id', 'country_name AS name')->get();
														break;
													case '4':
														$dynmc_values_dropdown = DB::table("data_classifications")->where('organization_id', Auth::user()->client_id)->select('confidentiality_level as id', 'classification_name_en AS name', 'classification_name_fr AS name_fr')->get();
														break;
													case '5':
														$dynmc_values_dropdown = DB::table("impact")->select('id', 'impact_name_en AS name', 'impact_name_fr AS name_fr')->get();
														break;
													case '6':
														$dynmc_values_dropdown = DB::table("asset_tier_matrix")->select('id', 'tier_value AS name')->get();
														break;
												} ?>
												<select  class="form form-control select_for_js" name="{{ $question->form_key.'_'.$question->q_id}}" q-id="{{$question->q_id}}" style="margin-bottom:20px">

													@if(session('locale')=='fr') 
														<option value="">-- SELECT --</option>
														@if($question->not_sure_option)
															<option value="0">Not Sure</option>
														@endif
													@else
														<option value="">-- SELECT --</option>
														@if($question->not_sure_option)
															<option value="0"
															@if (isset($filled[$question->form_key]) && $filled[$question->form_key]['question_response'] == 0){
																selected
															@endif
															>Not Sure</option>
														@endif
													@endif				
													@foreach($dynmc_values_dropdown as $opt)
														<option value="{{ $opt->id }}"
														@if (isset($filled[$question->form_key]) && $filled[$question->form_key]['question_response'] == $opt->id){
															selected
														@endif
														>{{ $opt->name }}</option>
													@endforeach
												</select>
												@if($showcomment)
												<div class="col-md-12 d-flex justify-content-end py-3">
													<button type="button" class="open_add_comment_model_btn btn btn-primary mr-2" q-id="{{ $question->q_id }}" form_id="{{ $question->uf_id }}"> Add comment</button>
													<button type="button" class="open_add_remidation_model_btn btn btn-primary" q-id="{{ $question->q_id }}"> Add Remidation Plan</button>
												</div>
												@endif
												<div id="{{"perc-bar-".$question->q_id}}" class="barfiller hidden">
													<div class="tipWrap">
														<span class="tip"></span>
													</div>
													<span class="fill" id="{{"fill-bar-".$question->q_id}}" data-percentage="0"></span>
												</div>
												<?php
												break;
											case ('bl'):
											case ('qa'):
												$total_questions++;?>
												<div>
													<form>
														<label></label>
														<?php
															if (stripos($question->question, 'name') !== false || stripos($question->question, 'email') !== false || stripos($question->question, 'phone') !== false || stripos($question->question, 'city') !== false):?>
																<textarea class="textarea_for_js" {{ $attr }} name="{{ $question->form_key.'_'.$question->q_id }}" q-id="{{$question->q_id}}" style="margin-bottom:20px;max-height:35px;overflow:hidden"><?php echo (isset($filled[$question->form_key]))?($filled[$question->form_key]['question_response']):('') ?></textarea>
														<?php
															else:?>
																<textarea class="textarea_for_js" {{ $attr }} name="{{ $question->form_key.'_'.$question->q_id }}" q-id="{{$question->q_id}}" rows="4" cols="50"><?php echo (isset($filled[$question->form_key]))?($filled[$question->form_key]['question_response']):('') ?></textarea>
														<?php 
															endif;?>
														@if($showcomment)
														<div class="col-md-12 d-flex justify-content-end py-3">
															<button type="button" class="open_add_comment_model_btn btn btn-primary mr-2" q-id="{{ $question->q_id }}" form_id="{{ $question->uf_id }}"> Add comment</button>
															<button type="button" class="open_add_remidation_model_btn btn btn-primary" q-id="{{ $question->q_id }}"> Add Remidation Plan</button>
														</div>
														@endif
													</form>
												</div>
													<?php
												break;
											case('im'): ?>
												<form id="upload_form-{{ $question->q_id }}" enctype="multipart/form-data" method="POST">
													<input type="hidden" name="_token" value="{{ csrf_token()}}">
													<input type="hidden" name="user-form-id" value="{{ $questions[0]->uf_id }}">
													<input type="hidden" name="form-id" value="{{ $questions[0]->form_id }}">
													<input type="hidden" name="form-link-id" value="{{ $questions[0]->form_link_id }}">
													<input type="hidden" name="user-id" value="{{ $questions[0]->user_id }}">
													<input type="hidden" name="subform-id" value="{{ $questions[0]->sub_form_id }}">
													<input type="hidden" name="question-id" value={{ $question->q_id }}>
													<input type="hidden" name="question-key" value="{{ $question->form_key }}">
													<!-- dev -->
													<input type="hidden"  name="accepted_types" id="accepted_types_{{ $question->q_id }}" value="{{ $question->attachments }}">
													<input type="file" name="img-{{ $question->q_id }}" id="file-upload-{{$question->q_id}}" class="dropify" {{(isset($filled[$question->form_key]['question_response']) && !empty($filled[$question->form_key]['question_response']))?("data-default-file=".URL::to('public/'.$filled[$question->form_key]['question_response'])):('') }}>
													<span id="image_error"></span>
												</form> 
												@if($showcomment)
												<div class="col-md-12 d-flex justify-content-end py-3">
													<button type="button" class="open_add_comment_model_btn btn btn-primary mr-2" q-id="{{ $question->q_id }}" form_id="{{ $question->uf_id }}"> Add comment</button>
													<button type="button" class="open_add_remidation_model_btn btn btn-primary" q-id="{{ $question->q_id }}"> Add Remidation Plan</button>
												</div>
												@endif
												<?php break; ?>
												<div id="{{"perc-bar-".$question->q_id }}" class="barfiller hidden">
													<div class="tipWrap">
														<span class="tip"></span>
													</div>
													<span class="fill" id="{{"fill-bar-".$question->q_id}}" data-percentage="0"></span>
												</div> 
												<?php	
												break;
										endswitch;
								
										if ($question->additional_comments):
											?>
											<div>
												<form>
													<label>Additional comments below:</label>
													<textarea class="textarea_for_js" {{ $attr }} type="text" name="c-{{ $question->q_id.':'.str_replace('q-','', $question->form_key) }}"><?php echo (isset($filled[$question->form_key]))?($filled[$question->form_key]['question_comment']):('') ?></textarea>
												</form>
											</div>
											<?php	
										endif; ?>
									</div>
									<?php 
									endforeach; ?>
									<div class="alert alert-success hidden submit-msg" id="submit-msg" style="margin-top:20px;margin-bottom:10px">
										<h4>{{ __('Almost Done') }}!</h4>
										{{ __('Please review your answers before submitting form and click') }} <button class="btn btn-success btn-lg submit">{{ __('Submit') }}</button> 
										{{ __('once finalized') }}.
									</div>
									<div class="alert alert-warning  danger-msg " id="danger-msg" style="margin-top:20px;margin-bottom:10px">
										<h4>{{ __('All fields are required to proceed') }}</h4>
									</div>
									<div class="alert alert-info" style="margin-top:10px;">
										<h4>{{ __('Section List')}}</h4>
										<div class="user-guide">
											<p><span class="legend-green">&#x25A0;</span> {{ __('Filled / Not Required Sections') }} </p>
											<p><span class="legend-red">&#x25A0;</span> {{ __('Not Filled Sections') }} </p>
											<p>{{ __('Please fill at least one question from each section in order to be considered filled. You can click the relevent section bullet to jump on that section') }}
											</p>
										</div>
										<div class="review-section-list"></div>
									</div>
								</div>
				</div>
				
				<div class="row btn-section">
					<?php   
						$curr_sec = 1; 
						if (isset($questions[0]) && !empty($questions[0]->curr_sec))
						{
							$curr_sec = $questions[0]->curr_sec; 
						}
					?>
					<div class="col col-md-6">
						<button class="btn btn-primary btn-lg prev" sec-num="{{$curr_sec - 1}}" <?php if ($curr_sec - 1 < 1) echo 'disabled'; ?>>
							< {{ __('Previous') }}
						</button>
					</div>
					<div class="col col-md-6 text-right custbtm">
						<button onclick="topFunction()" id="myBtn_top" class="btn btn-primary btn-lg next" sec-num="{{$curr_sec + 1}}" <?php if ($curr_sec + 1 > $section) echo 'disabled'; ?>>
							{{ __('Next') }} >
						</button>
					</div>
				</div>
			</div>
			<!--  -->
		</div>

		<!-- Add Comment Model -->
		<div class="modal fade" id="add_comment_model" tabindex="-1" role="dialog" aria-labelledby="add_comment_model_title" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="add_comment_model_title">Question Comment</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<input type="hidden" id="q_id_for_comment">
					<input type="hidden" id="user_form_id_for_comment"> 
					<label for="comment_for_question">Comment</label>
					<textarea rows="5" cols="50" class="form-control" placeholder="Comment ..." id="comment_for_question"></textarea>
					<b class="text-danger mt-1" id="error_for_comment"></b>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cancel</button>
					<button type="button" class="btn btn-primary" id="add_comment_to_db">Add Comment</button>
				</div>
				</div>
			</div>
		</div>
		<!-- end -->
		<!-- Add Remidation Model -->
		<div class="modal fade" id="add_remidation_model" tabindex="-1" role="dialog" aria-labelledby="add_remidation_model_title" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
				<div class="modal-content">
				<div class="modal-header">
				<h5 class="modal-title" id="add_remidation_model_title">Remidation Plan </h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
				</div>
				<div class="modal-body">
					<input type="hidden" id="q_id_for_comment">
					<div class=" form-group py-2">
						<label for="comment_for_question">Asset Name</label>
						<select class="form-control">
							<option value="">-- SELECT --</option>
						</select>
					</div>

					<div class=" form-group py-2">
						<label for="comment_for_question">Business Unit</label>
						<select class="form-control">
							<option value="">-- SELECT --</option>
						</select>
					</div>

					<div class=" form-group py-2">
						<label for="comment_for_question">Control name</label>
						<select class="form-control">
							<option value="">-- SELECT --</option>
						</select>
					</div>

					<div class=" form-group py-2">
						<label for="comment_for_question">Initial Review</label>
						<select class="form-control">
							<option value="">-- SELECT --</option>
							@foreach($ratings as $rate)
								<option value="{{ $rate->id }}">{{ $rate->rating }}</option>
							@endforeach
						</select>
					</div>

					<div class=" form-group py-2">
						<label for="comment_for_question">Proposed Remediation</label>
						<input type="" name="" value="" class="form-control">
					</div>

					<div class=" form-group py-2">
						<label for="comment_for_question">Completed Actions</label>
						<input type="" name="" value="" class="form-control">
					</div>


					<div class=" form-group py-2">
						<label for="comment_for_question">ETA (Remediation completion date)</label>
						<input type="date" name="" value="" class="form-control">
					</div>

					<div class=" form-group py-2">
						<label for="comment_for_question">Person in Charge</label>
						<input type="" name="" value="" class="form-control">
					</div>

					<div class=" form-group py-2">
						<label for="comment_for_question">Status</label>
						<select class="form-control">
							<option value="">-- SELECT --</option>
							<option value="Analysis in Progress">Analysis in Progress</option>
							<option value="Remediation in Progress">Remediation in Progress</option>
							<option value="Remediation Applied">Remediation Applied</option>
							<option value="Risk Acceptance">Risk Acceptance</option>
							<option value="Other">Other</option>
						</select>
					</div>

					<div class=" form-group py-2">
						<label for="comment_for_question">Initial Rating (Automatic)</label>
						<select class="form-control">
							<option value="">-- SELECT --</option>
						</select>
					</div>

					<div class=" form-group py-2">
						<label for="comment_for_question">Post Remediation Rating</label>
						<select class="form-control">
							<option value="">-- SELECT --</option>
							<!-- <option value="Analysis in Progress">Analysis in Progress</option>
							<option value="Remediation in Progress">Remediation in Progress</option>
							<option value="Remediation Applied">Remediation Applied</option>
							<option value="Risk Acceptance">Risk Acceptance</option>
							<option value="Other">Other</option> -->
						</select>
					</div>
				</div>
				<div class="modal-footer">
				<button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-primary">Add Comment</button>
				</div>
				</div>
			</div>
		</div>
		<!-- End -->

		<script src="https://browser.sentry-cdn.com/7.34.0/bundle.min.js" integrity="sha384-VbKYDKfqD/Ot8dcfLdRuwWu7DcsQt0X19E5IlKO4n/VMcYrBaLWebQIOFpsJYO9N" crossorigin="anonymous" > </script>
		<!-- <script type="text/javascript" src="{{ url('public/custom_form/js/jquery.min.js') }}"></script> -->
		<script type="text/javascript" src="{{ url('public/custom_form/js/popper.min.js') }}"></script>
		<script src="{{ url('public/custom_form/js/easySelectable.js') }}"></script>
		<script type="text/javascript" src="{{ url('public/custom_form/js/cust_js.js') }}"></script>
		<script src="{{ url('public/bar-filler/js/jquery.barfiller.js') }}" type="text/javascript"></script>
		<script src="{{ url('backend/js/jquery.datetimepicker.js') }}"></script>
		<!-- <script src="{{ url('public/dropify/js/dropify.min.js') }}" type="text/javascript"></script> -->
		<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
		<script>
			$(function(){
				$(".open_add_comment_model_btn").on('click', function (event) {
					let qid = $(this).attr("q-id");
					let f_id = $(this).attr("form_id");
					console.log(qid, " -- ", f_id);
					$("#q_id_for_comment").val(qid);
					$("#user_form_id_for_comment").val(f_id);
					$('#add_comment_model').modal('show'); 
				});

				// Add comment in db
				$('#add_comment_to_db').on('click', function () {
					const comment = $('#comment_for_question').val();
					if(comment == null || comment == ""){
						$("#error_for_comment").text("* Please Add comment ");
						return;
					}else{
						$("#error_for_comment").text("");
					}
					const data = {
						q_id: $("#q_id_for_comment").val(),
						f_id: $("#user_form_id_for_comment").val(),
						comment
					}
					$.ajaxSetup({
						headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						}
					});
					$.ajax({
					url:'{{route("audit.add_comment_against_question")}}',
					method:'POST',
					data: data,
					success: function(response) {
						if (response == 1) {
							$('#add_comment_model').modal('hide'); 
							swal('Comment Successfully Added','' , 'success');
							$('#comment_for_question').val("");
						}
					}
				});		
				});


				$(".open_add_remidation_model_btn").on('click', function (event) {
					let qid = $(this).attr("q-id");
					console.log(qid);
					$('#add_remidation_model').modal('show'); 
				})
			})

			function validate_date(){
					jQuery('#date-picker-2').datetimepicker({
					timepicker:false,
					formatDate:'Y-m-d',
					minDate: $('#date-picker-1').val()  //yesterday is minimum date(for today use 0 or -1970/01/01)
					// maxDate:'+1970/01/02'//tomorrow is maximum date calendar
					});
			}
		</script>
		<script>
			var key_list = [];    
			var not_filled = [];
			function get_filled_questions_count (){
				var count = 0;
				//alert('get_filled_questions_count');
				$('li.es-selectable, .textarea_for_js, .select_for_js').each(function(){
					var key = $(this).attr('name');
					var tag = ($(this).prop('tagName')).toLowerCase(); 
					var ques_section_id     = $(this).closest('div.form-section').attr('id');
					//console.log('pl '+form_section_id);
					switch (tag) {
						case 'li':
							if ($(this).hasClass('es-selected') && key_list.indexOf(key) == -1) {
								key_list.push(key);
								// update_filled_sections(ques_section_id);

								//console.log('li');
								//console.log($(this).text())
							}
                    		break;
                
						case 'textarea':
							if (key.indexOf('q-') != -1 && ($(this).val() != '' || $(this).text() != '') && key_list.indexOf(key) == -1) {
								key_list.push(key);
								// update_filled_sections (ques_section_id);
								//console.log('textarea');
								//console.log($(this).val());
							}
							break;
						case 'select':
							if (key.indexOf('q-') != -1 && key_list.indexOf(key) == -1) {

								if ($(this).attr('case-name')) {
									if ($(this).attr('case-name') == 'asset-country') {
										if ($(this).val() != 'Select Country') {
											//console.log('Pusing '+key+' due to select');
											key_list.push(key);
											// update_filled_sections(ques_section_id);
										}
										else {
											// if not sure is selected, add this to answered questions
											//if ($('li[name="'+key+'"]').hasClass('es-selected')) {
												//console.log('Pusing '+key+' due to li');
											//    $('li[name="'+key+'"]').addClass('es-selected');
											//    key_list.push(key);
											//}
										}
									}
									else if ($(this).attr('case-name') == 'assets') {
										if ($(this).val() != 'Select Option') {
											key_list.push(key);
											// update_filled_sections(ques_section_id);
										}                                    
									}                            
									else {
										console.log('Pusing '+key+' due to select');
										key_list.push(key);
										// update_filled_sections(ques_section_id);

									}
								}
								else {
									key_list.push(key);
									// update_filled_sections(ques_section_id);							
								}
							}                    
							break;                    
						default:
							console.log('not filled');
							console.log('tag '+ tag);
							console.log('key '+ key);
							console.log($(this).text());                    
					}
				});
				//console.log('total count '+count);
        		console.log(key_list);
				// console.log("key_list");
				//console.log(section_list);
        		return key_list.length;
    		}
			
			function update_form_data_request (data){
				$.ajax({
					url:'{{route('ajax_int_user_submit_form')}}',
					method:'POST',
					data: data,
					success: function(response) {

						console.log(response);
					}
				});		
			}
	
			// get array of selected checkbox options
			function get_selected_mc_options (options_key){
				var vals = [];
				
				$('li[name="'+options_key+'"]').each(function(){
					
					if ($(this).hasClass('es-selected')) {
						vals.push($(this).attr('value'));
					}
					
				});	

				return (vals.length > 0)?(vals):('');
			}


			var prev_shown_bar_id = '';

			function check_if_all_sections_filled (){
				var all_sections_filled=[];
				$.each(section_list,function(i,list){
					var current_section_filled=[];
					$.each(list['key'],function(k,val){
						//alert(k+'---------'+val);
						if (list['q-list'][val].filter(e => e.status === true).length > 0) {
							current_section_filled.push(val); 
						}
					}); 
					if(current_section_filled.length==list['key'].length)
					all_sections_filled.push(1);
				});
        		return all_sections_filled.length == section_list.length;
    		}
			function update_filled_sections (form_section_id){
				var is_section_filled = false;
				var section           = section_list.find(el => el.id == form_section_id);
				var section_num       = section.num;

				$('div#'+form_section_id).find('li.es-selectable, .textarea_for_js[q-id], .select_for_js').each(function(){
					
				var tag = ($(this).prop('tagName')).toLowerCase(); 
				var key = $(this).attr('name');

				switch (tag) {
						case 'li':
							if ($(this).hasClass('es-selected')) {
								is_section_filled = true;
							}
							break;
						case 'textarea':
							if (key.indexOf('q-') != -1 && ($(this).val() != '' || $(this).text() != '')) {
								is_section_filled = true;
							}
							break;
						case 'select':
							if (key.indexOf('q-') != -1 && key_list.indexOf(key) == -1) {
								if ($(this).attr('case-name')) {
									if ($(this).attr('case-name') == 'asset-country') {
										if ($(this).val() != 'Select Country') {
											is_section_filled = true;
										}
									}
									else if ($(this).attr('case-name') == 'assets') {
										if ($(this).val() != 'Select Option') {
											is_section_filled = true;
										}                                    
									}                            
									else {
											is_section_filled = true;
									}
								}
							}                    
							break;
						default:
							console.log('not filled');

				}  
				
				var removeClass;
				var addClass;
				
				if (is_section_filled) {
					
					section.filled = 1;
					//removeClass    = 'arrow-red';
					addClass       = 'arrow-green';
				}
				else {

					section.filled = 0;
					//addClass       = 'arrow-red';
					removeClass    = 'arrow-green';               
				}
				
				$('a[num="'+section_num+'"]').removeClass(removeClass).addClass(addClass);            
				});
		
			}

			var prev_percentage;

			function update_progress_bar (completed_questions, id_num = '', locked = false){
				var total_questions     = {{ $total_questions }};
				var percentage = Math.ceil((completed_questions/total_questions)*100);

				if (id_num != '') {
					var form_section_id     = $('[q-id="'+id_num+'"]').closest('div.form-section').attr('id');
					//update_filled_sections(form_section_id);
					var op = 'add';
					if (percentage < prev_percentage) {
						op = 'remove';
					}
					else if (percentage == prev_percentage) {
						op = '';
					}			
					
					update_section_question_status(form_section_id, id_num, op);
					check_if_one_questions_filled_in_each_question_curr_section(form_section_id, 'id');
				}
				if ((percentage == 100  || check_if_all_sections_filled()) && locked === false) {
					$('.submit-msg').removeClass('hidden');
					$('.danger-msg').addClass('hidden');

				}
				else {
					$('.submit-msg').addClass('hidden');
					if(locked === false){
						$('.danger-msg').removeClass('hidden');
					}
					if(locked === true){
						// $('#danger-msg').hide();
						$('.danger-msg').addClass('hidden');


					}		
				}
				if (percentage > 100) {
					percentage = 100;
				}
				$('.fill').attr('data-percentage', percentage);
				prev_percentage = percentage;

				if (!locked) {
					if (id_num != '') {
					
						if (prev_shown_bar_id != '') {
							$('#perc-bar-'+prev_shown_bar_id).addClass('hidden');
						}
						
						<?php if (!$hide_pb): ?>	  
						$('#perc-bar-'+id_num).removeClass('hidden');            
						
						var qbar = $('#perc-bar-'+id_num).barfiller({
							barColor:'#3bd83f',
							animateOnResize:true
						});
						<?php endif; ?>

						prev_shown_bar_id = id_num;
						//$('#fill-bar-'+id_num).show();
						//$('#fill-bar-'+id_num).barfiller('refill');
					}        

					setTimeout(function() { 
						<?php if (!$hide_pb): ?>				
							$('#perc-bar').barfiller({ barColor:'#3bd83f', animateOnResize:true,});
						<?php endif; ?>					
					},10);	
				}
			}

			var associated_questions_indices = [];
			function get_sectionwise_questions (){
				var section;
				for (i = 0; i < section_list.length; i++) {
					section = section_list[i].id;
					section_list[i]['q-list'] = [];
					section_list[i]['key'] = [];
					var q_status = {};
					///////////////////////////////////////////////
					$('div#'+section).find('[data-parentsection]').each(function(){
						var tag = ($(this).prop('tagName')).toLowerCase();
						var key = $(this).attr('name');
						var parentsection = $(this).data('parentsection');
						
						if(section_list[i]['q-list'][parentsection]===undefined)
						section_list[i]['q-list'][parentsection]=[]; // Making question sections questions groups with key
					
						if(!section_list[i]['key'].includes(parentsection)){
						section_list[i]['key'].push(parentsection); // Putting question sections keys for later access [q-list] with that keys   
						}
						
						var is_filled = false;
						switch (tag) {
							case 'li':
								if ($(this).hasClass('es-selected')) {
									is_filled = true;
								}
								break;
							case 'textarea':
								if ($(this).val() != '') {
									is_filled = true;
								}
								break;
							case 'select':
						// 		if (key.indexOf('q-') != -1 && key_list.indexOf(key) == -1) {
									if ($(this).attr('case-name')) {
										if ($(this).attr('case-name') == 'asset-country') {
											if ($(this).val() != 'Select Country') {
												is_filled = true;
											}
										}
										else if ($(this).attr('case-name') == 'assets') {
											if ($(this).val() != 'Select Option') {
												is_filled = true;
											}
										}
										else {
											is_filled = true;
										}
									}
						// 		}
								break;
							default:
						}

						if (key.indexOf('li-') > -1) {
							var key_num = key.replace('li-', '');
							key = 'q-'+key_num+'_'+key_num;
						}
						var key_index = section_list[i]['q-list'][parentsection].findIndex(function(obj, ind){
							return (obj.key == key || obj.key == key+'[]');
						}, key);
						if (assoc_questions.indexOf(key) > -1) {
							if (key_index == -1) {
								for (index = 0; index < assoc_questions.length; index++) {
									section_list[i]['q-list'][parentsection].push({key:assoc_questions[index],status:is_filled});
								}
							}
							else {
								if (is_filled) {
									for (index = 0; index < assoc_questions.length; index++) {
										var key_index = section_list[i]['q-list'][parentsection].findIndex(function(obj, ind){
											return (obj.key == assoc_questions[index] || obj.key == assoc_questions[index]+'[]');
										}, key);
										section_list[i]['q-list'][parentsection][key_index]['status'] = is_filled;
									}
								}
							}
						}
						else {
							if (key_index == -1) { // question section already exists, then push 
								if (optional_sections.indexOf(section_list[i].id) > -1) {
									is_filled = true;
								}
								section_list[i]['q-list'][parentsection].push({key:key,status:is_filled});
							}
							else {
								if (is_filled) {
									section_list[i]['q-list'][parentsection][key_index]['status'] = is_filled;
								}
							}
						}
					});
				}
			}

			function update_section_question_status (section, question_key, op ='add'){
				get_sectionwise_questions();

			}

			function check_if_one_questions_filled_in_each_question_curr_section (section, section_value = 'num', change_next_button_status = true){
				
				var sec;
				var section_num;
				var section_filled = false;

				if (section_value == 'num') {
					sec = 'section-'+section;
					section_num = section;
				}
				else {
					sec = section;
					section_num = section.replace('section-','');
				}
				var section_ind = section_list.findIndex(function(curr_sec){
					return curr_sec.id == sec
				});

				var section = section_list[section_ind];
				var current_section_filled=[];
				$('#lengthy_length').html('');
				$('#filled').html('');
				$('#not_filled').html('');
				$.each(section['key'],function(k,val){
					$('#lengthy_length').append(section['q-list'][val].filter(e => e.status === true).length);
					if (section['q-list'][val].filter(e => e.status === true).length > 0) {
					$('#filled').append('-filled:'+k+'----'+val);
						current_section_filled.push(val); 
					}else{
						//$('#not_filled').append('-not filled:'+k+'----'+val);
						//alert('not filled-->'+k+'----'+val);
					}
				
					}); 
					console.log(current_section_filled.length +"sdfsfd"+ section['key'].length);
					if(current_section_filled.length==section['key'].length)
						section_filled=true;
					if (section_filled) {
					removeClass    = 'arrow-red';
					addClass       = 'arrow-green';
					if (change_next_button_status) {
						if (section_num < {{$section}}) {
							$('.next').prop('disabled', false);
						}
					}
				}
				else {
					addClass       = 'arrow-red';
					removeClass    = 'arrow-green';
					if (change_next_button_status) {
						$('.next').prop('disabled', true);
					}
				}

				$('a[num="'+section_num+'"]').removeClass(removeClass).addClass(addClass);
				return section_filled;
			}

			$(document).ready(function(){

				jQuery('#date-picker-2').datetimepicker({
					timepicker:false,
					formatDate:'Y-m-d',
					minDate: $('#date-picker-1').val()  
					//yesterday is minimum date(for today use 0 or -1970/01/01)
					// maxDate:'+1970/01/02'//tomorrow is maximum date calendar
				});

				
				$('.dropify').dropify();
				$('input.dropify').change(function(event){
					var q_id = this.name;
					q_id =q_id.split("-");
					q_id = q_id[1];
					// file types which will accepted o this question pecified by admin 

					let accepted_file_types = JSON.parse($(`#accepted_types_${q_id}`).val()).map(function(str) { return parseInt(str)});
					// All possible Extention for these file types 
					const all_extentions = ["", ["jpg", "png", "jpeg", "gif", "JPG", "PNG", "JPEG", "GIF"], ['docs'], ['pdf'] , ['xlxs' ,'csv'], ['zip']];
					// Extentions for Current Question
					let accepted_extentions  = [];

					for (let i =0; i < all_extentions.length; i++) {
						if (accepted_file_types.indexOf(i) != -1) {
							for (let j = 0; j < all_extentions[i].length; j++) {
								accepted_extentions.push(all_extentions[i][j]);
							}
						}
					}
					// Uploaded file whose we have to check extention upported or not 
					var uploaded_file_extention = event.target.files[0].name.split('.')[1];

					if (accepted_extentions.indexOf(uploaded_file_extention) == -1) {
						$('#image_error').text("Invalid File Formate");
						return;
					}
					$('#image_error').text("");

					$.ajax({
							url:'{{route('ajax_int_user_submit_form')}}',
							data: new FormData($("#upload_form-"+q_id)[0]),
							dataType:'json',
							async:false,
							type:'post',
							processData: false,
							contentType: false,
							success:function(response){
							console.log(response);
							},
					});
				});

				// dev
				$('.dynamic_question_answer').change(function(event){
					var q_id = this.name;
					let data = {
						id: q_id
					}
					$.ajax({
							url:'{{route('ajax_int_user_submit_form')}}',
							data: data,
							dataType:'json',
							async:false,
							type:'post',
							processData: false,
							contentType: false,
							success:function(response){
							console.log(response);
							},
					});
				});



				var num_of_form_sections = <?php echo $section; ?>;
				if (num_of_form_sections && num_of_form_sections < 2) {
					$('.btn-section').hide();
					$('.alert-info').hide();
				}            
				get_sectionwise_questions ();
				console.log('assoc_questions');

				var locked = ({{ Auth::user()->role }} != "2" && {{ $questions[0]->is_locked }} == '1')?(true):(false);
						
				if (locked == true) {
					$('#perc-bar').hide();
					console.log('hidden');
					$('input').prop('disabled', true);
					$('.textarea_for_js').prop('disabled', true);
					$('.select_for_js').prop('disabled', true);
				}
				
				var completed_questions = get_filled_questions_count();
				
				var curr_sec = {{ $curr_sec }};
				check_if_one_questions_filled_in_each_question_curr_section(curr_sec);

				//update_progress_bar(curr_sec);
				update_progress_bar(completed_questions,'', locked);
				
				$('.prev, .next').click(function (){
					var sec_num = Number($(this).attr('sec-num'));
					var next = sec_num + 1;
					var prev = sec_num - 1;     
					var total_sections = <?php echo $section; ?>;
					$('#section-'+sec_num).show();
					$('#section-'+curr_sec).hide();
					$('.next').attr('sec-num', next);

					if (!check_if_one_questions_filled_in_each_question_curr_section(sec_num)) {
						$('.next').prop('disabled', true);
					}

					if (next > total_sections) {
						$('.next').prop('disabled', true);
					}
					else {
						//$('.next').prop('disabled', false);
					}
					
					$('.prev').attr('sec-num', prev);
					if (prev < 1) {
						$('.prev').prop('disabled', true);  
					}
					else {
						$('.prev').prop('disabled', false);             
					}            
					curr_sec = sec_num;
					//update_progress_bar(curr_sec);
				});
				
				$('#section-'+curr_sec).show();
				console.log('curr_sec '+curr_sec);
				var post_data 			  = {};
				var form_id      		  = $('input[name="form-id"]').val();
				var subform_id            = $('input[name="subform-id"]').val();
				var user_form_id 		  = $('input[name="user-form-id"]').val();
				var form_link_id 		  = $('input[name="form-link-id"]').val();
				var email        		  = $('input[name="email"]').val();
				var user_id      		  = $('input[name="user-id"]').val();
				post_data['_token']       = '{{csrf_token()}}';
				post_data['form-id'] 	  = form_id;
				post_data['subform-id']   = subform_id;		
				post_data['user-form-id'] = user_form_id;
				post_data['user-id']      = user_id;
				post_data['form-link-id'] = form_link_id;
				post_data['email']        = email;		

				$('.easySelectable').easySelectable({
					filter: "li",
					state: !locked,
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
						var id                    = el.attr('q-id');
							
						if ($(el).attr('custom') && $(el).attr('custom') == '1') {
							var response_info     = {};

							if ($(el).attr('case-name') && $(el).attr('case-name') == 'Not Sure') {
								var clear_field_q_id = $(el).attr('q-id');
								$('.select_for_js[name="q-'+clear_field_q_id+'_'+clear_field_q_id+'"]').prop('selectedIndex',0);;
								$('.textarea_for_js[name^="q-'+clear_field_q_id+'_'+clear_field_q_id+'"]').val('');
								response_info['type']         = type;
								response_info['response']     = $(el).attr('value');
								post_data[question_key]       = response_info;	            
								post_data['is_response_obj']  = 1;
							}
						}
						else {
						if (type == 'mc') {					
							post_data[question_key] = get_selected_mc_options(question_key);
						}
						else {				
								post_data[question_key] = el.attr('value');
						}
						
						}
							
						
						post_data['curr-form-sec'] = curr_sec;				
						
						console.log(post_data);
						
						//return false;
						
						update_form_data_request(post_data);

						if (post_data[question_key] != '' && key_list.indexOf(question_key) == -1) {
							key_list.push(question_key);
							completed_questions++;
						}
						update_progress_bar(completed_questions,id,locked);
						delete post_data[question_key];
						delete post_data['is_response_obj'];	            
					},
					onUnSelected: function(el) {		
						var vals 				  = [];	
						var type                  = el.attr('type');
						var question_key          = el.attr('name');
						var id                    = el.attr('q-id');
						
						post_data['curr-form-sec'] = curr_sec;	
						
						if ($(el).attr('case-name') && $(el).attr('case-name') == 'Not Sure') {
							var response_info     = {};
							var clear_field_q_id = $(el).attr('q-id');
							response_info['type']         = type;
							response_info['response']     = '';
							post_data[question_key]       = response_info;	            
							post_data['is_response_obj']  = 1;
							update_form_data_request(post_data);
						}
						else {
							if ($(el).attr('type') == 'sc') {
								$(el).addClass("es-selected");	
							}
							else if ($(el).attr('type') == 'mc') {	
								post_data[question_key] = get_selected_mc_options(question_key);
								update_form_data_request(post_data);
							}
						}

						if (post_data[question_key] == '' && (ind = key_list.indexOf(question_key)) > -1) {
							key_list.splice(ind,1);
							completed_questions--;
						}

						update_progress_bar(completed_questions,id,locked);
						delete post_data[question_key];
						delete post_data['is_response_obj'];	            
					}
				});
				
				
				$('.select_for_js, .textarea_for_js').change(function(){
					var type                  = $(this).attr('type');
					var question_key          = $(this).attr('name');
					var id                    = $(this).attr('q-id');
					var mul_val_response      = {};
					var response_info         = {};
					
					if ($(this).attr('custom') && $(this).attr('custom') == 1) {
						//assets_case
						if ($(this).attr('case-name') && $(this).attr('case-name') == 'assets') {
							mul_val_response['dd']        = $(".select_for_js[case-name='assets']").val();
							mul_val_response['qa']        = $(".textarea_for_js[case-name='assets']").val();
							
							response_info['response']     = mul_val_response;
							post_data['mul_val_obj']      = 1;
							post_data['case_name']        = 'assets';
							
						}
						else if ($(this).attr('case-name') && $(this).attr('case-name') == 'multi-qa') {
							var text_vals = {};
							var field_num = 0;
							$('.textarea_for_js[name^="'+question_key+'"]').each(function() {
								text_vals[field_num++] = (($(this).val() != '') && ($(this).val() != null))?($(this).val()):('');
							});	                
							mul_val_response              = text_vals;
							response_info['response']     = mul_val_response;
							post_data['case_name']        = 'multi-qa';
							post_data['mul_val_obj']      = 2;
							var q_id_key                  = $(this).attr('q-id');

							$('li[name^="q-'+q_id_key+'_'+q_id_key+'"]').removeClass('es-selected');
							
							//console.log(post_data);
							
						}
						else if ($(this).attr('case-name') && $(this).attr('case-name') == 'asset-country') {
							post_data['case_name']        = 'asset-country';
							post_data['mul_val_obj']      = 0;
							response_info['response']     = $(this).val();
							var q_id_key                  = $(this).attr('q-id');
							
							$('li[name="q-'+q_id_key+'_'+q_id_key+'"]').removeClass('es-selected');
						}
						else {
							response_info['response']     = $(this).val();
							post_data['mul_val_obj']      = 0;
							var q_id_key                  = $(this).attr('q-id');
							$('li[name="q-'+q_id_key+'_'+q_id_key+'"]').removeClass('es-selected');
						}
						post_data['is_response_obj']  = 1;	            
						response_info['type']         = $(this).attr('type');
						post_data[question_key]       = response_info;	            
					}
					else {
						post_data['is_response_obj']  = 0;	 
						post_data['mul_val_obj']      = 1;
						post_data[question_key]   = $(this).val();
					}
					
					console.log($(this).attr('custom'));
					console.log(post_data);
					
					post_data['curr-form-sec'] = curr_sec;
					
					update_form_data_request(post_data);
								
					if (question_key.indexOf('q-') > -1 && post_data[question_key] != '' && key_list.indexOf(question_key) == -1) {
						key_list.push(question_key);
						completed_questions++;
					}
					else if (question_key.indexOf('q-') > -1 && post_data[question_key] == '' && (ind = key_list.indexOf(question_key)) > -1) {
						key_list.splice(ind,1);
						completed_questions--;
					}
											
					update_progress_bar(completed_questions,id,locked);
					delete post_data[question_key];
					delete post_data['is_response_obj'];	            
					delete post_data['mul_val_obj'];	            
				});
				
				$('.textarea_for_js').focus(function(){
					var id = $(this).attr('q-id');
					setTimeout(function(){
					get_sectionwise_questions ();
					update_progress_bar(completed_questions, id); 
					},500);
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
				
				$('.submit').click(function() {
					var data             = {};
					data['user_type']    = 'in';
					data['link_id']      = $('input[name="form-link-id"]').val();
					data['_token']       = '{{csrf_token()}}';
					
					$.ajax({
						url   :'{{route('ajax_lock_user_form')}}',
						method:'POST',
						data  : data,
						success: function(response) {
							if (response == 1) {
								window.location.href = "{{route('show_success_msg')}}";
							}
							// console.log(response);
						}
					});	
				});
			
				var review_sections_ul = '<div style="margin-top:10px;">';
				var review_section_num;
				var review_section_heading;
				
				$('div.section-heading').each(function(){
					var review_section_num     = $(this).attr('num');
					var review_section_heading = $(this).find('h3').text();
					var curr_section           = section_list.find(el => el.num == review_section_num);
					console.log('curr_section ');
					console.log(curr_section);
					var arrow_class_color = 'red';
					if (check_if_one_questions_filled_in_each_question_curr_section(review_section_num, 'num', false))
					{
						arrow_class_color = 'green';
					}
					review_sections_ul += '<a href="#" class="sec-review-links arrow-'+arrow_class_color+'" style="margin-top:10px; margin-left:10px;" num="'+review_section_num+'">'+'Section '+review_section_num+': '+review_section_heading+'</a>';
				});
				
				review_sections_ul += '</div>';
				
				$('.review-section-list').html(review_sections_ul);
				
				$(document).on('click', '.sec-review-links', function(event){
					var total_sections = <?php echo $section; ?>;
					var sec_link = event.currentTarget;
					var sec_to_show = $(sec_link).attr('num');
					$('.form-section').hide();
					$('#section-'+sec_to_show).show();
					var new_prev_num = Number(sec_to_show)-1;
					var new_next_num = Number(sec_to_show)+1;
					$('.prev').attr('sec-num', new_prev_num);
					if (new_prev_num < 1) {
						$('.prev').prop('disabled', true);
					}
					else {
						$('.prev').prop('disabled', false);
					}
					$('.next').attr('sec-num', new_next_num);
					if (new_next_num > total_sections) {
						$('.next').prop('disabled', true);
					}
					else {
						$('.next').prop('disabled', false);
					}
					curr_sec = sec_to_show;
					check_if_one_questions_filled_in_each_question_curr_section(sec_to_show);
				});
				
				<?php for ($dp_num = 1; $dp_num <= $date_picker_count; $dp_num++): ?>
					jQuery('#date-picker-<?php echo $dp_num; ?>').datetimepicker({
						timepicker:false,
						format:'Y-m-d',
						onSelectDate: function (ct) {
							$('#date-picker-li-<?php echo $dp_num; ?>').siblings().each(function(i, li){
								$(li).removeClass('es-selected');
							});
							$('#date-picker-li-<?php echo $dp_num; ?>').addClass('es-selected');								
							var selectedDate = ct.dateFormat('Y-m-d');
							$('#date-picker-<?php echo $dp_num; ?>').val(selectedDate).text(selectedDate);
							
							var type                  = $('#date-picker-<?php echo $dp_num; ?>').attr('type');
							var id                    = $('#date-picker-<?php echo $dp_num; ?>').attr('q-id');
							var question_key          = 'd-'+id;
											
							//console.log('question key ' + question_key);
							//console.log('id ' + id);
							
							
							post_data[question_key] = selectedDate;
							question_key 			= 'q-'+id;
							
							post_data['curr-form-sec'] = curr_sec;				
							
							console.log(post_data);
											
							update_form_data_request(post_data);
							
							if (post_data[question_key] != '' && key_list.indexOf(question_key) == -1) {
								key_list.push(question_key);
								completed_questions++;
							}
							update_progress_bar(completed_questions,id,locked);	
							delete post_data[question_key];
						}
					});		
				
					$('#date-picker-li-<?php echo $dp_num; ?>').click(function(event){
						event.preventDefault();
						if ($('#date-picker-<?php echo $dp_num; ?>').val() != '') {
							if (!$(this).hasClass('es-selected')) {
								$(this).addClass('es-selected');
							}
						}
						else {
							$(this).removeClass('es-selected');	
						}

						
						$('#date-picker-<?php echo $dp_num; ?>').datetimepicker('show');
					});
				<?php endfor; ?>
			});
		</script>
		<script>
			//Get the button
			var mybutton = document.getElementById("myBtn_top");
			// When the user scrolls down 20px from the top of the document, show the button
			window.onscroll = function() {scrollFunction()};
			function scrollFunction() {
				if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
					mybutton.style.display = "block";
				} else {
					mybutton.style.display = "none";
				}
			}

			// When the user clicks on the button, scroll to the top of the document
			function topFunction() {
				document.body.scrollTop = 0;
				document.documentElement.scrollTop = 0;
			}
		</script>
@endsection