@extends('admin.client.client_app')
@section('page_title')
{{ __('DATA INVENTORY') }}
@endsection
@section('content')
  <link href="https://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">
  <script src="https://www.jqueryscript.net/demo/jQuery-Plugin-To-Export-Table-Data-To-CSV-File-table2csv/src/table2csv.js"></script>
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

  <div class="export_btn" style="    margin-left: 40px; margin-bottom: 1rem;"> 
    <a href="{{url('report_export/2')}}" class="btn btn-sm btn-primary"><i class="fa fa-arrow-circle-down" aria-hidden="true"></i> {{ __('Export') }}</a>
  </div>

  <div class="card" style="border-radius: 30px;;margin-left: 30px; margin-right: 30px">
    <div class="card-body">
      <div class="table-responsive">
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
            <tr>
              <td class="coloring fixed"> <!-- <span> User </span> |  Option --> </td>
              @if(session('locale') == 'fr' && $final_fr != null)
                @foreach($final_fr as $options)   
                  <td  id="{{$options}}" class="table-sm">{{$options}}</td>        
                @endforeach
              @elseif(session('locale') == 'en' && $final != null)
                @foreach($final as $options)   
                  <td  id="{{$options}}" class="table-sm">{{$options}}</td>        
                @endforeach
              @endif
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
                    <?php $flag = false; ?>
                    @foreach($responses['response_fr'] as $res )
                      @if( trim($res) == trim($options) )
                        <td>
                          <i class="fa fa-check-circle styling"></i> 
                        </td>
                        <?php $flag = true;  break;?>
                      @endif
                    @endforeach
                      <?php if($flag == false){ ?>
                        <td class="blank">-</td>
                        <?php
                        }
                      ?>
                  @endforeach
                @else
                  @foreach($final as $options)
                    <!-- options -->
                    <?php $flag = false; ?>
                    @foreach($responses['response'] as $res )
                      @if( trim($res) == trim($options) )
                        <td>  <i class="fa fa-check-circle styling"></i> </td>
                        <?php $flag = true; break; ?>
                      @endif
                    @endforeach
                    <?php if($flag == false){ ?> 
                      <td class="blank">-</td>
                    <?php } ?>
                  @endforeach
                @endif
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <script>
    /* global $ */
    $("#dl").click(function(){
      $("#tab").table2csv('output', {appendTo: '#out'});
      $("#tab").table2csv();
    })
  </script>
@endsection