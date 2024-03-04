@extends ((Auth::user()->role == 1)?('admin.layouts.admin_app'):('admin.client.client_app'))
@section('page_title')
    {{ __('Audit Form Details') }}
@endsection
@section('content')
	<link rel="stylesheet" type="text/css" href="{{ url('public/custom_form/css/style.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ url('public/bar-filler/css/style.css') }}" />
	<link rel="stylesheet" type="text/css" href="{{ url('backend/css/jquery.datetimepicker.css') }}" />
	<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css">
    <style>
        .content p {
            border-bottom : 0;
        }
        .head {
            background-color: #0f75bd;
        }
        .head h3, .fork i, .btn:hover {
            color: #FCFCFC;
        }
        .head ul {
            background-color: #73b84d;
        }
        textarea {
            border-radius: 6px;
            width: 100%;
        }
        .edit_enable {
            display:none;
        }
        .handle_hover:hover .edit_enable{
            display:block;
        }
    </style>
	<div class="bg-light px-4">
		<div class="row p-0">  
			<div class="col-12 p-0 m-0">
				<div id="" class="bg-danger">
					<div class="head" id="form-heading">
						<ul>
							<li><strong>★</strong></li>
							<li><i class="fa fa-chevron-up"></i></li>
						</ul>
						<h3>{{ $form_details->title_fr }}</h3> 
					</div>
				</div>
			</div> 
		</div>

		<div class="row bg-white pt-3" style="margin-top: -18px;">  
			@foreach($form_details->group->sections as $section)
				<div class="col-12 p-3 " style="overflow-y:auto;">
					<span class="w-100" type="button">
						<div id="form-heading" class="head"   data-toggle="collapse" data-target="#questions_area{{$section->id}}" aria-expanded="true" aria-controls="questions_area{{$section->id}}">
							<ul>
								<li><strong>★</strong></li>
								<li><i class="fa fa-chevron-up" aria-hidden="true"></i></li>
							</ul>
							<div class="w-100 px-4 d-flex justify-content-between">
								<h3 id="title">{{ $section->section_title }}</h3>
							</div>
						</div>
					</span>
					<div class="collapse show" id="questions_area{{$section->id}}">
						<div class="row" id="render_area">
							@foreach($section->questions as $question)
								<div class="col-12 my-3">
									<div class="card shadow handle_hover">
										<div class="content p-3 py-4">
											<div class="d-flex justify-content-between">
												<span>
													{{$section->number}}.{{ $question->question_num }}
												</span>
											</div>

											<h6 id="display_english_question_{{$question->id}}">
												<strong class="mr-3">En Q: </strong>
												<span class="edit_question_english" onclick="edit_question_ajax(event)" id="edit_en_q_{{$question->id}}" q_id="{{$question->id}}" q_val="{{$question->question}}" value="{{$question->question}}" type="english">{{$question->question}}</span> 
											</h6>
											<div class="d-flex align-items-end" id="append_div_to_edit_english_{{$question->id}}"></div>

											@if($question->question_comment)
												<h6 class="question-comment" data-toggle="tooltip" data-placement="left" title="" data-original-title="Click To Edit English Question Comment">
													<small class="d-flex" id="display_english_comment_{{$question->id}}"> En Comment:&nbsp;&nbsp;<span class="edit_english_comment" onclick="edit_question_ajax(event)" id="edit_en_c_{{$question->id}}" q_id="{{$question->id}}" q_val="{{$question->question_comment}}" type="en_comment" value="{{$question->question_comment}}">{{ $question->question_comment }}</span></small>
												</h6>
												<div class="d-flex align-items-end" id="append_div_to_edit_en_comment_{{$question->id}}"></div>
											@endif

											<h6 id="display_fr_question{{$question->id}}" data-toggle="tooltip" data-placement="left" title="" data-original-title="Question Title French">
												<strong class="mr-3">Fr Q: </strong>
												<span class="edit_question_fr ml-1" onclick="edit_question_ajax(event)" id="edit_fr_q_{{$question->id}}" q_id="{{$question->id}}" q_val="{{$question->question_fr}}" type="fr" value="{{$question->question_fr}}">{{ $question->question_fr }}</span>
											</h6>
											<div class="d-flex align-items-end" id="append_div_to_edit_fr_{{$question->id}}"></div>

											@if($question->question_comment_fr)
												<h6 class="question-comment " data-toggle="tooltip" data-placement="left" title="" data-original-title="Click To Edit French Question Comment">
													<small class="mr-3" id="display_fr_comment{{$question->id}}">Fr Comment: 
														<span class="edit_fr_comment" onclick="edit_question_ajax(event)" id="edit_fr_c_{{$question->id}}" q_id="{{$question->id}}" q_val="{{$question->question_comment_fr}}" type="fr_comment" value="{{$question->question_comment_fr}}">{{ $question->question_comment_fr }}</span>
													</small>
												</h6>
												<div class="d-flex align-items-end" id="append_div_to_edit_fr_comment_{{$question->id}}"></div>
											@endif

											@switch($question->type)
												@case('qa')
													<div>
														<textarea name="" rows="4"" disabled></textarea>
														@if ($question->not_sure_option == 'true')
														<ul id="easySelectable" class="easySelectable">
															<li class="es-selectable" name="" value="not sure" type="mc">Not Sure</li>
														</ul>
														@endif
													</div>
													@break
												@case('mc')
													<section class="options" id="">
														@php 
															$options    = explode(',', $question->options);
															$options_fr = explode(',', $question->options_fr);
														@endphp
														<label for="easySelectable">Englih Options</label>
														<ul id="easySelectable" class="easySelectable">
															@foreach($options as $option)
															<li class="es-selectable " name="" value="Non applicable" type="sc">{{ $option }}</li>
															@endforeach
														</ul>
														<label for="easySelectable">French Options</label>
														<ul id="easySelectable" class="easySelectable">
															@foreach($options_fr as $option)
															<li class="es-selectable " name="" value="Non applicable" type="sc">{{ $option }}</li>
															@endforeach
														</ul>
													</section>
													@break
												@case('sc')
													<section class="options" id="">
														@php 
															$options    = explode(',', $question->options);
															$options_fr = explode(',', $question->options_fr);
														@endphp
														<label for="easySelectable">Englih Options</label>
														<ul id="easySelectable" class="easySelectable">
															@foreach($options as $option)
															<li class="es-selectable " name="" value="Non applicable" type="sc">{{ $option }}</li>
															@endforeach
														</ul>
														<label for="easySelectable">French Options</label>
														<ul id="easySelectable" class="easySelectable">
															@foreach($options_fr as $option)
															<li class="es-selectable " name="" value="Non applicable" type="sc">{{ $option }}</li>
															@endforeach
														</ul>
													</section>
													@break
												@case('dc')
													@php 
														$arr = ["", "DATA ELEMENTS OF ORGANIZATION", "ASSETS OF ORGANIZATION", "COUNTRIES", "DATA CLASSIFICATION", "ASSETS COMBINED IMPACT", "ASSETS TIER"];
													@endphp
													<label for="">Dropdown Values From</label>
													<select class="form-control">
														<option>{{$arr[$question->dropdown_value_from]}}</option>
													</select>
													@break
												@default
											@endswitch
										</div>
									</div>
								</div>
							@endforeach
						</div>
					</div>
				</div>
			@endforeach
		</div>
	</div>

    <!-- Modals -->
    {{-- New Question Model Start --}}
        <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog"  aria-labelledby="addQuestionModel" id="addQuestionModel" tabindex="-1"  aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="">Add New Question</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form class="form-horizontal" id="add_question_form" method="POST" action="{{ route('add_question_to_group') }}" onsubmit="add_new_question(event)">
                        <div class="modal-body">

                            <input type="hidden" name="group_id" id="ggroup_id">

                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Select Question Type</h5>
                            </div>

                            <div class="row pb-2">
                                <a onclick="get_html('mc')" id="mc" class="btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Multiple Choice Question</a>
                                <a onclick="get_html('sc')" id="sc" class="btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Single Choice Question</a>
                                <a onclick="get_html('qa')" id="qa" class="btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Text Question</a>
                                <!-- <a onclick="get_html('im')" id="im" class="btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Attachment Upload Option</a>
                                <a onclick="get_html('sp')" id="sp" class="btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Design Multi Level Question</a>
                                <a onclick="get_html('da')" id="da" class="btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Data Inventory Questions</a> -->
                                <a onclick="get_html('dc')" id="dc" class="btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Dynamic Control Question</a>
                            </div>

                            <div class="form-check py-2">
                                <input class="form-check-input d_cehckbox" onclick="disable_fr_options(this)" id="qmodel-same" type="checkbox" value="" id="flexCheckChecked">
                                <label class="form-check-label" for="flexCheckChecked">English Only Form</label>
                                <!-- <div class="not_same_for_fr" style="color: red;">You have to explicitly write all french data </div> -->
                                <div class="same_for_fr" style="color: green; display: none;">
                                    All french data will be the same as english
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="question_title" class="col-form-label">Add English Question <strong style="color: red">*</strong></label>
                                <textarea rows="4" name="question_title" class="form-control" onkeyup="$('#qmodel_main_q').val($(this).val())"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="question_title_fr" class="col-form-label">Add French Question<strong style="color: red">*</strong></label>
                                <textarea rows="4" name="question_title_fr" class="form-control fr_field" id="qmodel_main_q"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="question_title" class="col-form-label">Add Question English Short Title  <strong style="color: red">*</strong></label>
                                <input type="text" name="question_title_short" class="form-control" onkeyup="$('#q_simple_model_main_fr').val($(this).val())">
                            </div>

                            <div class="form-group">
                                <label for="question_title_fr" class="col-form-label">Add Question French Short Title<strong style="color: red">*</strong></label>
                                <input type="text" name="question_title_short_fr" class="form-control fr_field" id="q_simple_model_main_fr">
                            </div>

                            <div class="form-group">
                                <label for="control_id" class="col-form-label">Control ID <strong style="color: red">*</strong></label>
                                <input type="text" name="control_id" class="form-control" id="control_id">
                            </div>
                            
                            <div class="form-group" id="qmodel-type" style="display: none"></div>
                            <div class="form-group" id="qmodel-custom_div" style="display: none"></div>
                            <div id="render_question_data"></div>
                        </div>

                        <div class="modal-footer">
                            <a type="button" class="btn btn-outline-danger" data-dismiss="modal">Cancel</a>
                            <button type="submit" class="btn btn-primary"> Save Question </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    {{-- New Question Model End --}}

    {{-- Add Section  model start --}}
        <div class="modal fade" id="addSectionModel" tabindex="-1" role="dialog"
            aria-labelledby="addSubQuestionModel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add New Section</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="section_title" class="col-form-label">Section title <small>(English)</small><strong style="color: red">*</strong></label>
                            <input type="text" name="section_title" class="form-control" id="section_title" onkeyup="$('#section_title_fr').val($(this).val())">
                        </div>
                        <div class="form-group">
                            <label for="section_title_fr" class="col-form-label">Section title <small>(French)</small> <strong style="color: red">*</strong></label>
                            <input type="text" name="section_title_fr" class="form-control fr_field" id="section_title_fr">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a type="" class="btn btn-outline-danger" data-dismiss="modal">
                            <i class="fa fa-ban"></i>
                            Cancel
                        </a>
                        <button type="button" onclick="save_section(event)" class="btn btn-primary">Save Section</button>
                    </div>
                    
                </div>
            </div>
        </div>
    {{-- Add Section model end --}}

@endsection
@push('scripts')
	<script src="https://browser.sentry-cdn.com/7.34.0/bundle.min.js"></script>
	<!-- <script type="text/javascript" src="{{ url('public/custom_form/js/jquery.min.js') }}"></script> -->
	<script type="text/javascript" src="{{ url('public/custom_form/js/popper.min.js') }}"></script>
	<script src="{{ url('public/custom_form/js/easySelectable.js') }}"></script>
	<script type="text/javascript" src="{{ url('public/custom_form/js/cust_js.js') }}"></script>
	<script src="{{ url('public/bar-filler/js/jquery.barfiller.js') }}" type="text/javascript"></script>
	<script src="{{ url('backend/js/jquery.datetimepicker.js') }}"></script>
	<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
	<script>
		$('.attachment_file').dropify();
	</script>
@endpush
