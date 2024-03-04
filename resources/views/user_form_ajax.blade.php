@extends('admin.client.client_app')
<link rel="stylesheet" type="text/css" href="{{ url('backend/css/jquery.datetimepicker.css') }}" />

@section('content')

<style>

.section-heading {

    margin-bottom:10px;

}

</style>


<h1><?php if (isset(($questions[0]))) echo $questions[0]->title; ?></h1>

<p><?php if (isset($questions[0]) && !empty($questions[0]->description)) echo $questions[0]->description; ?></p>

<form method="POST" id="user-form" action="">

        {{ csrf_field() }}

    @if (!empty($questions))

        @if (isset($questions[0]))

    <input type="hidden" name="user-form-id"  value="{{ $questions[0]->uf_id }}">
    <input type="hidden" name="form-id"       value="{{ $questions[0]->form_id }}">
    <input type="hidden" name="form-link-id"  value="{{ $questions[0]->form_link_id }}">
    <input type="hidden" name="user-id"       value="{{ $questions[0]->user_id }}">

        @endif

    <input type="hidden" name="subform-id"    value="{{ $questions[0]->sub_form_id }}">


    <?php $heading_recs = []; ?>

    @foreach ($questions as $key => $question)

    <?php
        $sec_id          = $question->afs_sec_id;
        $heading         = $question->admin_sec_title;

            //echo "<h3>client title ".$question->client_sec_title."</h3><br>";

        if ((isset($question->client_sec_title) && !empty($question->client_sec_title))) {
            $sec_id          = $question->cfs_sec_id;
            $heading         = $question->client_sec_title;
        }

        //if ($question->question_section != null && !in_array($question->question_section, $heading_recs)):
            //$heading_recs[] = $question->question_section;
        if ($heading != null && !in_array($heading, $heading_recs)):
            $heading_recs[] = $heading;
    ?>
    <div class="section-heading">
        <h3>{{ $heading }}</h3>
    </div>
    <?php
        endif;
    ?>

    <div class="form-group">

        <label><h5>{{ $question->question_num.' '.$question->question }}</h5></label>

        @if ($question->question_comment != null && $question->question_comment != '')

        <p>

            {{ $question->question_comment }}

        </p>

        @endif

        @switch($question->type)



        @case('sc')

            <?php $radio_options = explode(', ', $question->options); ?>

            @if (!empty($radio_options))

                @foreach ($radio_options as $option)

                <div class="form-group form-check">

                    <label class="form-check-label">
                        <input type="radio" name="{{ $question->form_key.'_'.$question->q_id }}"  value="{{ $option }}" <?php if (isset($filled[$question->form_key]) && trim($filled[$question->form_key]['question_response']) == trim($option)) echo 'checked'; ?>>
                        @if ($option == 'Date Picker Option')
                        <input type="text" name="d-{{ $question->q_id }}" id="date-picker" value="<?php if (isset($filled[$question->form_key]) && ($filled[$question->form_key]['additional_resp'])) echo $filled[$question->form_key]['additional_resp']; ?>" />
                        @else
                        {{ $option }}
                        @endif
                        <br><br>

                    </label>

                </div>

                @endforeach

            @endif

        @break



        @case('mc')

            <?php $chkbox_options = explode(', ', $question->options); ?>

            @if (!empty($chkbox_options))

                @foreach ($chkbox_options as $option)

                <div class="form-group form-check">

                    <label class="form-check-label">

                    <input type="checkbox" name="{{ $question->form_key.'_'.$question->q_id }}[]" value="{{ $option }}" <?php if (isset($filled[$question->form_key]) && in_array($option, $filled[$question->form_key]['question_response'])) echo 'checked'; ?>> {{ $option}}<br><br>

                    </label>

                </div>

                @endforeach

            @endif

        @break



        @case('bl')

            <div class="form-group">

                <label></label>

                <input type="text" name="{{ $question->form_key.'_'.$question->q_id }}"  value="<?php echo (isset($filled[$question->form_key]))?($filled[$question->form_key]['question_response']):('') ?>"><br><br>

            </div>



        @break



        @case('qa')

            <!--

            <br>

            <textarea name="{{ $question->form_key }}"  rows="4" cols="50" disabled></textarea><br><br>

            -->

            <div>

                        <textarea name="{{ $question->form_key.'_'.$question->q_id }}"  rows="4" cols="50"><?php echo (isset($filled[$question->form_key]))?($filled[$question->form_key]['question_response']):('') ?></textarea><br><br>

            </div>

        @break



       @case('blanks')

       @break





                Default case...

@endswitch

    @if ($question->additional_comments)

        <div>

        <div>

        <label>Additional comments below:</label>

        </div>

                <input type="text" name="c-{{ $question->q_id.':'.str_replace('q-','', $question->form_key) }}" value="<?php echo (isset($filled[$question->form_key]))?($filled[$question->form_key]['question_comment']):('') ?>"><br><br>

        </div>



    @endif

    </div>



    @endforeach

    @endif

    <br>

<!--
    <button type="submit" class="btn btn-primary">Submit</button>
-->
</form>
<script src="{{ url('backend/js/jquery.datetimepicker.js') }}"></script>
<script>

    $(document).ready(function(){


        jQuery('#date-picker').datetimepicker({
            timepicker:false,
            format:'Y-m-d'
        });




        $('input, textarea').change(function(){

            //var $inputs = $('#user-form :input');

            var type         = $(this).attr('type');

            var form_id      = $('input[name="form-id"]').val();

            var user_form_id = $('input[name="user-form-id"]').val();

            var form_link_id = $('input[name="form-link-id"]').val();

            var email        = $('input[name="email"]').val();

            var user_id      = $('input[name="user-id"]').val();

            var question_key = $(this).attr('name');

            var post_data        = {};

            post_data['_token']  = '{{csrf_token()}}';

            post_data['form-id'] = form_id;

            post_data['user-form-id'] = user_form_id;

            post_data['user-id'] = user_id;

            post_data['form-link-id'] = form_link_id;

            post_data['email'] = email;

            //console.log($('input[name="'+question_key+'"]'));

            if (type == 'checkbox') {

                var vals = [];

                $('input[name="'+question_key+'"]').each(function(){

                    console.log($(this).prop('checked'));

                    if ($(this).prop('checked')) {

                        vals.push($(this).val());

                    }
                    else {
                        var index = vals.indexOf($(this).val());

                        if (index > -1) {
                            vals.splice(index, 1);
                        }
                    }

                    post_data[question_key] = vals;

                    //$('input[name="'+question_key+'"]')

                });

            }
            else {
                post_data[question_key] = $(this).val();
            }

            //console.log(post_data);

            //return;

            $.ajax({

                url:'{{route('ajax_int_user_submit_form')}}',

                method:'POST',

                /*

                headers: {

                    'X-CSRF-TOKEN': $( 'meta[name="csrf-token"]' ).attr( 'content' )

                },

                */

                data: post_data,

                success: function(response) {

                    console.log(response);

                }

            });


        });

    });

</script>






@endsection

