@extends('parent', ['page_title' => $title, 'page_heading' => $heading])

@section('user_form')

<form method="POST" action="{{ route('submit_form') }}">
    {{ csrf_field() }}

  @if (!empty($questions))
  <input type="hidden" name="form-id"      value="{{ $questions[0]->uf_id }}">
  <input type="hidden" name="form-link-id" value="{{ $questions[0]->form_link_id }}">
  <input type="hidden" name="user-id"      value="{{ $questions[0]->u_id }}">
  <input type="hidden" name="email"        value="{{ $user_info->email }}">
  @foreach ($questions as $key => $question)
  <div>
      <b>{{ $question->question }}</b>
      @switch($question->type)
          @case('sc')
              <?php $radio_options = explode(', ', $question->options); ?>
              @if (!empty($radio_options))
                @foreach ($radio_options as $option)
                  <input type="radio" name="{{ $question->form_key.'_'.$question->q_id }}"  value="{{ $option }}">{{ $option}}
                @endforeach
              @endif
          @break
          @case('mc')
          <?php $chkbox_options = explode(', ', $question->options); ?>
          @if (!empty($chkbox_options))
            @foreach ($chkbox_options as $option)
              <input type="checkbox" name="{{ $question->form_key.'_'.$question->q_id }}[]" value="{{ $option }}">{{ $option}}
            @endforeach
          @endif
          @break
          @case('bl')
            <br>
            <input type="text" name="{{ $question->form_key.'_'.$question->q_id }}"  ></input>
          @break
          @case('qa')
            <br>
            <textarea name="{{ $question->form_key.'_'.$question->q_id }}"  rows="4" cols="50"></textarea>
          @break

          @case('blanks')
          @break
        Default case...
@endswitch
  </div>

  @endforeach
  @endif
  <br>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
