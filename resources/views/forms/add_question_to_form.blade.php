@extends (('admin.layouts.admin_app'))
@section('content')
    <link rel="stylesheet" type="text/css" href="{{ url('public/custom_form/css/style.css') }}">
    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        #size {
            margin-left: 58px;
            width: 168px;
            padding: 3px;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #222d32;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked+.slider {
            background-color: #1cc88a !important;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked+.slider {
            background-color: #2196F3;
        }

        input:focus+.slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked+.slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }

        .max-spec {
            font-size: 14px;
        }

        .card-body-form {
            width: 100%;
        }

        .form-group {
            display: block;
            flex-direction: column;
        }

        .form-btn a,
        .form-btn button {
            font-size: 18px;

        }

        .add_color {
            background: #0f75bd;
            font-size: 19px;
            color: #fff;
        }

        .section-heading-edit {
            display: none;
        }

        .change-heading-btn {
            display: none;
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

        #easySelectable li.es-selected,
        #easySelectable li:hover {
            background: #73b84d !important;
            color: #fff;
        }

        textarea {
            border-radius: 6px;
        }

        .change-heading-btn {
            background-color: #73b84d;
            color: #fff;
        }

        li.es-selectable {
            font-size: 12px;
        }

        h6.question-comment {
            font-size: 16px;
        }

        .form-note {
            color: #fff;
            background: #c52c14;

        }

        .main-panel {
            padding-bottom: 25px;
        }

        .container-fluid h3 {
            float: left;
        }

        .container-fluid a {
            display: flex;
            justify-content: flex-end;
        }
    </style>

    <body style="background-color: #E5E5E5;">
        @php
            // $att = attchment formats
            $att = ['Images', '.docs', '.pdf', '.xlxs , .csv', '.zip'];
        @endphp
        
        <input type="hidden" id="form_id_to" value="{{ $form_id }}">
        @if ($user_type == 'admin')
            <div class="app-title">
                <ul class="app-breadcrumb breadcrumb">
                    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i>
                    </li>
                    <li class="breadcrumb-item"><a href="{{ route('admin_forms_list') }}">Manage Assessment Forms</a>
                    </li>
                    <li class="breadcrumb-item"><a href="{{ url('Forms/ViewForm/' . $form_id) }}">View Forms</a>
                    </li>
                </ul>
            </div>
        @endif
        <div class="container-fluid">
        @section('page_title')
            {{ __('ASSESMENT FORM') }}
        @endsection
        @php
            $btn_url = '/Forms/FormsList';
            if (Auth::user()->role == 1) {
                $btn_url = '/Forms/AdminFormsList';
            }
            if (Auth::user()->role == 3 && Auth::user()->user_type != 1) {
                $btn_url = '/Forms/ClientUserFormsList';
            }
        @endphp
        @if (Session::has('message'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>{{ Session::get('message') }}</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if (count($errors) > 0)
            @foreach ($errors->all() as $error)
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>{{ $error }}</strong>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endforeach
        @endif
        @if (!$can_update && $form_id > 14)
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <span>You cannot edit this form because it is </span><strong>assigned to users.</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if (!$can_update && $form_id < 15)
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <span>You cannot <strong>Edit/Update</strong> prevously created forms</span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <span></span>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="row" style="    margin-left: 3px;">
            <a href="{{ url($btn_url) }}"><button class="btn btn-primary pull-right">{{ __('Back') }}</button></a>
            @if ($form_id > 14)
                <a><button class="btn btn-primary pull-right ml-2" data-toggle="modal"
                        data-target="#addSectionModel">{{ __('Add Section') }}</button></a>
            @endif
        </div>
        <!---------------main-panel start----------->
        <div class="main-panel">
            @if (!empty($questions))
                @if (isset($questions[0]))
                    <form style="display:none">
                        <input type="hidden" name="form-id" id="form-id" value="{{ $form_id }}">
                    </form>
                @endif
            @endif
            <div id="heading">
                <div class="head" id="form-heading">
                    <ul>
                        <li><strong>&starf;</strong></li>
                        <li><i class="fa fa-chevron-up"></i></li>
                    </ul>
                    @if (isset($questions[0]) && !empty($questions[0]))
                        <h3>
                            @if (session('locale') == 'fr')
                                {{ $questions[0]->title_fr ? $questions[0]->title_fr : $questions[0]->title }}
                            @else
                                {{ $questions[0]->title }}
                            @endif
                        </h3>
                    @endif
                </div>
            </div>
            <div class="collapseZero" id="form-body ">
                @if (isset($questions[0]) && !empty($questions[0]->comments))
                    <div class="form-note">
                        <h5>
                            {{-- $questions[->comments --}}
                        </h5>
                    </div>
                @endif
                <?php
						$heading_recs = [];
						$section      = 0;
						$display_body_sec_div = 0;
						$close_body_sec_div   = 0;	

						foreach ($questions as $key => $question):
							$sec_id          = $question->afs_sec_id;
							$heading         = $question->admin_sec_title;
							$sec_title_fr 	 = $question->section_title_fr;
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
            <div class="head sec-heading" id="{{ 'section-title-' . $sec_id }}" num="{{ $section }}">
                <ul>
                    <li><span>{{ __('Section') }} {{ $section }}</span></li>
                    <li><i class="fa fa-chevron-up"></i></li>
                </ul>
                <div class="section-heading" id="{{ 'section-heading-' . $sec_id }}" num="{{ $section }}">
                    <h3>{{ $heading }}</h3>
                </div>
                <div id="{{ 'section-heading-edit-' . $sec_id }}" class="section-heading-edit container"
                    style="width:20%">
                    <div class="">
                        <div class="row">
                            <div class="form-control">
                                <label>En: </label>
                                <input id="{{ 'new-section-heading-' . $sec_id }}" type="text"
                                    class="form form-control" value="{{ $heading }}"
                                    old-value="{{ $heading }}" />
                                <label>Fr: </label>
                                <input id="{{ 'new-section-heading_fr-' . $sec_id }}" type="text"
                                    class="form form-control" value="{{ $sec_title_fr ? $sec_title_fr : $heading }}"
                                    old-value="{{ $sec_title_fr ? $sec_title_fr : $heading }}" />
                            </div>
                        </div>
                    </div>
                </div>

                @if (Auth::user()->role == 1)
                    @if ($can_update || true)
                        <div class="fork">
                            <a href="#" class="change-heading-icon" id={{ 'heading-edit-' . $sec_id }}
                                sec-id="{{ $sec_id }}"><i class="fa fa-pencil"></i></a>
                            <button class="btn btn-default change-heading-btn"
                                id="save-sec-heading-{{ $sec_id }}" sec-id="{{ $sec_id }}">Save</button>
                        </div>
                    @endif
                @endif

                @if ($form_id > 14 || true)
                    @if ($can_update || true)
                        <button class="btn btn-success btn-sm pull-right" style="margin-right: 9px;"
                            onclick="$('.this_section_id').val('{{ $question->question_section_id }}');$('#this_section_title').text('{{ $questions[0]->title }}')"
                            data-toggle="modal" data-target="#addQuestionModel">
                            <i class="fa fa-plus-circle mr-1" aria-hidden="true"></i>
                            {{ __('Add New Question In This Section') }}
                        </button>
                    @endif
                @endif
            </div>
            <!-- clear upper -->

            <?php  endif;  ?>

            <?php if ($display_body_sec_div): 
					$display_body_sec_div 	= 0;
					$close_body_sec_div 	= 1;
				?>
            <div class="margin" id="section-{{ $section }}-body" num="{{ $section }}"
                class="sec-heading-detail" style="margin: 20px 30px; display: block; ">
                <?php endif; ?>
                <!-- Sorting and Question Area -->
                <div class="content">
                    {{-- BARI START --}}
                    @if ($form_id > 14)
                        @if ($can_update)
                            @if ($question->is_parent == 1)
                                <p data-toggle="tooltip" data-placement="left"
                                    title="Delete Parent Question, All of his child questions will also be deleted!"
                                    id="delete-parent-{{ $question->question_id }}" data-title=""
                                    onclick="delete_question(this.id , 'All of his child questions will also be deleted!')"
                                    class="pull-right btn btn-sm btn-danger ">
                                    <i class="fa fa-trash-o"
                                        style="font-size: 23px;margin-right: 1px;vertical-align: initial;"
                                        aria-hidden="true"></i>
                                </p>
                            @elseif($question->parent_q_id != null && $question->is_parent == 0)
                                <p data-toggle="tooltip" data-placement="left" title="Delete Child Question"
                                    id="delete-child-{{ $question->question_id }}"
                                    data-title="This operation cannot be undone"
                                    onclick="delete_question(this.id , 'This operation cannot be undone')"
                                    class="pull-right btn btn-sm"
                                    style="color: #FFF !important;    background-color: #17a2b8 !important; border-color: #0f75bd !important;">
                                    <i class="fa fa-trash-o"
                                        style="font-size: 23px;margin-right: 1px;vertical-align: initial;"
                                        aria-hidden="true"></i>
                                </p>
                            @elseif($question->parent_q_id == null && $question->is_parent == 0)
                                <p data-toggle="tooltip" data-placement="left" title="Delete Question"
                                    id="delete-normal-{{ $question->question_id }}"
                                    data-title="This operation cannot be undone"
                                    onclick="delete_question(this.id , 'This operation cannot be undone')"
                                    class="pull-right btn btn-sm btn-danger "
                                    style="color: #FFF !important;background-color: #6c757d !important;border-color: #6c757d !important;">
                                    <i class="fa fa-trash-o"
                                        style="font-size: 23px;margin-right: 1px;vertical-align: initial;"
                                        aria-hidden="true"></i>
                                </p>
                            @endif
                        @endif
                    @endif

                    Sort Order: <input type="text" id="sort_order_{{ $question->fq_id }}"
                        style="width:50px;height:20px;1px solid #000;color:#000"
                        value="{{ $question->sort_order }}" /><a href="javascript:;" class="updatesorting"
                        id="{{ $question->fq_id }}">Update Sorting</a>
                    <h6 @if ($form_id > 14) @if ($can_update)  
								data-toggle="tooltip" data-placement="left" title="Question Title English" @endif
                        @endif >
                        <strong class="mr-3">
                            En:
                        </strong>
                        <!-- working area -->
                        <span
                            class="
								@if ($form_id > 14 || true) @if ($can_update || true) 
										class_for_edit_question_js_call @endif 
								@endif"
                            id="{{ $question->question_id }}">
                            {{ $question->question }}
                        </span>
                    </h6>
                    <h6 @if ($form_id > 14 || true) @if ($can_update || true)  
								data-toggle="tooltip" data-placement="left" title="Question Title French" @endif
                        @endif >
                        <strong class="mr-3">Fr: </strong>
                        {{ $question->question_fr }}
                        <span
                            class="ml-1 
							@if ($form_id > 14 || true) @if ($can_update || true) 
									class_for_edit_question_js_call_fr @endif 
							@endif"
                            id="{{ $question->question_id }}-fr"></span>
                    </h6>
                    <hr>
                    {{-- BARI END --}}
                </div>
                <!--  -->

                <!-- Main Answer  Area -->
                <div id="wrap" class="wrap-content">
                    @if ($question->type != 'cc')
                        @if ($question->question_comment != null && $question->question_comment != '')
                            <h6 class="question-comment "
                                @if ($form_id > 14 || true) @if ($can_update || true)	 @if ($question->type != 'cc')  data-toggle="tooltip" data-placement="left" title="Click To Edit English Question Comment" @endif
                                @endif
                        @endif><small class="mr-3">En: </small>
                        <span
                            class=" @if ($form_id > 14 || true) @if ($can_update || true) @if ($question->type != 'cc') edit_comment @endif @endif @endif"
                            id="{{ $question->question_id }}">
                            {!! $question->question_comment !!}
                        </span>
                        </h6>
                    @else
                        <h6 class="question-comment"
                            @if ($form_id > 14 || true) @if ($can_update) @if ($question->type != 'cc') data-toggle="tooltip" data-placement="left" title="Click To Edit English Question Comment" @endif
                            @endif
                    @endif ><small class="mr-3">En: </small><span
                        class="@if ($form_id > 14 || true) @if ($can_update || true)  @if ($question->type != 'cc') edit_comment @endif @endif @endif"
                        id="{{ $question->question_id }}">#</span></h6>
                    @endif
                    @if ($question->question_comment_fr != null && $question->question_comment_fr != '')
                        <h6 class="question-comment "
                            @if ($form_id > 14 || true) @if ($can_update || true) @if ($question->type != 'cc') data-toggle="tooltip" data-placement="left" title="Click To Edit French Question Comment" @endif
                            @endif
                    @endif><small class="mr-3">Fr: </small><span
                        class="@if ($form_id > 14 || true) @if ($can_update || true) @if ($question->type != 'cc') edit_comment_fr @endif @endif @endif"
                        id="{{ $question->question_id }}-fr">{!! $question->question_comment_fr !!}</span></h6>
                @else
                    <h6 class="question-comment "
                        @if ($form_id > 14 || true) @if ($can_update) @if ($question->type != 'cc')  data-toggle="tooltip" data-placement="left" title="Click To Edit French Question Comment" @endif
                        @endif
                        @endif><small class="mr-3">Fr: </small><span
                            class="@if ($form_id > 14 || true) @if ($can_update || true) @if ($question->type != 'cc') edit_comment_fr @endif @endif @endif"
                            id="{{ $question->question_id }}-fr">#</span></h6>
                    @endif
                    @endif
                    <?php
						$type = $question->type;	
						switch ($type): 
							case('cc'):
							    echo isset($question->custom_fields)?$question->custom_fields:'';
							    break;
							case('sc'):
							case('mc'):
								$options = explode(', ', $question->options); 
								$options_fr = explode(', ', $question->options_fr); 
								?>
                    @if (!empty($options))
                        @if ($form_id > 14 || true)
                            @if ($can_update || true)
                                <p data-toggle="tooltip" data-placement="top"
                                    title="Click here to edit english question options"
                                    class="pull-right btn btn-sm btn-warning"
                                    onclick="$('#textarea-{{ $question->question_id }}').show(300) , $('#select-{{ $question->question_id }}').hide(600) ">
                                    Edit English Options</p>
                                <div id="textarea-{{ $question->question_id }}" style="display: none">
                                    <form action="{{ url('update_options') }}" method="POST">
                                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                        <textarea rows="4" cols="50"
                                            @if ($type == 'mc') onkeyup="check_is_type_sc_for_datepicker(this.value,this)" @endif
                                            @if ($type == 'sc') onkeyup="check_is_date_picker_is_single(this.value,this)" @endif
                                            name="updated_options" class="form-control">{{ str_replace(', ', ',', $question->options) }}</textarea>
                                        {{-- <a type="submit" class="pull-right btn btn-sm btn-success" onclick="$('#textarea-{{  $question->question_id  }}').hide(600) , $('#select-{{  $question->question_id }}').show(300)">Update</a> --}}
                                        <input type="hidden" name="question_id"
                                            value="{{ $question->question_id }}">
                                        <div class="row" style="margin-left: inherit; margin-top: 5px;">
                                            <button type="submit" class="btn save_btn"
                                                style="color: white;background: #0f75bd;"><i
                                                    class="fa fa-check-circle" style="font-size: 20px;"
                                                    aria-hidden="true"></i></button>
                                            <a class="btn btn-danger"
                                                onclick="$('#textarea-{{ $question->question_id }}').hide(600) , $('#select-{{ $question->question_id }}').show(300)"><i
                                                    class="fa fa-times" style="font-size: 20px;"
                                                    aria-hidden="true"></i></a>
                                        </div>
                                    </form>
                                    <br>
                                </div>
                            @endif
                        @endif
                        <section class="options" id="select-{{ $question->question_id }}">
                            <ul id="easySelectable" class="easySelectable">
                                <!-- Bugs Here -->
                                <?php
												$pia_form_check = false;
												foreach ($options as $option):
													$selected_class = '';
													if($question->form_id==10 || $question->form_id==8 || $question->form_id==14)
														$option = str_replace('.',',',$option);
														
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
                                @if ($pia_form_check == 'true')
                                    <li class="es-selectable {{ $selected_class }}" name=""
                                        value="{{ $option }}" type="{{ $type }}">{{ $option }}
                                    </li>
                                @elseif($pia_form_check == 'false' || isset($pia_form_check) == 'false')
                                    <li class="es-selectable {{ $selected_class }}" name=""
                                        value="{{ $option }}" type="{{ $type }}">{{ $option }}
                                    </li>
                                @endif

                                <?php endforeach; ?>
                            </ul>
                        </section>
                        {{-- ********************************* --}}
                        @if ($form_id > 14 || true)
                            @if ($can_update || true)
                                <p data-toggle="tooltip" data-placement="top"
                                    title="Click here to edit french question options"
                                    class="pull-right btn btn-sm btn-warning"
                                    onclick="$('#frtextarea-{{ $question->question_id }}').show(300) , $('#frselect-{{ $question->question_id }}').hide(600) ">
                                    Edit French Options</p>
                                <div id="frtextarea-{{ $question->question_id }}" style="display: none">
                                    <form action="{{ url('update_options_fr') }}" method="POST">
                                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                        <textarea rows="4" cols="50"
                                            @if ($type == 'mc') onkeyup="check_is_type_sc_for_datepicker(this.value,this)" @endif
                                            @if ($type == 'sc') onkeyup="check_is_date_picker_is_single(this.value,this)" @endif
                                            name="updated_options_fr" class="form-control">{{ str_replace(', ', ',', $question->options_fr) }}</textarea>
                                        {{-- <a type="submit" class="pull-right btn btn-sm btn-success" onclick="$('#textarea-{{  $question->question_id  }}').hide(600) , $('#select-{{  $question->question_id }}').show(300)">Update</a> --}}
                                        <input type="hidden" name="question_id"
                                            value="{{ $question->question_id }}">
                                        <div class="row" style="margin-left: inherit; margin-top: 5px;">
                                            <button type="submit" class="btn save_btn"
                                                style="color: white;background: #0f75bd;"><i
                                                    class="fa fa-check-circle" style="font-size: 20px;"
                                                    aria-hidden="true"></i>
                                            </button>
                                            <a class="btn btn-danger"
                                                onclick="$('#frtextarea-{{ $question->question_id }}').hide(600) , $('#frselect-{{ $question->question_id }}').show(300)"><i
                                                    class="fa fa-times" style="font-size: 20px;"
                                                    aria-hidden="true"></i></a>
                                        </div>
                                    </form>
                                    <br>
                                </div>
                            @endif
                        @endif
                        <section class="options" id="frselect-{{ $question->question_id }}">
                            <ul id="easySelectable" class="easySelectable">
                                <?php 
												$pia_form_check = false;
													
												foreach ($options_fr as $option):
													$selected_class = '';
													$option = str_replace('.',',',$option);
													if (isset($filled[$question->form_key])){
														if ($type == 'sc' && trim($filled[$question->form_key]['question_response']) == trim($option)){
															$selected_class = 'es-selected';
														}
														if ($type == 'mc' && in_array(trim($option), $filled[$question->form_key]['question_response'])){
															$selected_class = 'es-selected';			
														}
													}
											?>
                                @if ($pia_form_check == 'true')
                                    <li class="es-selectable {{ $selected_class }}" name=""
                                        value="{{ $option->name }}" type="{{ $type }}">{{ $option->name }}
                                    </li>
                                @elseif($pia_form_check == 'false' || isset($pia_form_check) == 'false')
                                    <li class="es-selectable {{ $selected_class }}" name=""
                                        value="{{ $option }}" type="{{ $type }}">{{ $option }}
                                    </li>
                                @endif

                                <?php endforeach; ?>
                            </ul>
                        </section>
                    @endif
                    {{-- ********************************** --}}
                    <?php
								break;
							case ('bl'):
							case ('qa'):
								?>
                    <div>
                        <form>
                            <label></label>
                            <textarea name="" rows="4" cols="50" disabled></textarea>
                            @if ($question->not_sure_option == 'true')
                                <ul id="easySelectable" class="easySelectable">
                                    <li class="es-selectable" name="" value="not sure" type="mc">Not
                                        Sure</li>
                                </ul>
                            @endif
                        </form>
                    </div>
                    <?php	
								break;
								
							case ('qs'):
								?>
                    <div>
                        <form>
                            <label></label>
                            <textarea name="" rows="4" cols="50" disabled></textarea>
                        </form>
                    </div>
                    <?php	
								break;
							case ('im'):
                                $attachment = DB::table('questions')->where('id',$question->question_id)->first();
								$attachments =json_decode($attachment->attachments);
								?>
                    <div>
                        <h6>Attachments</h6>
                        @if ($attachments != '')
                            @for ($i = 1; $i <= 5; $i++)
                                <input type="checkbox" name="attachment[]" id="img"
                                    {{ in_array($i, $attachments) ? 'checked=true' : '' }} />{{ $att[$i - 1] }}
                                &nbsp;&nbsp;
                            @endfor
                        @endif

                    </div>
                    <br><br>
                    <?php	
								break;	
							case ('dc'):
								$drop_down_from = ['', 'FROM DATA ELEMENTS', 'FROM ORG ASSET', 'FROM COUNTRIES', 'TYPE OF DATA CLASSIFICATION', 'ASSETS COMBINED IMPACT', 'ASSETS TIER']
								?>
                    @if (is_numeric($question->dropdown_value_from))
                        <select name="" class="form-control">
                            <option value="">{{ $drop_down_from[$question->dropdown_value_from] }}</option>
                        </select>
                    @endif
                    @if ($question->not_sure_option == 'true')
                        <ul id="easySelectable" class="easySelectable">
                            <li class="es-selectable" name="" value="not sure" type="mc">Not Sure</li>
                        </ul>
                    @endif

                    <?php
								break;	
						endswitch; 
					?>
                    <br><br>
                </div>
                <!--  -->
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Models Section -->

    {{-- Add Sectiion  model start --}}
    <div class="modal fade" id="addSectionModel" tabindex="-1" role="dialog"
        aria-labelledby="addSubQuestionModel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Form Section</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form-horizontal" method="POST" action="{{ url('form/add/section/to/form') }}"
                    enctype="multipart/form-data" autocomplete="off">
                    <div class="modal-body">
                        {{ csrf_field() }}
                        <input type="hidden" class="this_section_id" name="this_section_id" id="this_section_id" value="0">
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Section title English<strong style="color: red">*</strong></label>
                            <input type="text" name="section_title" class="form-control" id="section_title" onkeyup="$('#section_title_fr').val($(this).val())">
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Section title French <strong style="color: red">*</strong></label>
                            <input type="text" name="section_title_fr" class="form-control fr_field" id="section_title_fr">
                        </div>
                        <div class="form-check">
                            <input class="form-check-input d_cehckbox" id="smodel-same" type="checkbox" onclick="check_is_check(this)" value="" id="flexCheckChecked">
                            <label class="form-check-label danger" for="flexCheckChecked"> Same For English</label>
                            <div class="not_same_for_fr" style="color: red;">You have to explicitly write all french data </div>
                            <div class="same_for_fr" style="color: green; display:none;">All french data will be saved same as english</div>
                        </div>
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Select Question Type</h5>
                        </div>
                        <div class="row">
                            <a onclick="render_questions(this.id,'smodel-type')" id="mc" class="btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Add Multiple Choice Question</a>
                            <a onclick="render_questions(this.id,'smodel-type')" id="sc" class="btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Add Single Select Question</a>
                            <a onclick="render_questions(this.id,'smodel-type')" id="qa" class="btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Add Text Question</a>
                            <a onclick="render_questions(this.id,'smodel-type')" id="im" class="btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Attachment Upload Option</a>
                        </div>
                        <div class="form-group" id="question_div" style="display:none">
                            <label for="question_title" class="col-form-label">AddQuestion Title English <strong style="color: red">*</strong></label>
                            <input type="text" name="question_title" class="form-control" onkeyup="$('#sec_question_title_fr').val($(this).val())">
                            <label for="question_title_fr" class="col-form-label">Add Question Title French <strong style="color: red">*</strong></label>
                            <input type="text" name="question_title_fr" class="form-control fr_field" id="sec_question_title_fr">
                        </div>

                        <div class="form-group">
                            <label for="question_title" class="col-form-label">Add Question Short Title English <strong style="color: red">*</strong></label>
                            <input type="text" name="question_title_short" class="form-control" onkeyup="$('#qmodel_main_q').val($(this).val())">
                            <label for="question_title_fr" class="col-form-label">Add Question Short Title French <strong style="color: red">*</strong></label>
                            <input type="text" name="question_title_short_fr" class="form-control fr_field" id="qmodel_main_q">
                        </div>

                        <div class="form-group" id="smodel-type" style="display: none"></div>
                        <div class="form-group" id="smodel-custom_div" style="display: none"></div>
                        <div id="render_question_data"></div>
                        <input type="hidden" value="{{ $form_id }}" name="form_id">
                    </div>
                    <div class="modal-footer">
                        <a type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Section</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Add Sectiion model end --}}



    {{-- New Question Model Start --}}

    <div class="modal fade" id="addQuestionModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Question in <strong
                            id="this_section_title"></strong> Section</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form-horizontal" method="POST" action="{{ url('form/add/question/to/form') }}"
                    enctype="multipart/form-data" autocomplete="off">
                    <div class="modal-body">
                        {{ csrf_field() }}
                        <input type="hidden" class="this_section_id" name="this_section_id" id="this_section_id"
                            value="0">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Select Question Type</h5>
                        </div>
                        <div class="row">
                            <a onclick="render_questions(this.id,'qmodel-type')" id="mc" class="btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Add Multiple Choice Question</a>
                            <a onclick="render_questions(this.id,'qmodel-type')" id="sc" class="btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Add Single Select Question</a>
                            <a onclick="render_questions(this.id,'qmodel-type')" id="qa" class="btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Add Text Question</a>
                            <a onclick="render_questions(this.id,'qmodel-type')" id="im" class="btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Attachment Upload Option</a>
                            <!-- <a onclick="render_questions(this.id,'special_question-type')" data-toggle="modal" data-target="#specialQuestionModel" id="cc" class="btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Design custom question with multiple fields</a> -->
                            <a onclick="render_questions(this.id,'special_question-type')" data-toggle="modal" data-target="#specialQuestionModel" id="parent" class="btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Design Multi Level Question</a>
                            <a onclick="render_questions(this.id,'special_question-type')" data-toggle="modal" data-target="#specialQuestionModel" id="data" class="btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Data Inventory Questions</a>
                            <a onclick="render_questions(this.id,'qmodel-type')" id="dc" class="btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Dynamic Controlled Items</a>
                        </div>

                        <div class="form-group">
                            <label for="question_title" class="col-form-label">Add Question Title English <strong style="color: red">*</strong></label>
                            <input type="text" name="question_title" class="form-control" onkeyup="$('#qmodel_main_q').val($(this).val())">
                            <label for="question_title_fr" class="col-form-label">Add Question Title French <strong style="color: red">*</strong></label>
                            <input type="text" name="question_title_fr" class="form-control fr_field" id="qmodel_main_q">
                        </div>

                        <div class="form-group">
                            <label for="question_title" class="col-form-label">Add Question Short Title English <strong style="color: red">*</strong></label>
                            <input type="text" name="question_title_short" class="form-control" onkeyup="$('#q_simple_model_main_fr').val($(this).val())">
                            <label for="question_title_fr" class="col-form-label">Add Question Short Title French <strong style="color: red">*</strong></label>
                            <input type="text" name="question_title_short_fr" class="form-control fr_field" id="q_simple_model_main_fr">
                        </div>

                        <div class="form-check">
                            <input class="form-check-input d_cehckbox" onclick="check_is_check(this)" id="qmodel-same" type="checkbox" value="" id="flexCheckChecked">
                            <label class="form-check-label" for="flexCheckChecked"> Only For English </label>
                            <div class="not_same_for_fr" style="color: red;">You have to explicitly write all french data </div>
                            <div class="same_for_fr" style="color: green; display: none;">
                                All french data will be saved same as english
                            </div>
                        </div>

                        <div class="form-group" id="qmodel-type" style="display:
								none">
                        </div>
                        <div class="form-group" id="qmodel-custom_div" style="display: none">
                        </div>
                        <div id="render_question_data">
                        </div>

                        <input type="hidden" value="{{ $form_id }}" name="form_id">
                    </div>
                    <div class="modal-footer">
                        <a type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</a>
                        <button type="submit" class="btn btn-primary"> Save Question </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- New Question Model End --}}



    {{-- special question model Start --}}

    <div class="modal fade" id="specialQuestionModel" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form class="form-horizontal" method="POST" action="{{ url('form/add/special_question/to/form') }}"
                enctype="multipart/form-data" autocomplete="off">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <input type="hidden" class="this_section_id" name="this_section_id" id="this_section_id"
                        value="0">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle"> Create Custom Question </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="question_title" class="col-form-label">Add Main Question Title English<strong style="color:red;">*</strong></label>
                                <input type="text" name="question_title" class="form-control" onkeyup="$('#data_inv_main').val($(this).val())">
                                <label for="question_title_fr" class="col-form-label fr_field">Add Main Question Title French <strong style="color:red">*</strong></label>
                                <input type="text" name="question_title_fr" class="form-control fr_field" id="data_inv_main">
                            </div>
                            <div class="form-group">
                                <label for="question_short_title" class="col-form-label">Add Question Short Title English <strong style="color: red">*</strong></label>
                                <input type="text" name="question_title_short" class="form-control" onkeyup="$('#qmodel__short_fr').val($(this).val())">
                                <label for="question_short_title_fr" class="col-form-label">Add Question Short Title French <strong style="color: red">*</strong></label>
                                <input type="text" name="question_title_short_fr" class="form-control fr_field" id="qmodel__short_fr">
                            </div>
                            <div class="form-check">
                                <input class="form-check-input d_cehckbox" onclick="check_is_check(this)"  id="special_question-same" type="checkbox" value="" id="flexCheckChecked">
                                <label class="form-check-label" for="flexCheckChecked"> Only For English</label>
                                <div class="not_same_for_fr" style="color: red;">You have to explicitly write all french data </div>
                                <div class="same_for_fr" style="color:green; display: none;">All french data will be saved same as english</div>
                            </div>
                            <hr>
                            <div class="form-group" id="special_question-type"style="display:none;">
                            </div>
                            <div class="form-group" id="special_question-custom_div" style="display: none"></div>
                            <div id="render_question_data"></div>
                            <input type="hidden" value="{{ $form_id }}"name="form_id">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- special question model End --}}


    <script type="text/javascript" src="{{ url('public/custom_form/js/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ url('public/custom_form/js/popper.min.js') }}"></script>
    <script src="{{ url('public/custom_form/js/easySelectable.js') }}"></script>
    <script type="text/javascript" src="{{ url('public/custom_form/js/cust_js.js') }}"></script>

    <script type="text/javascript">
        function check_is_check(c) {
            if (c.checked) {
                $('.fr_field').attr('readonly', true);
                $('.not_same_for_fr').hide();
                $('.same_for_fr').show();
            } else {
                $('.fr_field').attr('readonly', false);
                $('.not_same_for_fr').show();
                $('.same_for_fr').hide();
            }
        }

        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        })

        function addAnotherSubQuestion(question_id, form_id, section_id, data) {}

        function delete_question(id, title) {
            var data = id.split("-");
            var question_type = data[1];
            var question_id = data[2];
            var qtype = question_type;
            if (qtype == 'normal') {
                qtype = '';
            }
            swal({
                    title: 'Delete ' + qtype + ' question',
                    text: title,
                    type: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#F79426',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes',
                    showLoaderOnConfirm: true
                },
                function() {
                    // swal('Question Deleted Successfully','' , 'success');
                    var post_data = {};
                    post_data['_token'] = '{{ csrf_token() }}';
                    post_data['question_id'] = question_id;
                    post_data['question_type'] = question_type;
                    $.ajax({
                        url: '{{ url('delete_question') }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: post_data,
                        success: function(response) {

                            swal('Question Deleted Successfully', '', 'success');
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        }
                    });
                });
        }
    </script>

    <script>
        var count_multi_attachment = 0;

        function add_attachment_box(event, multi = 0) {
            $(event.target).val($(event.target).prop("checked"))
            if(event.target.checked){
                if(multi){
                    count_multi_attachment = count_multi_attachment +1;
                    $('.options').last().html('<label for="inputCity" class="form-label mt-3">'+'Select Accepted Formates:'+'</label>'+
                        '<div class="form-label">'+
                            '<input  type="checkbox" name="attachment['+count_multi_attachment+'][]" id="" value="1">'+
                            '<label  for="inlineRadio1"> IMAGE &nbsp;&nbsp;</label>'+
                            '<input  type="checkbox" name="attachment['+count_multi_attachment+'][]" id="" value="2">'+
                            '<label  for="inlineRadio1"> WORD DOCs&nbsp;&nbsp;</label>'+
                            '<input  type="checkbox" name="attachment['+count_multi_attachment+'][]" id="" value="3">'+
                            '<label  for="inlineRadio1"> PDF &nbsp;&nbsp;</label>'+
                            '<input  type="checkbox" name="attachment['+count_multi_attachment+'][]" id="" value="4">'+
                            '<label  for="inlineRadio1"> EXCEL &nbsp;&nbsp;</label>'+
                            '<input  type="checkbox" name="attachment['+count_multi_attachment+'][]" id="" value="5">'+
                            '<label  for="inlineRadio1"> ZIP</label>'+
                        '</div>'
                    );
                }else{

                    $('.options').last().html('<label for="inputCity" class="form-label mt-3">'+'Select Accepted Formates:'+'</label>'+
                        '<div class="form-label">'+
                            '<input  type="checkbox" name="attachment[]" id="" value="1">'+
                            '<label  for="inlineRadio1"> IMAGE &nbsp;&nbsp;</label>'+
                            '<input  type="checkbox" name="attachment[]" id="" value="2">'+
                            '<label  for="inlineRadio1"> WORD DOCs&nbsp;&nbsp;</label>'+
                            '<input  type="checkbox" name="attachment[]" id="" value="3">'+
                            '<label  for="inlineRadio1"> PDF &nbsp;&nbsp;</label>'+
                            '<input  type="checkbox" name="attachment[]" id="" value="4">'+
                            '<label  for="inlineRadio1"> EXCEL &nbsp;&nbsp;</label>'+
                            '<input  type="checkbox" name="attachment[]" id="" value="5">'+
                            '<label  for="inlineRadio1"> ZIP</label>'+
                        '</div>'
                    );
                }
            }else{
                $('.options').html("");
            }
            
        }

        function count_comma_values(id) {
            var data = id.split("-");
            var q_id = data[1];
            var type = data[0].split("_");
            var type = type[1];
            // console.log(data);
            // console.log(q_id);	
            // console.log(type);
            // console.log($('#'+id).val());
            var count = $('#' + id).val().split(",");
            count = count.length;
            var this_count = count;
            switch (type) {
                case 'en':
                    var count_other = $('#option_fr-' + q_id).val().split(",");
                    count_other = count_other.length;
                    // console.log("other => "+count_other.length);
                    if (this_count != count_other) {
                        swal('There should be same number of (,) separated options in english and french version.', '',
                            'error');
                        $('#' + id).val('');
                    }
                    break;
                case 'fr':
                    var count_other = $('#option_en-' + q_id).val().split(",");
                    count_other = count_other.length;
                    if (this_count != count_other) {
                        swal('There should be same number of (,) separated options in english and french version.', '',
                            'error');
                        $('#' + id).val('');
                    }
                    break;

            }
        }

        let created_div_id = 0;
        var hide_items_array = [];

        function check_is_type_sc_for_datepicker(val, tarea) {
            if (val.includes("Date Picker Option")) {
                swal('Date Picker Option is only allowed in Single Select Option', 'error');
                var origial_value = tarea.value;
                tarea.value = '';
            }
        }

        function check_is_date_picker_is_single(val, tarea) {
            if ((val.split(new RegExp("Date Picker Option", "gi")).length - 1) > 1) {
                swal('Date Picker Option is only allowed once for each question', 'error');
                var origial_value = tarea.value;
                tarea.value = '';
            }
        }

        function get_html_for_multi_level(type, render_div) {
            var is_same = render_div.split("-");
            is_same = is_same[0];
            var property = 'readonly=""';
            var kup = '$(\'#sub-' + created_div_id + '\').val($(this).val())';

            if (document.getElementById(is_same + '-same').checked) {

                is_same = true;
                property = 'readonly="true"';
                kup = '$(\'#sub-' + created_div_id + '\').val($(this).val())';

            } else {

                is_same = false;
                property = '';
                kup = '';

            }
            $('#' + created_div_id + 'another_question').hide();
            $('.button_div').hide();
            var questions = '<label for="question_title" class="col-form-label">Add Sub Question Title English  <strong style="color:  red">*</strong></label>'+
                            '<input type="text" name="s_question_title[]" class="form-control" onkeyup="$(\'#main-' +created_div_id +'\').val($(this).val())" >'+
                            '<label for="question_title_fr" class="col-form-label">Add Sub Question Title French  <strong style="color:  red">*</strong></label>'+
                            '<input type="text" ' +property + ' id="main-' + created_div_id + '" name="s_question_title_fr[]" class="form-control fr_field" >'+
                            '<div class="form-group">'+
                                '<label for="question_title" class="col-form-label">Add Question Short Title English <strong style="color: red">*</strong></label>'+
                                '<input type="text" name="question_title_short" class="form-control" onkeyup="$("#qm_main_q").val($(this).val())">'+
                                '<label for="question_title_fr" class="col-form-label">Add Question Short Title French <strong style="color: red">*</strong></label>'+
                                '<input type="text" name="question_title_short_fr" class="form-control fr_field" id="qm_main_q">'+
                            '</div>';

            html = '<input  type="hidden" name="s_q_type[]" value="' + type + '">';
            switch (type) {
                case 'mc':
                    html += '<h5><br> Multiple Choice Question </h5><hr>';
                    html += questions;
                    html += '<div id="' + created_div_id + '"><label for="' + type +
                        '_question_options" class="col-form-label">Add (,) Separated English options  <strong style="color:  red">*</strong></label><textarea onkeyup="check_is_type_sc_for_datepicker(this.value,this) ,$(\'#sub-' +
                        created_div_id +
                        '\').val($(this).val()) " type="text"  class="form-control  " name="s_question_options[]" > </textarea><label for="' +
                        type +
                        '_question_options_fr" class="col-form-label ">Add (,) Separated French options  <strong style="color:  red">*</strong></label><textarea onkeyup="check_is_type_sc_for_datepicker(this.value,this)" ' +
                        property + ' type="text"  class="form-control fr_field"  id="sub-' + created_div_id +
                        '" name="s_question_options_fr[]" > </textarea></div>';
                    break;

                case 'sc':
                    html += '<h5><br> Single Select Question </h5><hr>';
                    html += questions;
                    html += '<div id="' + created_div_id + '"><label for="' + type +
                        '_question_options" class="col-form-label">Add (,) Separated English options  <strong style="color:  red">*</strong></label><textarea onkeyup="check_is_date_picker_is_single(this.value,this),$(\'#sub-' +
                        created_div_id +
                        '\').val($(this).val())" type="text"  class="form-control" name="s_question_options[]" > </textarea><label for="' +
                        type +
                        '_question_options_fr" class="col-form-label">Add (,) Separated French options  <strong style="color:  red">*</strong></label><textarea onkeyup="check_is_date_picker_is_single(this.value,this)" type="text" ' +
                        property + ' class="form-control fr_field" name="s_question_options_fr[]" id="sub-' +
                        created_div_id + '"  > </textarea></div>';
                    break;

                case 'qa':
                    html += '<h5><br> Text Question </h5><hr>';
                    html += questions;
                    html += '<div id="' + created_div_id +
                        '"><input type="hidden" value="0" class="form-control" name="s_question_options[]" ><input type="hidden"  class="form-control" value="0" name="s_question_options_fr[]" > </div>';
                    break;

                case 'im':
                    html += '<h5><br> Attachment Upload Question </h5><hr>';
                    html += questions;
                    html += '<div id="' + created_div_id +
                        '"><input type="hidden" value="0" class="form-control" name="s_question_options[]" ><input type="hidden"  class="form-control" value="0" name="s_question_options_fr[]" ><div class="col-md-6"><label for="inputCity" class="form-label">Select Attachment:</label><div class="form-label"><input  type="checkbox" name="attachment[]" id="" value="1"><label  for="inlineRadio1">IMAGE &nbsp;&nbsp;</label><input  type="checkbox" name="attachment[]" id="" value="2"><label  for="inlineRadio1">WORD DOCs&nbsp;&nbsp;</label><input  type="checkbox" name="attachment[]" id="" value="3"><label  for="inlineRadio1">PDF &nbsp;&nbsp;</label><input  type="checkbox" name="attachment[]" id="" value="4"><label  for="inlineRadio1">EXCEL &nbsp;&nbsp;</label><input  type="checkbox" name="attachment[]" id="" value="5"><label  for="inlineRadio1">ZIP</label></div> </div>';
                    break;

                case 'dd':
                    html += '<h5><br> Country Drop Down </h5><hr>';
                    html += questions;
                    html += '<div id="' + created_div_id +
                        '"><input type="hidden" value="0" class="form-control" name="s_question_options[]" ><input type="hidden"  class="form-control" value="0" name="s_question_options_fr[]" > </div>';
                    break;
            }

            html += '<div id="' + created_div_id + '"><label for="' + type +
                '_question_options" class="col-form-label">Add English Question Comment (Optional)</label><textarea type="text"  class="form-control" name="s_question_coment[]" onkeyup="$(\'#option-' +
                created_div_id + '\').val($(this).val())" > </textarea><label for="' + type +
                '_question_options" class="col-form-label">Add French Question Comment (Optional)</label><textarea type="text" id="option-' +
                created_div_id + '"  class="form-control fr_field" ' + property +
                ' name="s_question_coment_fr[]" > </textarea></div>';
            created_div_id++;
            html += '<input type="checkbox" onclick="add_attachment_box(event, 1)" value="false"  name="add_attachments_box[]" class="mt-3"> Allow Attachments &nbsp;&nbsp;'+
                    '<div class="options"></div>'+
                    '<div id="' + created_div_id +
                'another_question"><small style="color:blue" > Add Another Question </smal></div>';
            $('#' + render_div).append(html);
            var show_div = '\'custom_div\'';
            var model_type = render_div.split('-');
            model_type = model_type[0];
            show_div = '\'' + model_type + '-custom_div\'';
            var button_html = '<div class="row button_div" id="button_div">';
            // if(hide_items_array.includes('mc') == false ){  
            button_html += '<a onclick="get_html_for_multi_level(this.id,' + show_div +
                ')" id="mc" class=" mc btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Add Multiple Choice </a>';
            // }
            // if(hide_items_array.includes('sc') == false ){  
            button_html += '<a onclick="get_html_for_multi_level(this.id,' + show_div +
                ')" id="sc" class="sc btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Add Single Select </a>';
            // }
            // if(hide_items_array.includes('qa') == false && hide_items_array.length == 0){  
            button_html += '<a onclick="get_html_for_multi_level(this.id,' + show_div +
                ')" id="qa" class="btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Add Text Question</a>';
            // }
            // if(hide_items_array.includes('dd') == false  ){  
            button_html += '<a onclick="get_html_for_multi_level(this.id,' + show_div +
                ')" id="dd" class="dd btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Country Drop Down</a>';

            button_html += '<a onclick="get_html_for_multi_level(this.id,' + show_div +
                ')" id="im" class="im btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Attachment Upload</a>';
            // }
            button_html += '</div>';
            $('#' + render_div).append(button_html)
            $('#' + render_div).show();
            $('#question_div').show();
        }

        //////////////////////////// Data Inventory ///////////////////////////////////////////
        
        function get_html_for_data_inventory(type, render_div) {
            var is_same = render_div.split("-");
            is_same = is_same[0];
            var property = 'readonly=""';
            var kup = '$(\'#sub-' + created_div_id + '\').val($(this).val())';
            if (document.getElementById(is_same + '-same').checked) {
                is_same = true;
                property = 'readonly="true"';
                kup = '$(\'#sub-' + created_div_id + '\').val($(this).val())';
            } else {
                is_same = false;
                property = '';
                kup = '';
            }

            $('#' + created_div_id + 'another_question').hide();
            $('.button_div').hide();
            var questions = '<label for="question_title" class="col-form-label">Add Sub Question Title English  <strong style="color:  red">*</strong></label>'+
                            '<input type="text" name="s_question_title[]" onkeyup="$(\'#main-' +created_div_id +'\').val($(this).val())" class="form-control" >'+
                            '<label for="question_title_fr" class="col-form-label">Add Sub Question Title French  <strong style="color:  red">*</strong></label>'+
                            '<input type="text" ' +property + ' name="s_question_title_fr[]" id="main-' + created_div_id +'"  class="form-control fr_field" >'+
                            '<div class="form-group">'+
                                '<label for="question_title" class="col-form-label">Add Question Short Title English <strong style="color: red">*</strong></label>'+
                                '<input type="text" name="question_title_short" class="form-control" onkeyup="$("#q_main_q").val($(this).val())">'+
                                '<label for="question_title_fr" class="col-form-label">Add Question Short Title French <strong style="color: red">*</strong></label>'+
                                '<input type="text" name="question_title_short_fr" class="form-control fr_field" id="q_main_q">'+
                            '</div>';
            html = '<input type="hidden" name="s_q_type[]" value="' + type + '">';
            switch (type) {
                case 'mc':
                    html += '<h5>Multiple Choice Question </h5><hr>';
                    html += questions;
                    html += '<div id="' + created_div_id + '">'+
                                '<label for="' + type +'_question_options" class="col-form-label">Add (,) Separated English options <strong style="color:  red">*</strong></label>';
                    html += '<textarea id="option_en-' + created_div_id +'" onfocusout="count_comma_values(this.id)" onkeyup="check_is_type_sc_for_datepicker(this.value,this),$(\'#option_fr-' +created_div_id +'\').val($(this).val())" type="text"  class="form-control " name="s_question_options[]" ></textarea>';

                    html += '<label for="' + type +'_question_options_fr" class="col-form-label ">Add (,) Separated French options <strong style="color:  red">*</strong></label>';
                    
                    html += '<textarea id="option_fr-' + created_div_id +'" onfocusout="count_comma_values(this.id)" onkeyup="check_is_type_sc_for_datepicker(this.value,this)" id="sub-'+created_div_id+'" type="text"  class="form-control fr_field" name="s_question_options_fr[]"'+property+' ></textarea></div>';
                    break;
                case 'sc':
                    html += '<h5><br> Single Select Question </h5><hr>';
                    html += questions;
                    html += '<div id="' + created_div_id + '"><label for="' + type +
                        '_question_options" class="col-form-label">Add (,) Separated English options  <strong style="color:  red">*</strong></label><textarea id="option_en-' +
                        created_div_id +
                        '" onfocusout="count_comma_values(this.id)" onkeyup="check_is_date_picker_is_single(this.value,this) ,$(\'#option_fr-' +
                        created_div_id +
                        '\').val($(this).val()) " type="text"  class="form-control" name="s_question_options[]" ></textarea><label for="' +
                        type +
                        '_question_options_fr" class="col-form-label">Add (,) Separated French options  <strong style="color:  red">*</strong></label><textarea id="option_fr-' +
                        created_div_id +
                        '" onfocusout="count_comma_values(this.id)" onkeyup="check_is_date_picker_is_single(this.value,this)" type="text"  class="form-control fr_field" name="s_question_options_fr[]" id="sub-' +
                        created_div_id + '" ' + property + ' ></textarea></div>';
                    break;
                case 'qa':
                    html += '<h5><br> Text Question </h5><hr>';
                    html += questions;
                    html += '<div id="' + created_div_id +
                        '"><input type="hidden" value="0" class="form-control" name="s_question_options[]" ><input type="hidden"  class="form-control" value="0" name="s_question_options_fr[]" > </div>';
                    break;
                case 'im':
                    html += '<h5><br> Image Upload Question </h5><hr>';
                    html += questions;
                    html += '<div id="' + created_div_id +
                        '"><input type="hidden" value="0" class="form-control" name="s_question_options[]" ><input type="hidden"  class="form-control" value="0" name="s_question_options_fr[]" > </div>';
                    break;
                case 'dd':
                    html += '<h5><br> Country Drop Down </h5><hr>';
                    html += questions;
                    html += '<div id="' + created_div_id +
                        '"><input type="hidden" value="0" class="form-control" name="s_question_options[]" ><input type="hidden"  class="form-control" value="0" name="s_question_options_fr[]" > </div>';
                    break;
            }
            html += '<div id="' + created_div_id + '"><label for="' + type +'_question_options" class="col-form-label">Add English Question Comment (Optional)</label><textarea type="text"  class="form-control" name="s_question_coment[]" onkeyup="$(\'#option-' +created_div_id + '\').val($(this).val())" > </textarea><label for="' + type +'_question_options" class="col-form-label">Add French Question Comment (Optional)</label><textarea type="text" id="option-' +created_div_id + '"  class="form-control fr_field" ' + property + ' name="s_question_coment_fr[]" ' +property + ' > </textarea></div>';created_div_id;
            html += '<input type="checkbox" onclick="add_attachment_box(event, 1)" value="false"  name="add_attachments_box[]" class="mt-3"> Allow Attachments &nbsp;&nbsp;'+
                    '<div class="options"></div>'+
                    '<div id="' + created_div_id +'another_question"><small style="color:blue" > Add Another Question </smal></div>';
            $('#' + render_div).append(html);

            var show_div = '\'custom_div\'';
            var model_type = render_div.split('-');
            model_type = model_type[0];
            show_div = '\'' + model_type + '-custom_div\'';
            var button_html = '<div class="row button_div" id="button_div">';


            button_html += '<a onclick="get_html_for_data_inventory(this.id,' + show_div +
                ')" id="mc" class=" mc btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Add Multiple Choice </a>';
            // button_html += '<a onclick="get_html_for_data_inventory(this.id,'+show_div+')" id="sc" class="sc btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Add Single Select </a>';
            // button_html +='<a onclick="get_html_for_data_inventory(this.id,'+show_div+')" id="qa" class="btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Add Text Question</a>';
            // button_html +='<a onclick="get_html_for_data_inventory(this.id,'+show_div+')" id="dd" class="dd btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Country Drop Dwon</a>';
            // button_html +='<a onclick="get_html_for_multi_level(this.id,'+show_div+')" id="im" class="im btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Image Upload</a>';
            button_html += '</div>';
            $('#' + render_div).append(button_html)
            $('#' + render_div).show();
            $('#question_div').show();



        }

        //////////////////////////////--  --/////////////////////////////////////////

        $(function() {
            $('.edit_comment').on('click', function() {
                var div = $(this);
                var tb = div.find('input:text'); //get textbox, if exist
                if (tb.length) { //text box already exist
                    div.text(tb.val()); //remove text box & put its current value as text to the div
                    // alert(tb.val() +'----'+tb.attr('id'));
                    var post_data = {};
                    post_data['_token'] = '{{ csrf_token() }}';
                    post_data['question_id'] = tb.attr('id');
                    post_data['question_comment'] = tb.val();
                    $.ajax({
                        url: '{{ url('change_question_comment') }}',
                        method: 'POST',

                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },

                        data: post_data,
                        success: function(response) {
                            // console.log(response);
                            swal('English Question Comment Changed Successfully!', 'success');
                        }
                    });
                } else {
                    tb = $('<input>').prop({
                        'type': 'text',
                        'value': div.text(), //set text box value from div current text
                        'id': this.id,
                        'name': 'question_id',
                        'class': 'form-control'
                    });
                    div.empty().append(tb); //add new text box
                    tb.focus(); //put text box on focus
                }
            });
        });

        $(function() {
            $('.edit_comment_fr').on('click', function() {
                var div = $(this);
                var tb = div.find('input:text'); //get textbox, if exist
                if (tb.length) {
                    //text box already exist
                    div.text(tb.val()); //remove text box & put its current value as text to the div
                    // alert(tb.val() +'----'+tb.attr('id'));
                    var post_data = {};
                    post_data['_token'] = '{{ csrf_token() }}';
                    post_data['question_id'] = tb.attr('id');
                    post_data['question_comment_fr'] = tb.val();
                    $.ajax({
                        url: '{{ url('change_question_comment') }}',
                        method: 'POST',

                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },

                        data: post_data,
                        success: function(response) {
                            // console.log(response);
                            swal('French Question Comment Changed Successfully!', 'success');
                        }
                    });
                } else {
                    tb = $('<input>').prop({
                        'type': 'text',
                        'value': div.text(), //set text box value from div current text
                        'id': this.id,
                        'name': 'question_id',
                        'class': 'form-control'
                    });
                    div.empty().append(tb); //add new text box
                    tb.focus(); //put text box on focus
                }

            });
        });

        $(function() {
            $('.class_for_edit_question_js_call').on('click', function() {
                var div = $(this);
                var tb = div.find('input:text'); //get textbox, if exist
                if (tb.length) {
                    //text box already exist
                    div.text(tb.val()); //remove text box & put its current value as text to the div
                    // alert(tb.val() +'----'+tb.attr('id'));
                    var post_data = {};
                    post_data['_token'] = '{{ csrf_token() }}';
                    post_data['question_id'] = tb.attr('id');
                    post_data['question_title'] = tb.val();
                    $.ajax({
                        url: '{{ url('change_question_title') }}',
                        method: 'POST',
                        /*
                        headers: {
                        	'X-CSRF-TOKEN': $( 'meta[name="csrf-token"]' ).attr( 'content' )
                        },
                        */
                        data: post_data,
                        success: function(response) {
                            // console.log(response);
                            swal('English Question Title Changed Successfully!', 'success');
                        }
                    });
                } else {
                    tb = $('<input>').prop({
                        'type': 'text',
                        'value': div.text(), //set text box value from div current text
                        'id': this.id,
                        'name': 'question_id',
                        'class': 'form-control'
                    });
                    div.empty().append(tb); //add new text box
                    tb.focus(); //put text box on focus
                }

            });
        });

        $(function() {
            $('.class_for_edit_question_js_call_fr').on('click', function() {
                alert("clicked")
                var div = $(this);
                var tb = div.find('input:text'); //get textbox, if exist
                if (tb.length) { //text box already exist
                    div.text(tb.val()); //remove text box & put its current value as text to the div
                    // alert(tb.val() +'----'+tb.attr('id'));
                    var post_data = {};
                    post_data['_token'] = '{{ csrf_token() }}';
                    post_data['question_id'] = tb.attr('id');
                    post_data['question_title_fr'] = tb.val();
                    $.ajax({
                        url: '{{ url('change_question_title') }}',
                        method: 'POST',
                        /*
                        headers: {
                        	'X-CSRF-TOKEN': $( 'meta[name="csrf-token"]' ).attr( 'content' )
                        },
                        */
                        data: post_data,
                        success: function(response) {
                            // console.log(response);
                            swal('French Question Title Changed Successfully!', 'success');
                        }
                    });
                } else {
                    tb = $('<input>').prop({
                        'type': 'text',
                        'value': div.text(), //set text box value from div current text
                        'id': this.id,
                        'name': 'question_id',
                        'class': 'form-control'
                    });

                    div.empty().append(tb); //add new text box
                    tb.focus(); //put text box on focus
                }

            });
        });

        function get_html(type, render_div) {
            // var html;
            var is_same = render_div.split("-");
            is_same = is_same[0];
            var property = 'readonly=""';
            var kup = '$(\'#sub-' + created_div_id + '\').val($(this).val())';
            if (document.getElementById(is_same + '-same').checked) {
                is_same = true;
                property = 'readonly="true"';
                kup = '$(\'#sub-' + created_div_id + '\').val($(this).val())';
            } else {
                is_same = false;
                property = '';
                kup = '';
            }
            $('#' + created_div_id + 'another_question').hide();
            $('.button_div').hide();
            var questions =
                '<label for="question_title" class="col-form-label">Add Sub Question Title English <strong style="color:  red">*</strong></label><input type="text" name="s_question_title[]" onkeyup="$(\'#main-' +
                created_div_id +
                '\').val($(this).val())" class="form-control" ><label for="question_title_fr" class="col-form-label">Add Sub Question Title French <strong style="color:  red">*</strong></label><input type="text" id="main-' +
                created_div_id + '" ' + property + '  name="s_question_title_fr[]" class="form-control fr_field" >';
            html = '<input type="hidden" name="s_q_type[]" value="' + type + '">';
            switch (type) {
                case 'mc':
                    html += '<h5><br> Multiple Choice Question </h5><hr>';
                    html +=
                        '<input type="hidden" value="0" name="s_question_title[]" class="form-control" ><input type="hidden" value="0"  name="s_question_title_fr[]" class="form-control" >';
                    html +='<div id="'+ created_div_id +'">'+
                                '<label for="'+ type +'_question_options" class="col-form-label">Add (,) Separated English options <strong style="color:  red">*</strong></label>'+
                                '<textarea onkeyup="check_is_type_sc_for_datepicker(this.value,this),$(\'#sub-' +created_div_id +'\').val($(this).val())" type="text"  class="form-control" name="s_question_options[]"></textarea>'+
                                '<label for="' + type +'_question_options_fr" class="col-form-label">Add (,) Separated French options <strong style="color:  red">*</strong></label><textarea onkeyup="check_is_type_sc_for_datepicker(this.value,this)" type="text"  class="form-control fr_field" name="s_question_options_fr[]" id="sub-' +created_div_id + '" ' + property + ' ></textarea>'+
                            '</div>';
                    break;
                case 'sc':
                    html += '<h5><br> Single Select Question </h5><hr>';
                    html +=
                        '<input type="hidden" value="0" name="s_question_title[]" class="form-control" ><input type="hidden" value="0"  name="s_question_title_fr[]" class="form-control" >';
                    html +='<div id="' + created_div_id + '">'+
                                '<label for="' + type +'_question_options" class="col-form-label">Add (,) Separated English options <strong style="color:  red">*</strong></label>'+
                                '<textarea onkeyup="check_is_date_picker_is_single(this.value,this),$(\'#sub-' +created_div_id +'\').val($(this).val())" type="text"  class="form-control" name="s_question_options[]" ></textarea>'+
                                '<label for="' +type +'_question_options_fr" class="col-form-label">Add (,) Separated French options <strong style="color:  red">*</strong></label>'+
                                '<textarea onkeyup="check_is_date_picker_is_single(this.value,this)" type="text"  class="form-control fr_field" name="s_question_options_fr[]" id="sub-' +created_div_id + '"' + property + '></textarea>'+
                            '</div>';
                    break;
                case 'qa':
                    html += '<h5><br> Text Question</h5><hr>';
                    html += questions;
                    html += '<div id="' + created_div_id + '"></div>';
                    break;
                case 'im':
                    html += '<h5><br> Image Upload Question</h5><hr>';
                    html +='<input type="hidden" value="0" name="s_question_title[]" class="form-control" ><input type="hidden" value="0"  name="s_question_title_fr[]" class="form-control" >';
                    html += '<div id="'+ created_div_id +'"></div>';
                    break;
                case 'dd':
                    html += '<h5><br> Country Drop Down</h5><hr>';
                    html += questions;
                    // html += '<div id="'+created_div_id+'"><label for="'+type+'_question_options" class="col-form-label">Add English Question Comment (Optional)</label><textarea type="text"  class="form-control" name="s_question_coment[]" > </textarea><label for="'+type+'_question_options" class="col-form-label">Add French Question Comment (Optional)</label><textarea type="text"  class="form-control" name="s_question_coment_fr[]" > </textarea></div>';		
                    break;
            }
            
            created_div_id++;
            html += '<div id="' + created_div_id +
                'another_question"><small style="color:blue" > Add Another Question </smal></div>';
            $('#' + render_div).append(html);
            var show_div = '\'custom_div\'';
            var model_type = render_div.split('-');
            model_type = model_type[0];
            show_div = '\'' + model_type + '-custom_div\'';
            var button_html = '<div class="row button_div" id="button_div">';
            // if(hide_items_array.includes('mc') == false ){  
            // button_html += '<a onclick="cancel_the_question(this.id),get_html(this.id,'+show_div+')" id="mc" class=" mc btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Add Multiple Choice </a>';
            // }
            if (hide_items_array.includes('sc') == false) {
                button_html += '<a onclick="cancel_the_question(this.id),get_html(this.id,' + show_div +
                    ')" id="sc" class="sc btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Add Single Select </a>';
            }
            if (hide_items_array.includes('qa') == false && hide_items_array.length == 0) {
                button_html += '<a onclick="cancel_the_question(this.id),get_html(this.id,' + show_div +
                    ')" id="qa" class="btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Add Text Question</a>';
            }
            if (hide_items_array.includes('dd') == false) {
                button_html += '<a onclick="cancel_the_question(this.id),get_html(this.id,' + show_div +
                    ')" id="dd" class="dd btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Country Drop Down</a>';
            }
            button_html += '</div>';

            $('#' + render_div).append(button_html)
            $('#' + render_div).show();
            $('#question_div').show();

        }

        function cancel_the_question(theClass) {
            if (theClass == 'qa') {
                // hide_items_array.push('qa');
            }
            if (theClass !== 'qa') {
                hide_items_array.push('qa');
            }
            if (theClass === 'mc' || theClass === 'sc') {
                hide_items_array.push('sc');
                hide_items_array.push('mc');
                // hide_items_array.push('dd');
            } else {
                if (theClass !== 'qa') {
                    hide_items_array.push(theClass);
                }
            }
        }

        //this function hits when ever any button clicked
        function render_questions(type, render_div) {
            var is_same = render_div.split("-");
            is_same = is_same[0];
            var property = 'readonly=""';
            var kup = '$(\'#sub-' + created_div_id + '\').val($(this).val())';
            if (document.getElementById(is_same + '-same').checked) {
                is_same = true;
                property = 'readonly="true"';
                kup = '$(\'#sub-' + created_div_id + '\').val($(this).val())';
            } else {
                is_same = false;
                property = '';
                kup = '';
            }
            $('#' + render_div).html(' ');
            var html = '<input type="hidden" name="q_type" value="' + type + '">';
            switch (type) {
                case 'mc':
                    html += '<label for="' + type +
                        '_question_options" class="col-form-label">Add (,) Separated English options <strong style="color:  red">*</strong></label>' +
                        '<textarea onkeyup="check_is_type_sc_for_datepicker(this.value,this),$(\'#options-\').val($(this).val())" type="text"  class="form-control" name="question_options"></textarea>' +
                        '<label for="' + type +
                        '_question_options_fr" class="col-form-label">Add (,) Separated French options <strong style="color:  red">*</strong></label>' +
                        '<textarea onkeyup="check_is_type_sc_for_datepicker(this.value,this)" type="text"  class="form-control" id="options-" name="question_options_fr" ' +
                        property + ' ></textarea>' +
                        '<label for="' + type +
                        '_question_options" class="col-form-label">Add English Question Comment (Optional)</label>' +
                        '<textarea type="text"  class="form-control" name="question_coment" onkeyup="$(\'#comment-\').val($(this).val())" ></textarea>' +
                        '<label for="' + type +
                        '_question_options_fr" class="col-form-label">Add Question Comment French (Optional)</label>' +
                        '<textarea type="text" class="form-control" name="question_coment_fr" ' + property +'id="comment-" ></textarea>'+
                        '<div class="pt-2">'+
                            '<input type="checkbox" onclick="add_attachment_box(event)" value="false"  name="add_attachments_box"> Allow Attachments &nbsp;&nbsp;'+
                            '<div class="options"></div>'+
                            // '<input type="checkbox" onclick=$(this).val($(this).prop("checked")) value="false"  name="add_not_sure_box"> Add (Not Sure) Option'
                        '</div>';

                    $('#' + render_div).html(html);
                    $('#' + render_div).show();
                    $('#question_div').show();
                    break;
                case 'sc':
                    // code block
                    html += '<label for="' + type +
                        '_question_options" class="col-form-label">Add (,) Separated English options <strong style="color:  red">*</strong></label>' +
                        '<textarea onkeyup="check_is_date_picker_is_single(this.value,this),$(\'#options-\').val($(this).val())" type="text"  class="form-control" name="question_options" ></textarea>' +
                        '<label for="' + type +
                        '_question_options_fr" class="col-form-label">Add (,) Separated French options <strong style="color:  red">*</strong></label>' +
                        '<textarea onkeyup="check_is_date_picker_is_single(this.value,this)" type="text"  class="form-control" id="options-" name="question_options_fr" ' +
                        property + ' ></textarea>' +
                        '<label for="' + type +
                        '_question_options" class="col-form-label">Add English Question Comment (Optional)</label>' +
                        '<textarea type="text"  class="form-control" name="question_coment" onkeyup="$(\'#comment-\').val($(this).val())" ></textarea>' +
                        '<label for="' + type +
                        '_question_options_fr" class="col-form-label">Add Question Comment French (Optional)</label>' +
                        '<textarea type="text"  class="form-control" name="question_coment_fr" ' + property +
                        ' id="comment-"></textarea>'+
                        '<div class="pt-2">'+
                            '<input type="checkbox" onclick="add_attachment_box(event)" value="false"  name="add_attachments_box"> Allow Attachments &nbsp;&nbsp;'+
                            '<div class="options"></div>'+
                            // '<input type="checkbox" onclick=$(this).val($(this).prop("checked")) value="false"  name="add_not_sure_box"> Add (Not Sure) Option'
                        '</div>';
                    $('#' + render_div).html(html);
                    $('#' + render_div).show();
                    $('#question_div').show();
                    break;
                case 'qa':
                    html += '<label for="' + type +
                        '_question_options" class="col-form-label">Add English Question Comment (Optional)</label>' +
                        '<textarea type="text" onkeyup="$(\'#comment-\').val($(this).val())" class="form-control" name="question_coment"></textarea>' +
                        '<label for="' + type +
                        '_question_options" class="col-form-label">Add French Question Comment (Optional)</label>' +
                        '<textarea type="text"  class="form-control" name="question_coment_fr" ' + property +
                        '  id="comment-"></textarea>' +
                        '<br/>' +
                        '<input type="checkbox" onclick="add_attachment_box(event)" value="false"  name="add_attachments_box"> Allow Attachments &nbsp;&nbsp;'+
                        '<input type="checkbox" onclick=$(this).val($(this).prop("checked")) value="false"  name="add_not_sure_box"> &nbsp; Add (Not Sure) Option &nbsp;&nbsp;&nbsp;' +
                        '<div class="options"></div>';
                    $('#' + render_div).html(html);
                    $('#' + render_div).show();
                    $('#question_div').show();
                    break;
                case 'im':
                    html += '<label for="' + type +
                        '_question_options" class="col-form-label">Add English Question Comment (Optional)</label>' +
                        '<textarea type="text"  class="form-control" name="question_coment" onkeyup="$(\'#comment-\').val($(this).val())"  ></textarea>' +
                        '<label for="' + type +
                        '_question_options" class="col-form-label">Add French Question Comment (Optional)</label>' +
                        '<textarea type="text"  class="form-control" name="question_coment_fr" ' + property +
                        ' id="comment-"></textarea>' +
                        '<div class="col-md-6">' +
                        '<label for="inputCity" class="form-label">Select Attachment:</label>' +
                        '<div class="form-label">' +
                        '<input  type="checkbox" name="attachment[]" id="" value="1">' +
                        '<label  for="inlineRadio1">IMAGE &nbsp;&nbsp;</label>' +
                        '<input  type="checkbox" name="attachment[]" id="" value="2">' +
                        '<label  for="inlineRadio1">WORD DOCs&nbsp;&nbsp;</label>' +
                        '<input  type="checkbox" name="attachment[]" id="" value="3">' +
                        '<label  for="inlineRadio1">PDF &nbsp;&nbsp;</label>' +
                        '<input  type="checkbox" name="attachment[]" id="" value="4">' +
                        '<label  for="inlineRadio1">EXCEL &nbsp;&nbsp;</label>' +
                        '<input  type="checkbox" name="attachment[]" id="" value="5">' +
                        '<label  for="inlineRadio1">ZIP</label>' +
                        '</div>' +
                        '</div>';
                    $('#' + render_div).html(html);
                    $('#' + render_div).show();
                    $('#question_div').show();
                    // code block
                    break;

                case 'dc':
                    let checked = "checked";
                    html += '<label for="' + type +
                        '_question_options" class="col-form-label">Add English Question Comment (Optional)</label>' +
                        '<textarea type="text"  class="form-control" name="question_coment" onkeyup="$(\'#comment-\').val($(this).val())"  ></textarea>' +
                        '<label for="' + type +
                        '_question_options" class="col-form-label">Add French Question Comment (Optional)</label>' +
                        '<textarea type="text"  class="form-control" name="question_coment_fr" ' + property +
                        ' id="comment-"></textarea>' +
                        '<label for="' + type + '_question_options" class="col-form-label">Dynamic Items</label>' +
                        '<select type="text"  class="form-control" name="dropdown_value_from">' +
                        '<option value="1">DATA-ELEMENTS-OF-ORGANIZATION-WITH-SECTIONS</option>' +
                        '<option value="2">ASSETS-OF-ORGANIZATION</option>' +
                        '<option value="3">COUNTRIES</option>' +
                        '<option value="4">TYPE OF DATA CLASSIFICATION</option>' +
                        '<option value="5">ASSETS COMBINED IMPACT</option>' +
                        '<option value="6">ASSETS TIER</option>' +
                        '</select>' +
                        '<br/>' +
                        '<div class="pt-2">'+
                            '<input type="checkbox" onclick="add_attachment_box(event)" value="false"  name="add_attachments_box"> Allow Attachments &nbsp;&nbsp;'+
                            '<input type="checkbox" onclick=$(this).val($(this).prop("checked")) value="false"  name="add_not_sure_box"> Add (Not Sure) Option'+
                            '<div class="options"></div>'+
                        '</div>';
                    $('#' + render_div).html(html);
                    $('#' + render_div).show();
                    $('#question_div').show();
                    // code block
                    break;
                case 'cc':
                    var show_div = '\'custom_div\'';
                    var model_type = render_div.split('-');
                    model_type = model_type[0];
                    show_div = '\'' + model_type + '-custom_div\'';
                    $('#' + model_type + '-custom_div').empty();
                    hide_items_array = [];
                    // html = '<div class="row button_div" id="button_div"><a onclick="cancel_the_question(this.id),get_html(this.id,'+show_div+')" id="mc" class="cc btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Add Multiple Choice </a><a onclick="cancel_the_question(this.id),get_html(this.id,'+show_div+')" id="sc" class="sc btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Add Single Select </a><a onclick="get_html(this.id,'+show_div+')" id="qa" class="btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Add Text Question</a><a onclick="cancel_the_question(this.id),get_html(this.id,'+show_div+')" id="dd" class="dd btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">County Drop Down</a></div>';  
                    var button_html = '<div class="row button_div" id="button_div">';

                    // if(hide_items_array.includes('mc') == false ){  
                    // button_html += '<a onclick="cancel_the_question(this.id),get_html(this.id,'+show_div+')" id="mc" class=" mc btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Add Multiple Choice </a>';
                    // }
                    if (hide_items_array.includes('sc') == false) {
                        button_html += '<a onclick="cancel_the_question(this.id),get_html(this.id,' + show_div +
                            ')" id="sc" class="sc btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Add Single Select </a>';
                    }
                    if (hide_items_array.includes('qa') == false && hide_items_array.length == 0) {
                        button_html += '<a onclick="cancel_the_question(this.id),get_html(this.id,' + show_div +
                            ')" id="qa" class="btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Add Text Question</a>';
                    }
                    if (hide_items_array.includes('dd') == false) {

                        button_html += '<a onclick="cancel_the_question(this.id),get_html(this.id,' + show_div +
                            ')" id="dd" class="dd btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Country Drop Down</a>';
                    }
                    button_html += '</div>';
                    $('#' + render_div).append(html);
                    $('#' + render_div).append(button_html);
                    $('#' + render_div).show();
                    $('#question_div').show();
                    break;

                    ///////////////////////////////////////////////////////////////////////////
                    //////////////////////////Parent Child/////////////////////////////////
                    ///////////////////////////////////////////////////////////////////////////
                case 'parent':
                    var show_div = '\'custom_div\'';
                    var model_type = render_div.split('-');
                    model_type = model_type[0];
                    show_div = '\'' + model_type + '-custom_div\'';
                    $('#' + model_type + '-custom_div').empty();
                    hide_items_array = [];
                    var button_html = '<div class="row button_div" id="button_div">';

                    // if(hide_items_array.includes('mc') == false ){  
                    button_html += '<a onclick="get_html_for_multi_level(this.id,' + show_div +
                        ')" id="mc" class=" mc btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Add Multiple Choice </a>';
                    // }
                    // if(hide_items_array.includes('sc') == false ){  
                    button_html += '<a onclick="get_html_for_multi_level(this.id,' + show_div +
                        ')" id="sc" class="sc btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Add Single Select </a>';
                    // }
                    // if(hide_items_array.includes('qa') == false && hide_items_array.length == 0 ){  
                    button_html += '<a onclick="get_html_for_multi_level(this.id,' + show_div +
                        ')" id="qa" class="btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Add Text Question</a>';
                    // }
                    // if(hide_items_array.includes('dd') == false ){  
                    button_html += '<a onclick="get_html_for_multi_level(this.id,' + show_div +
                        ')" id="dd" class="dd btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Country Drop Down</a>';
                    // }
                    button_html += '</div>';
                    $('#' + render_div).append(html);
                    $('#' + render_div).append(button_html);
                    $('#' + render_div).show();
                    $('#question_div').show();
                    break;
                    ///////////////////////////////////////////////////////////////////////////
                    //////////////////////////Data Inventory/////////////////////////////////
                    ///////////////////////////////////////////////////////////////////////////

                case 'data':
                    var show_div = '\'custom_div\'';
                    var model_type = render_div.split('-');
                    model_type = model_type[0];
                    show_div = '\'' + model_type + '-custom_div\'';
                    $('#' + model_type + '-custom_div').empty();
                    hide_items_array = [];
                    var button_html = '<div class="row button_div" id="button_div">';
                    button_html += '<a onclick="get_html_for_data_inventory(this.id,' + show_div +
                        ')" id="mc" class=" mc btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Add Multiple Choice </a>';
                    // button_html += '<a onclick="get_html_for_data_inventory(this.id,'+show_div+')" id="sc" class="sc btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Add Single Select </a>';
                    button_html += '</div>';
                    $('#' + render_div).append(html);
                    $('#' + render_div).append(button_html);
                    $('#' + render_div).show();
                    $('#question_div').show();
                    break;

                    ///////////////////////////////////////////////////////////////////////////
                    //////////////////////////Dynamic Controller Items/////////////////////////////////
                    ///////////////////////////////////////////////////////////////////////////

            }
        }

    </script>

    <script>
        //***********************update cc comments
        $(function() {
            $('.cc_cmment').on('click', function() {

                var div = $(this);
                var question = div.attr("id").split("-");
                console.log(question);
                var num_of_comment = false;
                if (question.length == 6) {
                    num_of_comment = question[5];
                }
                var tb = div.find('input:text'); //get textbox, if exist
                if (tb.length) {
                    //text box already exist
                    div.text(tb.val()); //remove text box & put its current value as text to the div
                    // alert(tb.val() +'----'+tb.attr('id'));
                    var post_data = {};
                    post_data['_token'] = '{{ csrf_token() }}';
                    post_data['question_id'] = question[4];
                    post_data['type'] = question[1];
                    post_data['comment'] = tb.val();
                    post_data['q_type'] = question[2];
                    post_data['number_of_comment'] = num_of_comment;
                    console.log(post_data);

                    $.ajax({
                        url: '{{ url('update_sc_comment') }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: post_data,
                        success: function(response) {
                            // console.log(response);
                            swal('Question Comment Updated Successfully!', 'success');
                        }
                    });
                } else {
                    tb = $('<input>').prop({
                        'type': 'text',
                        'value': div.text(), //set text box value from div current text
                        'id': this.id,
                        'name': 'question_id',
                        'class': 'form-control'
                    });
                    div.empty().append(tb); //add new text box
                    tb.focus(); //put text box on focus
                }
            });
        });
        
        //**********************update cc options    
        function update_sc_question(a, type) {
            // alert($('#text_area-'+type+'-'+a).val());
            var can_update = true;
            var count = $('#text_area-' + type + '-' + a).val().split(",");
            count = count.length;
            var options = $('#text_area-en-' + a).val();
            var options_fr = $('#text_area-fr-' + a).val();
            // console.log(options);
            // console.log(options_fr);			


            switch (type) {
                case 'en':
                    // alert($('#text_area-fr-'+a).val());
                    var count_other = $('#text_area-fr-' + a).val().split(",");
                    count_other = count_other.length;
                    if (count != count_other) {
                        can_update = false;
                        swal('There should be same number of (,) separated options in english and french version.', '',
                            'error');
                        $('#text_area-en-' + a).val($('#input-' + type + '-' + a).val());
                    }
                    break;

                case 'fr':
                    // alert('fr');
                    // alert($('#text_area-en-'+a).val());
                    var count_other = $('#text_area-en-' + a).val().split(",");
                    count_other = count_other.length;
                    if (count != count_other) {
                        can_update = false;
                        swal('There should be same number of (,) separated options in english and french version.', '',
                            'error');
                        $('#text_area-fr-' + a).val($('#input-' + type + '-' + a).val());

                    }

                    break;
            }

            if (can_update) {

                var post_data = {};
                post_data['_token'] = '{{ csrf_token() }}';
                post_data['form_key'] = a;
                post_data['type'] = type;
                post_data['q_type'] = 'sc';
                post_data['new_options'] = options;
                post_data['new_options_fr'] = options_fr;
                $.ajax({
                    url: '{{ url('update_cc_options') }}',
                    method: 'POST',
                    data: post_data,
                    success: function(response) {
                        swal('Options Updated Successfully!', 'success');
                        setTimeout(function() {
                            location.reload()
                        }, 800);
                    }

                });

            }
        }

        $(document).ready(function() {
            //$('#form-body').slideToggle("slow");
            $('.change-heading-icon').click(function(event) {
                event.preventDefault();
                var heading_sec_id = $(this).attr('sec-id');
                $('div#section-heading-' + heading_sec_id).hide();
                $('#save-sec-heading-' + heading_sec_id).show();
                $('#heading-edit-' + heading_sec_id).hide();
                $('div#section-heading-edit-' + heading_sec_id).show();
            });

            $('.change-heading-btn').click(function(event) {
                event.preventDefault();
                var heading_sec_id = $(this).attr('sec-id');
                $('#save-sec-heading-' + heading_sec_id).hide();
                $('#heading-edit-' + heading_sec_id).show();
                $('div#section-heading-edit-' + heading_sec_id).hide();
                var old_heading = $('#new-section-heading-' + heading_sec_id).attr('old-value');
                var new_heading = $('#new-section-heading-' + heading_sec_id).val();
                var old_heading_fr = $('#new-section-heading_fr-' + heading_sec_id).attr('old-value');
                var new_heading_fr = $('#new-section-heading_fr-' + heading_sec_id).val();
                $('div#section-heading-' + heading_sec_id + ' > h3').text(new_heading);
                $('div#section-heading-' + heading_sec_id).show();

                var form_id = $('#form-id').val();

                var post_data = {};

                post_data['_token'] = '{{ csrf_token() }}';
                post_data['form_id'] = form_id;
                post_data['title'] = new_heading;
                post_data['old_title'] = old_heading;

                post_data['title_fr'] = new_heading_fr;
                post_data['old_title_fr'] = old_heading_fr;

                post_data['user_id'] = {{ Auth::user()->client_id }};
                //post_data['user_id']       =  23;
                post_data['updated_by'] = {{ Auth::id() }};
                post_data['form_section_id'] = heading_sec_id;

                <?php if (Auth::user()->role==1): ?>
                post_data['is_admin'] = 1;
                <?php endif; ?>

                $.ajax({
                    url: '{{ route('update_form_section_heading') }}',
                    method: 'POST',
                    data: post_data,
                    success: function(response) {
                        console.log(response);
                        //alert('Forms assigned to client');
                        if (response.status == 200) {
                            swal("", "Section Title Updated Successfully!", "success");
                        } else {

                            swal("", response.msg, "error");
                        }

                    }

                });

            });

            <?php if (Auth::user()->role == 1): ?>

            $('.sec-ctgry').change(function() {
                var section_name = $(this).attr('sec');
                var form_id = $(this).attr('form-id');
                var category_id = $(this).val();

                if (category_id != '') {

                    var post_data = {};

                    post_data['_token'] = '{{ csrf_token() }}';
                    post_data['form_id'] = form_id;
                    post_data['sec_name'] = section_name;
                    post_data['ctg_id'] = category_id;
                    $.ajax({
                        url: '{{ route('asgn_sec_ctgry') }}',
                        method: 'POST',
                        /*
                        	headers: {
                        		'X-CSRF-TOKEN': $( 'meta[name="csrf-token"]' ).attr( 'content' )
                        	},
                        */
                        data: post_data,
                        success: function(response) {
                            console.log(response);
                            //alert('Forms assigned to client');
                        }
                    });
                }

            });

            <?php endif; ?>

            $('.sec-heading').click(function(e) {

                var tag = e.target.tagName.toLowerCase();
                console.log(tag);
                var num = $(this).attr('num');

                var up = $(this).find($('i.fa-chevron-up')).length;
                var down = $(this).find($('i.fa-chevron-down')).length;

                if (tag == 'div' || tag == 'span' || tag == 'h3') {
                    $("#section-" + num + "-body").slideToggle("slow");
                    if (up) {
                        $("i.fa-chevron-up", this).toggleClass("fa-chevron-up fa-chevron-down");
                    }
                    if (down) {
                        $("i.fa-chevron-down", this).toggleClass("fa-chevron-down fa-chevron-up");
                    }
                }
            });
        });

        $(".updatesorting").click(function() {
            var fq_id = this.id;
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ url('updateSorting') }}',
                type: 'POST',
                data: {
                    'fq_id': fq_id,
                    'sort_order': $('#sort_order_' + fq_id).val(),
                    'form_id': $('#form_id_to').val()

                },
                success: function(data) {
                    if (data.status) {
                        swal('', data.msg, 'success');
                    } else {
                        swal('', data.msg, 'error');
                    }
                }
            });
        });

    </script>
@endsection
