@extends('users.ex_user_app', ['load_admin_css' => true])
@section('content')

<h1>@if (!empty($questions)) {{ $questions[0]->title }} @endif</h1>
<p>@if (!empty($questions) && !empty($questions[0]->description)) {{ $questions[0]->description }} @endif</p>

<form method="POST" action="{{ route('submit_form') }}">
    {{ csrf_field() }}
<?php


?>
  @if (!empty($questions))
  @if (isset($questions[0]))
  <input type="hidden" name="form-id"       value="{{ $questions[0]->uf_id }}">
  <input type="hidden" name="form-link-id"  value="{{ $questions[0]->form_link_id }}">  
  @endif
  <input type="hidden" name="subform-id"    value="{{ $questions[0]->sub_form_id }}">


  @foreach ($questions as $key => $question)
  <div>
      <b>{{ $question->question }}</b><br><br>
      @switch($question->type)
          @case('sc')
              <?php $radio_options = explode(', ', $question->options); ?>
              @if (!empty($radio_options))
                @foreach ($radio_options as $option)
                  <input type="radio" name="{{ $question->form_key.'_'.$question->q_id }}"  value="{{ $option }}" <?php if (isset($filled[$question->form_key]) && $filled[$question->form_key]['question_response'] == $option) echo 'checked'; ?>> {{ $option}}<br><br>
                @endforeach
              @endif
          @break
          @case('mc')
          <?php $chkbox_options = explode(', ', $question->options); ?>
          @if (!empty($chkbox_options))
            @foreach ($chkbox_options as $option)
              <input type="checkbox" name="{{ $question->form_key.'_'.$question->q_id }}[]" value="{{ $option }}" <?php if (isset($filled[$question->form_key]) && in_array($option, $filled[$question->form_key]['question_response'])) echo 'checked'; ?>> {{ $option}}<br><br>
            @endforeach
          @endif
          @break
          @case('bl')
            <br>
            <input type="text" name="{{ $question->form_key.'_'.$question->q_id }}"  value="<?php (isset($filled[$question->form_key]))?($filled[$question->form_key]):('') ?>"</input><br><br>
          @break
          @case('qa')
            <br>
            <textarea name="{{ $question->form_key.'_'.$question->q_id }}"  rows="4" cols="50"><?php (isset($filled[$question->form_key]))?($filled[$question->form_key]):('') ?></textarea><br><br>
          @break

          @case('blanks')
          @break
        Default case...
@endswitch
@if ($question->additional_comments)
        <div>Additional Comments</div>
        <input type="text" name="c-{{ $question->q_id }}" value="<?php echo (isset($filled[$question->form_key]))?($filled[$question->form_key]['question_comment']):('') ?>"><br><br>
@endif
  </div>

  @endforeach
  @endif
  <br>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>



@endsection
