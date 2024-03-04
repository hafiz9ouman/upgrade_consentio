@extends('admin.client.client_app')
@section('content')

<?php
    // echo "<pre>";
    // print_r($filled);
    // echo "</pre>";
    // exit;
?>
<div style="margin-left:30px;">
<form method="POST" action="">
    {{ csrf_field() }}

  @if (!empty($questions))
  @foreach ($questions as $key => $question)
  <div>
      <b>{{ $question->question }}</b><br><br>
      @switch($question->type)
          @case('sc')
              <?php $radio_options = explode(', ', $question->options); ?>
              @if (!empty($radio_options))
                @foreach ($radio_options as $option)
                  <input type="radio" name="{{ $question->form_key }}"  value="{{ $option }}" disabled> {{ $option}}<br><br>
                @endforeach
              @endif
          @break
          @case('mc')
          <?php $chkbox_options = explode(', ', $question->options); ?>
          @if (!empty($chkbox_options))
            @foreach ($chkbox_options as $option)
              <input type="checkbox" name="{{ $question->form_key }}[]" value="{{ $option }}" disabled> {{ $option}}<br><br>
            @endforeach
          @endif
          @break
          @case('bl')
            <br>
            <input type="text" name="{{ $question->form_key }}"  value="" disabled></input><br><br>
          @break
          @case('qa')
            <br>
            <textarea name="{{ $question->form_key }}"  rows="4" cols="50" disabled></textarea><br><br>
          @break

          @case('blanks')
          @break
        Default case...
@endswitch
@if ($question->additional_comments)
        <div>Additional Comments</div>
        <input type="text" name="c" value="" disabled><br><br>
@endif
  </div>

  @endforeach
  @endif
  <br>
</form>
</div>


@endsection
