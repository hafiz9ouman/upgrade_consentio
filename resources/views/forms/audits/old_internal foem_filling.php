@extends('admin.client.client_app', ['load_admin_css' => true])
@section('content')
@section('title')
{{ $form_details->title }}
@endsection
	<link rel="stylesheet" type="text/css" href="{{ url('public/custom_form/css/style.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ url('public/bar-filler/css/style.css') }}" />
	<link rel="stylesheet" type="text/css" href="{{ url('backend/css/jquery.datetimepicker.css') }}" />
	<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css">
	<style>	
        #render_area p{
            border-bottom : 0 !important;
        }
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
            width: 100%;
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
	<body>
        <div class="row bg-white py-3 ">  

            <div class="col-12 d-flex justify-content-end"></div>  

			<div class="col-12 p-3 bg-light">
				<div id="bar_top" class="barfiller"></div>
			</div>

            <div class="col-12 p-5 " style="overflow-y: auto;">

				<div class="col-12 d-flex justify-content-end" id="show_remediation_plan">
				</div>

                <span class="w-100" type="button" data-toggle="collapse" data-target="#questions_area" aria-expanded="true" aria-controls="questions_area">
                    <div id="form-heading" class="head">
                        <ul>
                            <li><strong>★</strong></li>
                            <li><i class="fa fa-chevron-up" aria-hidden="true"></i></li>
                        </ul>
                        <h3 id="title">{{ $form_details->title }}</h3>
                    </div>
                </span>

                <div class="collapse show" id="questions_area">
                    <div class="row" id="render_area">
						<input type="hidden" id="form_details" 
							user_form_id= "{{ $user_form_link_info->id }}"  
							sub_form_id	= "{{$user_form_link_info->sub_form_id}}" 
							form_id		= "{{$form_id}}"
							group_id	= "{{$group}}"
							group_id	= "{{$group}}"
							locked		= "{{$user_form_link_info->is_locked}}"
							local		= "{{session('locale')}}"
							admin		= "{{Auth::user()->role}}"
						>
                        {{-- @foreach($form_details->form->group->questions as $question)
                            <div class="col-12 my-2">
                                <div class="card">
                                    <div class="content p-3">
                                        @if(session('locale')=='fr')
                                            <h6> {{$question->question_num}}. {{ $question->question_fr }} </h6> 
                                            @if($question->question_comment_fr)
                                                <h6 class="question-comment"><small class="mr-3"><span class="edit_comment">{{ $question->question_comment_fr }}</span></small></h6>
                                            @endif   
                                        @else
                                            <h6> {{$question->question_num}}. {{ $question->question }} </h6>    
                                            @if($question->question_comment)
                                                <h6 class="question-comment mt-0"><small class="mr-3"><span class="edit_comment">{{ $question->question_comment }}</span></small></h6>
                                            @endif 
                                        @endif
                                        @switch($question->type)
                                            @case('qa')
                                                <textarea class="textarea_for_js" question_key="qa-{{$question->id}}" q-id="{{ $question->id }}" type="{{ $question->type }}" rows="4"   @if ($user_form_link_info->is_locked == 1) disabled = "true"@endif >@if(isset($filled_info) && isset($filled_info[$question->id])){{$filled_info[$question->id]['question_response']}}@endif</textarea>
												@if($question->attachment_allow)
													<div class="pt-2">
													<label for="">Attachment</label>
														<form id="question_with_attachment_{{$question->id}}" enctype="multipart/form-data" method="POST">
															<input type="hidden" name="_token" value="{{ csrf_token()}}">
															<input type="hidden" name="user_form_id" value="{{ $user_form_link_info->id }}">
															<input type="hidden" name="form_id" value="{{ $form_id }}">
															<input type="hidden" name="sub_form_id" value="{{ $user_form_link_info->sub_form_id }}">
															<input type="hidden" name="q_id" value="{{ $question->id }}">
															<input type="hidden" name="question_key" value="im-{{ $question->form_key }}">
															<input type="hidden" name="accepted_types" id="file_accepted_types_{{$question->id}}" value="{{$question->accepted_formates}}">
															<input type="file" name="img-{{ $question->id }}" q_id="{{ $question->id }}" id="file-upload-{{$question->id}}"  class="attachment_file" {{ isset($filled_info) && isset($filled_info[$question->id]) && !empty($filled_info[$question->id]['attachment']) ? "data-default-file=".URL::to('public/'.$filled_info[$question->id]['attachment']):''}} }}>
															<span id="attachment_error_{{$question->q_id}}"></span>
														</form> 
													</div>
												@endif
                                                @break
                                            @case('mc')
                                                @php  
                                                    $options = explode(', ', $question->options);
                                                @endphp

                                                @if(!empty($options))
													<ul id="easySelectable" class="easySelectable">
														@foreach ($options as $option)
															@php 
																$selected_class = '';
																$checked = 0;
																if( isset($filled_info) &&  isset($filled_info[$question->id]) ) {
																	if(in_array($option, $filled_info[$question->id]['question_response'])){
																		$selected_class = 'es-selected';
																		$checked = 1;
																	}
																}
															@endphp
															<li type="mc" class="es-selectable {{ $selected_class }}" check="{{$checked}}" @if ($user_form_link_info->is_locked == 1) disabled = "true" @else onclick="get_selected_options(event)" @endif  name="multi_select_{{$question->id}}" q-id="{{$question->id}}" value="{{ $option }}" type="{{ $question->type }}" {{($question->question_assoc_type == 2 && $question->form_id == '2')?("assoc=1"):('')}}> {{ ucfirst(strtolower(trim($option))) }}  </li>
														@endforeach
													</ul>
													@if($question->attachment_allow)
														<div class="pt-2">
														<label for="">Attachment </label>
															<form id="question_with_attachment_{{$question->id}}" enctype="multipart/form-data" method="POST">
																<input type="hidden" name="_token" value="{{ csrf_token()}}">
																<input type="hidden" name="user_form_id" value="{{ $user_form_link_info->id }}">
																<input type="hidden" name="form_id" value="{{ $form_id }}">
																<input type="hidden" name="sub_form_id" value="{{ $user_form_link_info->sub_form_id }}">
																<input type="hidden" name="q_id" value="{{ $question->id }}">
																<input type="hidden" name="question_key" value="im-{{ $question->form_key }}">
																<input type="hidden" name="accepted_types" id="file_accepted_types_{{$question->id}}" value="{{$question->accepted_formates}}">
																<input type="file" name="img-{{ $question->id }}" q_id="{{ $question->id }}" id="file-upload-{{$question->id}}" class="attachment_file" {{ isset($filled_info) && isset($filled_info[$question->id]) && !empty($filled_info[$question->id]['attachment']) ? "data-default-file=".URL::to('public/'.$filled_info[$question->id]['attachment']):''}} >
																<span id="attachment_error_{{$question->q_id}}"></span>
															</form> 
														</div>
													@endif
                                                @endif

                                                @break
                                            @case('sc')
												@php  
                                                    $options = explode(', ', $question->options);
                                                @endphp
												
                                                @if(!empty($options))
													<ul id="easySelectable" class="easySelectable">
														@foreach ($options as $option)
															@php 
																$selected_class = '';
																$checked = 0;
																if( isset($filled_info) &&  isset($filled_info[$question->id]) ) {
																	if($option == $filled_info[$question->id]['question_response']){
																		$selected_class = 'es-selected';
																		$checked = 1;
																	}
																}
															@endphp
															<li type="sc" class="es-selectable {{ $selected_class }}" check="{{ $checked }}" @if ($user_form_link_info->is_locked == 1) disabled = "true" @else onclick="get_selected_options(event)" @endif  name="multi_select_{{$question->id}}" q-id="{{$question->id}}" value="{{ $option }}" type="{{ $question->type }}" {{($question->question_assoc_type == 2 && $question->form_id == '2')?("assoc=1"):('')}}> {{ ucfirst(strtolower(trim($option))) }}  </li>
														@endforeach
													</ul>
                                                @endif

												@if($question->attachment_allow)
													<div class="pt-2">
													<label for="">Attachment</label>
														<form id="question_with_attachment_{{$question->id}}" enctype="multipart/form-data" method="POST">
															<input type="hidden" name="_token" value="{{ csrf_token()}}">
															<input type="hidden" name="user_form_id" value="{{ $user_form_link_info->id }}">
															<input type="hidden" name="form_id" value="{{ $form_id }}">
															<input type="hidden" name="sub_form_id" value="{{ $user_form_link_info->sub_form_id }}">
															<input type="hidden" name="q_id" value="{{ $question->id }}">
															<input type="hidden" name="question_key" value="im-{{ $question->form_key }}">
															<input type="hidden" name="accepted_types" id="file_accepted_types_{{$question->id}}" value="{{$question->accepted_formates}}">
															<input type="file" name="img-{{ $question->id }}" q_id="{{ $question->id }}" id="file-upload-{{$question->id}}" class="attachment_file" {{ isset($filled_info) && isset($filled_info[$question->id]) && !empty($filled_info[$question->id]['attachment']) ? "data-default-file=".URL::to('public/'.$filled_info[$question->id]['attachment']):''}}>
															<span id="attachment_error_{{$question->q_id}}"></span>
														</form> 
													</div>
												@endif
                                                @break
                                            @case('im')
                                                @php    
                                                    $accepted_formates  = ['Images', '.docs', '.pdf', '.xlxs , .csv', '.zip'];
                                                    $attachments        = json_decode($question->accepted_formates);
                                                @endphp 

                                                <form id="upload_form-{{ $question->id }}" enctype="multipart/form-data" method="POST" class="pb-3">
                                                    <input type="hidden" name="_token"            value="{{ csrf_token()}}">
                                                    <input type="hidden" name="user-form-id"      value="{{ $user_form_link_info->id}}">
                                                    <input type="hidden" name="form-id"           value="{{ $form_id}}">
                                                    <input type="hidden" name="form-link-id"      value="{{ $user_form_link_info->form_link_id}}">
                                                    <input type="hidden" name="user-id"           value="{{ $user_form_link_info->user_id}}1">
                                                    <input type="hidden" name="subform-id"        value="{{ $user_form_link_info->sub_form_id }}">
                                                    <input type="hidden" name="question-id"       value="{{ $question->id }}">
                                                    <input type="hidden" name="question-key"      	  value="im-{{ $question->form_key }}">
                                                    <input type="hidden" name="accepted_types"    value="{{ $question->accepted_formates }}" id="accepted_types_{{ $question->id }}">
                                                    <input type="file" name="img-{{ $question->id }}" id="file-upload-{{$question->id}}" class="dropify" 
													{{ isset($filled_info) && isset($filled_info[$question->id]) && !empty($filled_info[$question->id]['question_response']) ? "data-default-file=".URL::to('public/'.$filled_info[$question->id]['question_response']):''  }} >
                                                    <span id="image_error{{$question->id}}"></span>
                                                </form> 
												@if($attachments)
                                                    <h6 class="mt-3"> {{ __('Accepted Formates')}} </h6>
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        {{ in_array($i, $attachments) ? $accepted_formates[$i - 1]: '' }}, &nbsp;
                                                    @endfor
                                                @endif
                                                @break
                                            @case ('dc')
												@php
													$dynmc_values_dropdown = [];
													switch ($question->dropdown_value_from){
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
													}
												@endphp
												<select  class="select_box_for_js form-control" question_key="dc-{{$question->id}}" q-id="{{ $question->id }}" type="{{ $question->type }}"  style="margin-bottom:20px" @if ($user_form_link_info->is_locked == 1) disabled = "true"  @endif>
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
														@if (isset($filled_info) && isset($filled_info[$question->id]) && $filled_info[$question->id]['question_response'] == $opt->id)
															selected
														@endif
														>{{ $opt->name }}</option>
													@endforeach
												</select>
												@if($question->attachment_allow)
													<div class="pt-2">
													<label for="">Attachment</label>
														<form id="question_with_attachment_{{$question->id}}" enctype="multipart/form-data" method="POST">
															<input type="hidden" name="_token" value="{{ csrf_token()}}">
															<input type="hidden" name="user_form_id" value="{{ $user_form_link_info->id }}">
															<input type="hidden" name="form_id" value="{{ $form_id }}">
															<input type="hidden" name="sub_form_id" value="{{ $user_form_link_info->sub_form_id }}">
															<input type="hidden" name="q_id" value="{{ $question->id }}">
															<input type="hidden" name="question_key" value="im-{{ $question->form_key }}">
															<input type="hidden" name="accepted_types" id="file_accepted_types_{{$question->id}}" value="{{$question->accepted_formates}}">
															<input type="file"   name="img-{{ $question->id }}" q_id="{{ $question->id }}" id="file-upload-{{$question->id}}" class="attachment_file"  {{ isset($filled_info) && isset($filled_info[$question->id]) && !empty($filled_info[$question->id]['attachment']) ? "data-default-file=".URL::to('public/'.$filled_info[$question->id]['attachment']):''}} >
															<span id="attachment_error_{{$question->q_id}}"></span>
														</form> 
													</div>
												@endif
												@break;                
                                            @default
                                        @endswitch
										<div id="bar_{{$question->id}}" class="barfiller"></div>
										@if($user_form_link_info->is_locked == 1)
											<div class="col-md-12 py-3">
												<div class="w-100 mr-3">
													<label>Assessment </label>
													{{-- @foreach($eval_ratings as $rate)
													@if($filled_info[$question->id]['rating'] ==  $rate->id) 
													{{ $rate->rating }}
													@endif 
													@endforeach --}}

													<select class="form-control" class="add_rating_in_db" onchange="add_question_rating_in_db(event)" q-id="{{ $question->id }}">
														@if(isset($filled_info) && isset($filled_info[$question->id]) && $filled_info[$question->id]['rating'] == 0)
															<option value="">-- SELECT Assessment --</option>
														@endif
														@foreach($eval_ratings as $rate)
														<option value="{{ $rate->id }}" @if($filled_info[$question->id]['rating'] ==  $rate->id) selected @endif>{{ $rate->assessment }}</option>
														@endforeach
													</select>
												</div>
											</div>
											<div class="col-md-12 py-3">
												<label>Review Comment</label>
												<textarea rows="4"   class="form-control comment_for_question" placeholder="Comment ..."  q-id="{{ $question->id }}">@if(isset($filled_info) && isset($filled_info[$question->id])){{$filled_info[$question->id]['admin_comment']}}@endif</textarea>
											</div>
										@endif
                                    </div>
                                </div>
                            </div>
                        @endforeach	 --}}
                    </div>
                </div>
            </div>
		</div>

		<div class="row bg-white py-3">
			<div class="col-md-12">
				<h1>Hell__</h1>
			</div>
			<div class="col-12 p-5 hidden" id="submit_btn">
				<div class="row alert alert-success">
					<div class="col-md-8">
						<h4>{{ __('Almost Done') }}!</h4>
						{{ __('Please review your answers before submitting form and click') }} 
						{{ __('once finalized.') }}
					</div>
					<div class="col-md-4">
						<button class="btn btn-success btn-lg submit" onclick="lock_form()">{{ __('Submit') }}</button> 
					</div>
				</div>
			</div>
        </div>

		<script src="https://browser.sentry-cdn.com/7.34.0/bundle.min.js"></script>
		<!-- <script type="text/javascript" src="{{ url('public/custom_form/js/jquery.min.js') }}"></script> -->
		<script type="text/javascript" src="{{ url('public/custom_form/js/popper.min.js') }}"></script>
		<script src="{{ url('public/custom_form/js/easySelectable.js') }}"></script>
		<script type="text/javascript" src="{{ url('public/custom_form/js/cust_js.js') }}"></script>
		<script src="{{ url('public/bar-filler/js/jquery.barfiller.js') }}" type="text/javascript"></script>
		<script src="{{ url('backend/js/jquery.datetimepicker.js') }}"></script>
		<!-- <script src="{{ url('public/dropify/js/dropify.min.js') }}" type="text/javascript"></script> -->
		<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
		<script>
			$('.dropify').dropify();
			$('.attachment_file').dropify();

			$(function(){

				$('.select_box_for_js , .textarea_for_js').on('change', function() {
					var type = $(this).attr('type');
					const data = {
						type: 				$(this).attr('type'),
					 	question_key: 		$(this).attr('question_key'),
					 	q_id : 				$(this).attr('q-id'),
						sub_form_id:		$('#form_details').attr("sub_form_id"),
						user_form_id:		$('#form_details').attr("user_form_id"),
						form_id:		   	$('#form_details').attr("form_id"),
						ansswer:			$(this).val()
					} 
					submit_answer(data, $(this).attr('q-id'));
				});

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
					var uploaded_file_extention = event.target.files[0].name.split('.').pop();

					console.log("accepted_extentions", accepted_extentions);
					console.log("uploaded_file_extention", uploaded_file_extention);

					if (accepted_extentions.indexOf(uploaded_file_extention) == -1) {
						$('#image_error'+q_id).html("Invalid File Formate");
						return;
					}else{
						$('#image_error'+q_id).text("");
					}
					$.ajax({
							url:'{{route('ajax_int_user_submit_audit')}}',
							data: new FormData($("#upload_form-"+q_id)[0]),
							dataType:'json',
							async:false,
							type:'post',
							processData: false,
							contentType: false,
							success:function(response){
								console.log(response);
								counts(q_id);
							},
					});
				});

				$('input.attachment_file').change(function(event){

					var q_id = $(this).attr('q_id');
					if ($(`#file_accepted_types_${q_id}`).val() != 0) {
						// file types which will accepted o this question pecified by admin 
						let accepted_file_types = JSON.parse($(`#file_accepted_types_${q_id}`).val()).map(function(str) { return parseInt(str)});
						// All possible Extention for these file types 
						const all_extentions = ["", ["jpg", "png", "jpeg", "gif", "JPG", "PNG", "JPEG", "GIF"], ['docs'], ['pdf'] , ['xlxs' ,'csv'], ['zip']];
						// Extentions for Current Question
						let accepted_extentions  = [];
						for (let i =0; i < all_extentions.length; i++){
							if (accepted_file_types.indexOf(i) != -1) {
								for (let j = 0; j < all_extentions[i].length; j++) {
									accepted_extentions.push(all_extentions[i][j]);
								}
							}
						}
						// Uploaded file whose we have to check extention upported or not 
						var uploaded_file_extention = event.target.files[0].name.split('.').pop();
	
						if (accepted_extentions.indexOf(uploaded_file_extention) == -1) {
							$('#attachment_error_'+q_id).html("Invalid File Formate");
							return;
						}else{
							$('#attachment_error_'+q_id).text("");
						}
					}

					$.ajaxSetup({
						headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						}
					});
					$.ajax({
							url:'{{route("add_attachment_to_question")}}',
							data: new FormData($("#question_with_attachment_"+q_id)[0]),
							dataType:'json',
							async:false,
							type:'post',
							processData: false,
							contentType: false,
							success:function(response){
								console.log(response);
								counts(q_id);
							},
					});
				});
			})

			function get_selected_options(event){

				const name = event.target.getAttribute('name');
				const type = event.target.getAttribute('type');
				var options = [];
				if (type == 'mc') {
					$(event.target).attr('check', '1'); 
					$(event.target).attr('class', 'es-selectable es-selected');
					$.each($(`li[name=${name}]`), function(){
						if($(this).attr('check') == 1){
							options.push($(this).attr('value'))
						}
					});
					
				}else{
					$(`li[name=${name}]`).attr('check', '1'); 
					$(`li[name=${name}]`).attr('class', 'es-selectable');
					$(event.target).attr('check', '1'); 
					$(event.target).attr('class', 'es-selectable es-selected');
					var options = event.target.getAttribute('value')
				}
				console.log("Selected Options 123", options);
				const data = {
					type: 				event.target.getAttribute('type'),
					question_key: 		event.target.getAttribute('question_key'),
					q_id : 				event.target.getAttribute('q-id'),
					sub_form_id:		$('#form_details').attr("sub_form_id"),
					user_form_id:		$('#form_details').attr("user_form_id"),
					form_id:		   	$('#form_details').attr("form_id"),
					ansswer:			options
				} 
				console.log("data", data);
				submit_answer(data, event.target.getAttribute('q-id'));
			}

			function submit_answer(data, id){
				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
				$.ajax({
					url:'{{route('ajax_int_user_submit_audit')}}',
					method:'POST',
					data: data,
					success: function(response) {
						console.log(response);
						counts(id);
					}
				});		
			}

			function counts(id = 0){
				console.log(" --->>> id inside counts", id);
				const group = $('#form_details').attr('group_id');
				const sub_form_id = $('#form_details').attr('sub_form_id');
				const locked = $('#form_details').attr('locked');
				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
				$.ajax({
					url: "/audit/count/"+group+"/"+sub_form_id,
					method: 'GET',
					success: function(response) {
						console.log("response", response);
						if (response.total_questions == response.responded_questions && locked != 1) {
							$("#submit_btn").removeClass('hidden');
						}

						if (response.week_questions > 0 && response.remediation_added > 0) {
							
							$('#show_remediation_plan').html(`<a class="btn btn-primary text-white" href="/audit/remediation">{{__('Show Remediation plan')}}</a>`)
						}
						else if(response.total_questions == response.added_ratting && response.week_questions > 0) {
							$('#show_remediation_plan').html(`<a class="btn btn-primary text-white" href="/audit/remediation/add/{{$user_form_link_info->sub_form_id}}">{{__('Add Remediation plan')}}</a>`)
						}
						else{
							$('#show_remediation_plan').html("");
						}
						if (locked != 1) {
							const percent = Math.floor(( response.responded_questions / response.total_questions ) * 100);
							// $("#bar_top").append(`<span class="tip"></span><span class="fill" id="fill-bar" data-percentage="${percent}" style="background-color: #12d519 !important;"></span>`);
							// $(`#bar_${id}`).append(`<span class="tip"></span><span class="fill" id="fill-bar" data-percentage="${percent}" style="background-color: #12d519 !important;"></span>`);
	
							console.log("percent", percent);
							$('.barfiller').html("");
							// $(`#bar_top`).barfiller();
							// $(`#bar_${id}`).barfiller();
							$("#bar_top").append(`<h4>${percent}%</h4>`);
							$(`#bar_${id}`).append(`<h4>${percent}%</h4>`);
							console.log("barfiller should work ");
						}

					}
				}); 
			}counts(0);

			function lock_form(){
				var data             = {};
				data['user_id']    	 = <?php echo Auth::user()->id ?>;
				data['user_type']    = 'in';
				data['sub_form_id']  = $('#form_details').attr('sub_form_id');
				$.ajax({
					url   :'{{route('ajax_lock_user_audit_form')}}',
					method:'POST',
					data  : data,
					success: function(response) {
						if (response == 1) {
							window.location.href = "{{route('show_audit_success_msg')}}";
						}
						console.log(response);
					}
				});	
			}

			function add_question_rating_in_db(event) {
				const data = {
					rating		: event.target.value,
					q_id		: $(event.target).attr("q-id"),
					sub_form_id	: $("#form_details").attr("sub_form_id"),
					form_id		: $("#form_details").attr("form_id"),
				}
				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
				$.ajax({
					url:'{{route("audit.add_rating_against_question")}}',
					method:'POST',
					data: data,
					success: function(response){

						counts($(event.target).attr("q-id"));
						console.log(response);
						// if (response == 1) {
						// }
					}
				});	
			}
			
			function show_add_remediation_model() {
				const sub_form_id = $('#form_details').attr('sub_form_id');
				let local 		  = $(`#form_details`).attr('local');
				$.ajax({

					url: "/audit/remediation/controls/"+sub_form_id,
					method: 'GET',
					success: function(response) {
						console.log(response.status);
						if(response.status){
							$(`#append_questions`).html("");
							let questions = response.questions
							questions.map((question, index)=>{
								var display = "";
								if(local == "fr") {
									display = question.question_short_fr
								}else{
									display = question.question_short
								}
								$(`#append_questions`).append(`<h6>${index+1}: ${display}</h6>`);
							});
						}
						
						
						$(`#add_remediation_model`).modal('show');
					}
				}); 
			}

			$('.comment_for_question').on('change', function (event) {
				const data = {
					q_id:	 $(this).attr("q-id"),
					f_id: $("#form_details").attr("sub_form_id"),
					comment: $(this).val()
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
		</script>
	</body>
@endsection