@extends('admin.layouts.admin_app')
@section('content')
@section('page_title')
    {{ __('Group Lists') }}
@endsection
    <link rel="stylesheet" type="text/css" href="{{ url('public/custom_form/css/style.css') }}">
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
    <div class="app-title">
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="javaScript">{{ __('Question Group Details') }}</a></li>
        </ul>
    </div>

    <input type="hidden" id="group_id" value="{{$group->id}}">
    
    <div class="row py-3">  
        <div class="col-12 d-flex justify-content-end">
            <button type="button" class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#addSectionModel">
                <i class="fa fa-plus"></i>Add New Section
            </button>
        </div>
    </div>

    <div class="row p-0">  
        <div class="col-12 p-0 m-0">
            <div id="" class="bg-danger">
                <div class="head" id="form-heading">
                    <ul>
                        <li><strong>★</strong></li>
                        <li><i class="fa fa-chevron-up"></i></li>
                    </ul>
                    <h3>{{ $group->group_name }}</h3> 
                </div>
            </div>
        </div> 
    </div>

    <div class="row bg-white pt-3" style="margin-top: -18px;">  
        @foreach($group->sections as $section)
            <div class="col-12 p-3 " style="overflow-y:auto;">
                <span class="w-100" type="button">
                    <div id="form-heading" class="head">
                        <ul  data-toggle="collapse" data-target="#questions_area{{$section->id}}" aria-expanded="true" aria-controls="questions_area{{$section->id}}">
                            <li><strong>★</strong></li>
                            <li><i class="fa fa-chevron-up" aria-hidden="true"></i></li>
                        </ul>
                        <div class="w-100 px-4 d-flex justify-content-between">
                            <h3 id="title_{{$section->id}}" title="Click to Edit Title" onclick="edit_section_title('{{$section->id}}', '{{ $section->section_title}}', '{{$section->section_title_fr}}')">{{ $section->section_title }}</h3>
                            <button type="button" class="btn btn-success btn-sm pull-right" onclick="section_id ={{$section->id}};" data-toggle="modal"  data-target="#addQuestionModel">
                                <i class="fa fa-plus"></i> Add New Question 
                            </button>
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
                                                Sort Order: <input type="text" id="" style="width:50px;height:20px;1px solid #000;color:#000" value="{{ $question->question_num }}"><a href="javascript:;" class="updatesorting">Update Sorting</a>
                                            </span>
                                            <!-- <span>
                                                <i class=" edit_enable">
                                                    <span class="fa fa-edit"></span>
                                                </i>
                                            </span> -->
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
                                            @case('im')
                                                @php 
                                                    $accepted_formates = ['Images', '.docs', '.pdf', '.xlxs , .csv', '.zip'];
                                                    $attachments =json_decode($question->accepted_formates);
                                                @endphp 
                                                @if($attachments)
                                                    <p class="mt-3"> Accepted Formates </p>
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <input type="checkbox" name="attachment[]" id="img" {{ in_array($i, $attachments) ? 'checked=true' : '' }} disabled />{{ $accepted_formates[$i - 1] }}
                                                    @endfor
                                                @endif
                                                <input type="file" class="dropify" disabled>
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


    {{-- Add Section  model start --}}
        <div class="modal fade" id="edit_section_modal" tabindex="-1" role="dialog"
            aria-labelledby="edit_section_modal" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="edit_section_modal_label">Edit Section</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="hidden" id="e_section_id">
                            <label for="section_title" class="col-form-label">Section title <small>(English)</small><strong style="color: red">*</strong></label>
                            <input type="text" name="section_title" class="form-control" id="e_section_title" onkeyup="$('#e_section_title_fr').val($(this).val())">
                        </div>
                        <div class="form-group">
                            <label for="section_title_fr" class="col-form-label">Section title <small>(French)</small> <strong style="color: red">*</strong></label>
                            <input type="text" name="section_title_fr" class="form-control fr_field" id="e_section_title_fr">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a type="" class="btn btn-outline-danger" data-dismiss="modal">
                            <i class="fa fa-ban"></i>
                            Cancel
                        </a>
                        <button type="button" onclick="update_section(event)" class="btn btn-primary">Update Section</button>
                    </div>
                    
                </div>
            </div>
        </div>
    {{-- Add Section model end --}}

@endsection
@push('scripts')
    <script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script>
        var section_id = 0;
        $('.dropify').dropify();

        var previous_type = null;
        var idd = 0;

        function get_html(type){

            // child_questions = [];
            // current = 0;

            if (previous_type !=  type || type == 'sp') {
                previous_type = type;
                $('#ggroup_id').val(idd);
                console.log("prev ", previous_type, "current ", type);
                
                var html ="";
                switch (type) {
                    case 'mc':
                        html +='<div class="form-group" id="qmodel-type">'+
                                    '<input type="hidden" value="'+type+'" name="type">'+
                                    '<label for="question_options" class="col-form-label">Add (,) Separated English options <strong style="color:  red">*</strong></label>'+
                                    '<textarea onkeyup="$(`#question_options_fr`).val($(this).val())" type="text" class="form-control" name="question_options" id="question_options"></textarea>'+
                                    '<label for="question_options_fr" class="col-form-label">Add (,) Separated French options <strong style="color:  red">*</strong></label>'+
                                    '<textarea type="text" class="form-control fr_field" id="question_options_fr" name="question_options_fr"></textarea>'+
                                    '<label for="question_comment" class="col-form-label">Add English Question Comment (Optional)</label>'+
                                    '<textarea type="text" class="form-control" name="question_coment" onkeyup="$( `#question_comment_fr`).val($(this).val())" id="question_comment"></textarea>'+
                                    '<label for="question_comment_fr" class="col-form-label">Add Question Comment French (Optional)</label>'+
                                    '<textarea type="text" class="form-control fr_field" name="question_coment_fr" id="question_comment_fr"></textarea>'+
                                    '<div class="d-flex pt-3">'+
                                        '<input type="checkbox" onclick="add_attachment_box(event)" value="false"  name="add_attachments_box">&nbsp; Allow Attachments &nbsp;&nbsp;'+
                                    '</div>'+
                                    '<div class="options"></div>'+
                                '</div>';
                        break;
                    case 'sc':
                        html +='<div class="form-group" id="qmodel-type">'+
                                    '<input type="hidden" value="'+type+'" name="type">'+
                                    '<label for="question_options" class="col-form-label">Add (,) Separated English options <strong style="color:  red">*</strong></label>'+
                                    '<textarea onkeyup="$(`#question_options_fr`).val($(this).val())" type="text" class="form-control" name="question_options" id="question_options"></textarea>'+
                                    '<label for="question_options_fr" class="col-form-label">Add (,) Separated French options <strong style="color:  red">*</strong></label>'+
                                    '<textarea type="text" class="form-control fr_field" id="question_options_fr" name="question_options_fr"></textarea>'+
                                    '<label for="question_comment" class="col-form-label">Add English Question Comment (Optional)</label>'+
                                    '<textarea type="text" class="form-control" name="question_coment" onkeyup="$( `#question_comment_fr`).val($(this).val())" id="question_comment"></textarea>'+
                                    '<label for="question_comment_fr" class="col-form-label">Add Question Comment French (Optional)</label>'+
                                    '<textarea type="text" class="form-control fr_field" name="question_coment_fr" id="question_comment_fr"></textarea>'+
                                    '<div class="d-flex pt-3">'+
                                        '<input type="checkbox" onclick="add_attachment_box(event)" value="false"  name="add_attachments_box">&nbsp; Allow Attachments &nbsp;&nbsp;'+
                                    '</div>'+
                                    '<div class="options"></div>'+
                                '</div>';
                        break;
                    case 'qa':
                        html +='<div class="form-group" id="qmodel-type">'+
                                    '<input type="hidden" value="'+type+'" name="type">'+
                                    '<label for="question_comment" class="col-form-label">Add English Question Comment (Optional)</label>'+
                                    '<textarea type="text" class="form-control fr_field" name="question_coment" onkeyup="$( `#question_comment_fr`).val($(this).val())" id="question_comment"></textarea>'+
                                    '<label for="question_comment_fr" class="col-form-label">Add Question Comment French (Optional)</label>'+
                                    '<textarea type="text" class="form-control" name="question_coment_fr" id="question_comment_fr"></textarea>'+
                                    '<div class="d-flex pt-3">'+
                                        '<input type="checkbox" onclick="add_attachment_box(event)" value="false"  name="add_attachments_box">&nbsp; Allow Attachments &nbsp;&nbsp;'+
                                    '</div>'+
                                    '<div class="options"></div>'+
                                '</div>';
                        break;
                    // case 'im':
                        html += 
                        '<div class="pt-2">'+
                            '<label for="accepted_formates" class="form-label"> Select Accepted Formates :</label>'+
                            '<div class="form-label"><input type="checkbox" name="attachment[]" id="" value="1">'+
                                '<label for="inlineRadio1">IMAGE &nbsp;&nbsp;</label>'+
                                '<input type="checkbox" name="attachment[]" id="" value="2">'+
                                '<label for="inlineRadio1">WORD DOCs&nbsp;&nbsp;</label>'+
                                '<input type="checkbox" name="attachment[]" id="" value="3">'+
                                '<label for="inlineRadio1">PDF &nbsp;&nbsp;</label>'+
                                '<input type="checkbox" name="attachment[]" id="" value="4">'+
                                '<label for="inlineRadio1">EXCEL &nbsp;&nbsp;</label>'+
                                '<input type="checkbox" name="attachment[]" id="" value="5">'+
                                '<label for="inlineRadio1">ZIP</label>'+
                            '</div>'+
                            '<div class="form-group" id="qmodel-type">'+
                                '<input type="hidden" value="'+type+'" name="type">'+
                                '<label for="question_comment" class="col-form-label">Add English Question Comment (Optional)</label>'+
                                '<textarea type="text" class="form-control" name="question_coment" onkeyup="$( `#question_comment_fr`).val($(this).val())" id="question_comment"></textarea>'+
                                '<label for="question_comment_fr" class="col-form-label">Add Question Comment French (Optional)</label>'+
                                '<textarea type="text" class="form-control" name="question_coment_fr" id="question_comment_fr"></textarea>'+
                            '</div>'+
                        '</div>';
                        break;
                    case 'dc':
                        html +='<div class="form-group" id="qmodel-type">'+
                                    '<label for="dc_question_options" class="col-form-label">Dynamic Items</label>'+
                                    '<select type="text" class="form-control" name="dropdown_value_from">'+
                                        '<option value="1">DATA-ELEMENTS-OF-ORGANIZATION-WITH-SECTIONS</option>'+
                                        '<option value="2">ASSETS-OF-ORGANIZATION</option>'+
                                        '<option value="3">COUNTRIES</option>'+
                                        '<option value="4">TYPE OF DATA CLASSIFICATION</option>'+
                                        '<option value="5">ASSETS COMBINED IMPACT</option>'+
                                        '<option value="6">ASSETS TIER</option>'+
                                    '</select>'+
                                    '<input type="hidden" value="'+type+'" name="type">'+
                                    '<label for="question_comment" class="col-form-label">Add English Question Comment (Optional)</label>'+
                                    '<textarea type="text" class="form-control" name="question_coment" onkeyup="$( `#question_comment_fr`).val($(this).val())" id="question_comment"></textarea>'+
                                    '<label for="question_comment_fr" class="col-form-label">Add Question Comment French (Optional)</label>'+
                                    '<textarea type="text" class="form-control fr_field" name="question_coment_fr" id="question_comment_fr"></textarea>'+
                                    '<div class="d-flex pt-3">'+
                                        '<input type="checkbox" onclick="add_attachment_box(event)" value="false"  name="add_attachments_box">&nbsp; Allow Attachments &nbsp;&nbsp;'+
                                    '</div>'+
                                    '<div class="options"></div>'+
                                '</div>';	
                        break;
                    // case 'sp':
                    //     $("#render_special_question").html("");
                    //     $('#add_more_question').html(
                    //     '<div class="row">'+
                    //         '<input type="hidden" value="'+type+'" id="p_type">'+   
                    //         '<a onclick="get_multi_html(\'mc\')" id="mc" class="btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Add Multiple Choice Question</a>'+
                    //         '<a onclick="get_multi_html(\'sc\')" id="sc" class="btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Add Single Select Question</a>'+
                    //         '<a onclick="get_multi_html(\'qa\')" id="qa" class="btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Add Text Question</a>'+
                    //         '<a onclick="get_multi_html(\'im\')" id="im" class="btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Attachment Upload Option</a>'+
                    //         '<a onclick="get_multi_html(\'dc\')" id="dc" class="btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Dynamic Controlled Items</a>'+
                    //     '</div>');
                    //     $('#specialQuestionModel').modal('show');  
                    //     return ;
                    //     break;
                    // case 'da':
                        $("#render_special_question").html("");
                        $('#add_more_question').html(
                        '<div class="row">'+
                            '<input type="hidden" value="'+type+'" id="p_type">'+  
                            '<a onclick="get_multi_html(\'mc\')" id="mc" class="btn btn-warning btn-sm ml-1 mb-1 mr-1 mt-1">Add Multiple Choice</a>'+
                        '</div>');
                        $('#specialQuestionModel').modal('show');  

                        return ;
                        break;
                }

                $('#render_question_data').html(html);
            }
        }

        function disable_fr_options(c) {
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

        function add_attachment_box(event, multi = 0) {
            $(event.target).val($(event.target).prop("checked"))
            if(event.target.checked){
                // if(multi){
                //     $('.options').last().html('<label for="inputCity" class="form-label mt-3">'+'Select Accepted Formates:'+'</label>'+
                //         '<div class="form-label">'+
                //             '<input  type="checkbox" name="attachment_" id="" value="1">'+
                //             '<label  for=""> Images &nbsp;&nbsp;</label>'+
                //             '<input  type="checkbox" name="attachment_" id="" value="2">'+
                //             '<label  for=""> WORD Document&nbsp;&nbsp;</label>'+
                //             '<input  type="checkbox" name="attachment_" id="" value="3">'+
                //             '<label  for=""> PDF &nbsp;&nbsp;</label>'+
                //             '<input  type="checkbox" name="attachment_" id="" value="4">'+
                //             '<label  for=""> EXCEL &nbsp;&nbsp;</label>'+
                //             '<input  type="checkbox" name="attachment_" id="" value="5">'+
                //             '<label  for=""> ZIP</label>'+
                //         '</div>'
                //     );
                // }else{
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
                            '<label  for="inlineRadio1"> ZIP  &nbsp;&nbsp</label>'+
                            '<input  type="checkbox" name="attachment" id="" value="0">'+
                            '<label  for="inlineRadio1"> Allow All</label>'+
                        '</div>'
                    );
                // }
            }else{
                $('.options').html("");
            }
            
        }

        function add_new_question(event) {
            event.preventDefault();
            var formdata = new FormData($("#add_question_form")[0]);
            formdata.append('section_id', section_id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '{{route("add_question_to_group")}}',
                dataType:'json',
                data: formdata,
                type:'post',
                processData: false,
                contentType: false,
                success: function (res) {
                    if (!res.status) {
                        swal('', res.error, 'warning');
                    }else{
                        $('#addQuestionModel').modal('hide');
                        swal('', res.success, 'success');
                        $('textarea').val("");
                        $('input').val("");
                        
                        setTimeout(() => {
                            location.reload();
                        }, 500);
                    }
                    
                }
            });
        }

        function update_question(id) {
            const type = $(id).attr('type');
            const q_id = $(id).attr('q_id');
            const val  = $(id).val();
            const formdata = {
                name:   $(id).attr('name'),
                val:    val,
                q_id:   q_id
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '{{route('update_group_question')}}',
                data: formdata,
                type:'post',
                success: function (res) {
                    console.log("res", res);
                    if (res.status) {
                        switch (type) {
                            case "english":
                                $(`#append_div_to_edit_english_${q_id}`).html("");
                                $(`#display_english_question_${q_id}`).append(
                                    `<span class="edit_question_english" onclick=edit_question_ajax(event) id="edit_en_q_${q_id}" q_id="${q_id}" q_val="${val}" value="${val}" type="english">${val}</span> `
                                );
                                break;
                            case "fr":
                                $(`#append_div_to_edit_fr_${q_id}`).html("");

                                $(`#display_fr_question${q_id}`).append(
                                    `<span class="edit_question_fr ml-1" onclick="edit_question_ajax(event)" id="edit_fr_q_${q_id}" q_id="${q_id}" q_val="${val}" type="fr" value="${val}">${val}</span>`
                                );
                                break;
                            case "en_comment":
                                $(`#append_div_to_edit_en_comment_${q_id}`).html("");
                                $(`#display_english_comment_${q_id}`).append(
                                    `<span class="edit_english_comment" onclick="edit_question_ajax(event)" id="edit_en_c_${q_id}" q_id="${q_id}" q_val="${val}" type="en_comment" value="${val}">${val}</span>`
                                );
                                break;
                            case "fr_comment":
                                $(`#append_div_to_edit_fr_comment_${q_id}`).html("");

                                $(`#display_fr_comment${q_id}`).append(
                                    `<span class="edit_fr_comment" onclick="edit_question_ajax(event)" id="edit_fr_c_${q_id}" q_id="${q_id}" q_val="${val}" type="fr_comment" value="${val}">${val}</span>`
                                );
                                break;
                            default:
                                break;
                        }
                        swal('', res.success, 'success');
                    }else{
                        swal('', res.error, 'warning');
                    }
                    
                }
            });
        }

        function edit_question_ajax(e) {

            let q_id  = $(e.target).attr('q_id');
            let q_val = $(e.target).attr('q_val');
            let id    = $(e.target).attr('id');
            let type  = $(e.target).attr('type');
            switch (type) {
                case "english":
                    $(`#append_div_to_edit_english_${q_id}`).append(
                        `<textarea id="edit_en_q_${q_id}" type="${type}" q_id="${q_id}" name="edit_en_q" class="mr-2" rows="3">${q_val}</textarea>
                        <button class="btn btn-success" onclick=update_question(edit_en_q_${q_id})>  
                            <i class="fas fa-check-circle m-0"></i>
                        </button>`
                    );
                    break;
                case "fr":
                    $(`#append_div_to_edit_fr_${q_id}`).append(
                        `<textarea id="edit_fr_q_${q_id}" type="${type}" q_id="${q_id}" name="edit_fr_q" class="mr-2" rows="3">${q_val}</textarea>
                        <button class="btn btn-success" onclick=update_question(edit_fr_q_${q_id})>  
                            <i class="fas fa-check-circle m-0"></i>
                        </button>`
                    );
                    break;
                case "en_comment":
                    $(`#append_div_to_edit_en_comment_${q_id}`).append(
                        `<textarea id="edit_en_c_${q_id}" type="${type}" q_id="${q_id}" name="edit_en_c" class="mr-2" rows="3">${q_val}</textarea>
                        <button class="btn btn-success" onclick=update_question(edit_en_c_${q_id})>  
                            <i class="fas fa-check-circle m-0"></i>
                        </button>`
                    );
                    break;
                case "fr_comment":
                    $(`#append_div_to_edit_fr_comment_${q_id}`).append(
                        `<textarea id="edit_fr_c_${q_id}" type="${type}" q_id="${q_id}" name="edit_fr_c" class="mr-2" rows="3">${q_val}</textarea>
                        <button class="btn btn-success" onclick=update_question(edit_fr_c_${q_id})>  
                            <i class="fas fa-check-circle m-0"></i>
                        </button>`
                    );
                    break;
                default:
                    break;
            }
            $(e.target).remove();
        }

        function render_question(){
            // render_area
            let id = location.href.split('/')[location.href.split('/').length - 1];
            $.ajax({
                url: "/group/question/get/"+id,
                method: 'GET',
                success: function(response) {
                    $("#title").html(response.group_name);
                    idd = response.id;
                    console.log(response);
                }
            }); 
        }render_question();

        // function get_multi_html(type){
        //     current = current + 1;
        //     if (current > 1) {
        //         add_question_array();
        //     }

        //     previous_multi_type = type
        //     html = "";

        //     var questions = 
        //         '<label for="question_title" class="col-form-label">Add Sub Question Title English  <strong style="color:  red">*</strong></label>'+
        //         '<input type="text" name="s_question_title" id="s_question_title" class="form-control" onkeyup="$(\'#s_question_title_fr\').val($(this).val())" >'+
        //         '<label for="question_title_fr" class="col-form-label">Add Sub Question Title French  <strong style="color:  red">*</strong></label>'+
        //         '<input type="text"  id="s_question_title_fr" name="s_question_title_fr" class="form-control fr_field" >'+
        //         '<div class="form-group">'+

        //             '<label for="question_title_short" class="col-form-label">Add Question Short Title English <strong style="color: red">*</strong></label>'+
        //             '<input type="text" name="question_title_short" id="question_title_short" class="form-control" onkeyup="$(\'#question_title_short_fr\').val($(this).val())">'+
        //             '<label for="question_title_short_fr" class="col-form-label">Add Question Short Title French <strong style="color: red">*</strong></label>'+
        //             '<input type="text" name="question_title_short_fr" class="form-control fr_field" id="question_title_short_fr">'+
        //         '</div>'+
        //         '<input  type="hidden" name="s_q_type" value="' + type + '">';
            
        //     switch (type) {
        //         case 'mc':
        //             html += '<h5><br> Multiple Choice Question </h5>';
        //             html += questions;
        //             html += 
        //                 '<div>'+
        //                     '<label for="s_question_options" class="col-form-label">Add (,) Separated English options  <strong style="color:  red">*</strong></label>'+
        //                     '<textarea onkeyup="$(\'#s_question_options_fr\').val($(this).val())" id="s_question_options" type="text"  class="form-control" name="s_question_options"></textarea>'+

        //                     '<label for="s_question_options_fr" class="col-form-label ">Add (,) Separated French options  <strong style="color:  red">*</strong></label>'+
        //                     '<textarea type="text"  class="form-control"  id="s_question_options_fr" name="s_question_options_fr"></textarea>'+
        //                 '</div>';
        //             break;

        //         case 'sc':
        //                 html += '<h5><br> Single Select Question </h5>';
        //                 html += questions;
        //                 html += 
        //                     '<div>'+
        //                         '<label for="s_question_options" class="col-form-label">Add (,) Separated English options  <strong style="color:  red">*</strong></label>'+
        //                         '<textarea onkeyup="$(\'#s_question_options_fr\').val($(this).val())" id="s_question_options" type="text"  class="form-control" name="s_question_options"></textarea>'+

        //                         '<label for="s_question_options_fr" class="col-form-label ">Add (,) Separated French options  <strong style="color:  red">*</strong></label>'+
        //                         '<textarea type="text"  class="form-control"  id="s_question_options_fr" name="s_question_options_fr"></textarea>'+
        //                     '</div>';
        //                 break;
                
        //         case 'qa':
        //             html += '<h5><br> Text Question </h5>';
        //             html += questions;
        //             break;
        //         case 'im':
        //             html += '<h5><br> Attachment Upload Question </h5><hr>';
        //             html += questions;
        //             html += 
        //                 '<div class="w-100">'+
        //                     '<div class="col-md-12 p-0">'+
        //                         '<label for="attachments" class="form-label">Select Attachment:</label>'+
        //                         '<div class="form-label">'+
        //                             '<input  type="checkbox" name="attachment" value="1">'+
        //                             '<label  for="inlineRadio1">&nbsp;IMAGE &nbsp;&nbsp;</label>'+
        //                             '<input  type="checkbox" name="attachment" value="2">'+
        //                             '<label  for="inlineRadio1">&nbsp;WORD DOCs&nbsp;&nbsp;</label>'+
        //                             '<input  type="checkbox" name="attachment" value="3">'+
        //                             '<label  for="inlineRadio1">&nbsp;PDF &nbsp;&nbsp;</label>'+
        //                             '<input  type="checkbox" name="attachment" value="4">'+
        //                             '<label  for="inlineRadio1">&nbsp;EXCEL &nbsp;&nbsp;</label>'+
        //                             '<input  type="checkbox" name="attachment" value="5">'+
        //                             '<label  for="inlineRadio1">&nbsp;ZIP</label>'+
        //                         '</div>'+
        //                     '</div>'+
        //                 '</div>';
        //             break;
        //         case 'dc':
        //             html += '<h5><br> Dynamic Controlled Question </h5><hr>';
        //             html += questions;
        //             html +=
        //                 '<div class="form-group">'+
        //                     '<label for="dc_question_options" class="col-form-label">Dynamic Items From</label>'+
        //                     '<select type="text" class="form-control" name="dropdown_value_from" id="dropdown_value_from">'+
        //                         '<option value="1">DATA-ELEMENTS-OF-ORGANIZATION-WITH-SECTIONS</option>'+
        //                         '<option value="2">ASSETS-OF-ORGANIZATION</option>'+
        //                         '<option value="3">COUNTRIES</option>'+
        //                         '<option value="4">TYPE OF DATA CLASSIFICATION</option>'+
        //                         '<option value="5">ASSETS COMBINED IMPACT</option>'+
        //                         '<option value="6">ASSETS TIER</option>'+
        //                     '</select>'+
        //                 '</div>';	
        //             break;
        //     }

        //     html += 
        //         '<div class="pb-3">'+
        //             '<label for="question_comment" class="col-form-label">Add English Question Comment (Optional)</label>'+
        //             '<textarea type="text" id="question_comment" class="form-control" name="s_question_coment" onkeyup="$(\'#question_comment_fr\').val($(this).val())" ></textarea>'+
        //             '<label for="question_comment_fr" class="col-form-label">Add French Question Comment (Optional)</label>'+
        //             '<textarea type="text" id="question_comment_fr"  class="form-control" name="s_question_coment_fr" ></textarea>';
        //     if (type != "im") {
                
        //         html +='<div class="d-flex pt-3">'+
        //                     '<input type="checkbox" onclick="add_attachment_box(event, 1)" value="false"  name="add_attachments_box">&nbsp;&nbsp; Allow Attachments &nbsp;&nbsp;'+
        //                 '</div>'+
        //                 '<div class="options"></div>'+
        //             '</div>';

        //     }else{

        //         html += '</div>';
        //     }
                            
        //     $("#render_special_question").html(html);
        // }

        // function add_multi_question(event){
        //     event.preventDefault();
        //     add_question_array();
        //     const parent = {
        //         group_id        : $("#group_id").val(),
        //         title           : $('#p_question_title').val(),
        //         title_fr        : $('#p_question_title_fr').val(),
        //         short_title     : $('#p_question_short_title').val(),
        //         short_title_fr  : $('#p_question_short_title_fr').val(),
        //         type            : $('#p_type').val()
        //     }
        //     const formdata = {
        //         parent_question     :parent,
        //         children_question   : child_questions
        //     }
        //     console.log("formdata", formdata);
        //     $.ajaxSetup({
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         }
        //     });
        //     $.ajax({
        //         url: ',
        //         type:'POST',
        //         data: {
        //             parent_question: parent,
        //             children_question: child_questions
        //         },
        //         success: function (res) {
        //             if (!res.status) {
        //                 swal('', res.error, 'warning');
        //             }else{
        //                 $('#addQuestionModel').modal('hide');
        //                 console.log();
        //                 swal('', res.success, 'success');
        //                 setTimeout(() => {
        //                     location.reload();
        //                 }, 500);
        //             }
        //         }
        //     });
        // }

        function save_section(event) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '{{route("add_section_to_group")}}',
                type:'POST',
                data: {
                    group_id            : window.location.pathname.split("/").pop(),
                    section_title       : $("#section_title").val(),
                    section_title_fr    : $("#section_title_fr").val()
                },
                success: function (res) {
                    if (!res.status) {
                        swal('', res.error, 'warning');
                    }else{
                        $('#addQuestionModel').modal('hide');
                        console.log();
                        swal('', res.success, 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 500);
                    }
                }
            });
        }

        function edit_section_title(id, title, title_fr) {  
            $(`#e_section_title`).val(title)
            $(`#e_section_title_fr`).val(title_fr)
            $(`#e_section_id`).val(id)

            $('#edit_section_modal').modal('show');
        }
        function update_section(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '{{route("update_section_to_group")}}',
                type:'POST',
                data: {
                    section_title       : $(`#e_section_title`).val(),
                    section_title_fr    : $(`#e_section_title_fr`).val(),
                    section_id          : $(`#e_section_id`).val()
                },
                success: function (res) {
                    if (!res.status) {
                        swal('', res.error, 'warning');
                    }else{
                        $('#addQuestionModel').modal('hide');
                        console.log();
                        swal('', res.success, 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 500);
                    }
                }
            });
        }

    </script>
@endpush