

  <style>
        .set_heading {
      text-align: center;
      font-size: 15px;
      color: gray;
    }
    .table-responsive {
      background: #fff;
    }
    .set_heading {
        border-right: 9px solid #fff !important;
    }
    .table td, .table th {
    /*border-left: 9px solid #fff !important;*/
    /*border: none !important;*/
    border-bottom: 0 !important;
    /*border-bottom-color: rgba(110, 111, 115, 0.51);
    border-bottom-style: solid;
    border-bottom-width: 3px;*/
    }
    .table td, .table th {
      border-top:  0 !important;
    }
    .set_heading , .set_bg {
        background: #dfdfdfb3;
        border-radius: 11px;
        /*color: #fff;*/
    }
    .styling {
      font-size: 18px !important;
      color: #1cc88a !important;
    }
    .table-bordered thead td, .table-bordered thead th {
    border-bottom-width: 2px;
    border-radius: 14px;
    filter: drop-shadow;
    font-size: 15px;
    }
    .table thead th {
        vertical-align: bottom;
        font-size: 16px;
    }
    .table-responsive {
        min-height: 200px;
      }
      .add_color {
        color: #1cc88a;
      }
      .coloring span {
         color: #1cc88a;
         font-weight: 600;
      }
      .table-sm {
        font-size: 14px;
      }
      .print_exp {
        margin-bottom: .5rem;
      }
  </style>

 <table class="table table table-responsive-sm text-center" id="tab">
        <thead>
          
          <tr>
             @if(count($option_questions))
                 <th class="set_heading fixed">  {{ __('User') }} </th> 
             @endif
          
          @foreach($option_questions as $quest_heading)
          <th class="set_heading" colspan="{{$quest_heading['op_count']}}" >


      
            @if(session('locale') == 'fr')
               @if($quest_heading['question_string_fr'] != null)
               {{$quest_heading['question_string_fr']}}
               @else
               {{$quest_heading['question_string']}}
               @endif
            @else
                {{$quest_heading['question_string']}}
            @endif


        </th>
          @endforeach
          </tr>
         
        </thead>
        <tbody>
          @foreach($data as $responses)
          <!-- user -->
         
                           <tr>
<td class="add_color table-sm fixed">{{$responses['email']}}
@if(session('locale') == 'fr')
({{$responses['sub_form_title_fr']?$responses['sub_form_title_fr']:$responses['sub_form_title']}})
@else
( {{$responses['sub_form_title']}})
@endif
</td>

@if(session('locale') == 'fr' && $final_fr != null)
@foreach($final_fr as $options)
<!-- options -->
<?php
$flag = false;
?>
@foreach($responses['response_fr'] as $res )
@if( trim($res) == trim($options) )
<td> <i class="fa fa-check-circle styling"></i> </td>
<?php
$flag = true;
break;
?>
@endif
@endforeach
<?php
if($flag == false){
?>
<td class="blank">-</td>
<?php
}
?>
@endforeach
@else
@foreach($final as $options)
<!-- options -->
<?php
$flag = false;
?>
@foreach($responses['response'] as $res )
@if( trim($res) == trim($options) )
<td> <i class="fa fa-check-circle styling"></i> </td>
<?php
$flag = true;
break;
?>
@endif
@endforeach
<?php
if($flag == false){
?>
<td class="blank">-</td>
<?php
}
?>
@endforeach
@endif

</tr>
                           @endforeach
                 




          


        </tbody>


      </table>