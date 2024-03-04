
@extends('admin.client.client_app')

@section('page_title')
  {{ __('GLOBAL DATA INVENTORY') }}
@endsection

@section('content')
  <style>
    .set_heading {
      min-width: 10% !important;
    max-width: 152px;
    width: 65%; 
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
        /*color: #fff;*/
    }
    .styling {
      font-size: 14px !important;
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
      table {
        white-space: initial !important;
      }
      .table-sm {
        font-size: 16px;
      }
      .check {
        text-align: right;
    width: 50%;
      }
      .check i {
        font-size: 21px !important;
      }
  </style>   
  <div class="export_btn" style="margin-left: 40px; margin-bottom: 1rem;"> 
    <a href="{{url('report_export/1')}}" class="btn btn-sm btn-primary"><i class="fa fa-arrow-circle-down" aria-hidden="true"></i> {{ __('Export') }}</a>
  </div>
  <section class="assets_list">
    <div class="main_custom_table">
      <div class="main_table_redisign main_inv_not">
        <?php  $array_index=0; ?>
        @foreach($option_questions as $key => $row)
        <table class="table table-striped border-0 detail_inv_page">
          <thead>
            <th colspan="2">
              @if(session('locale') == 'fr') 
                @if(DB::table('questions')->where('question' , $row['question_string'])->pluck('question_fr')->first() != null)
                  {{ DB::table('questions')->where('question' , $row['question_string'])->pluck('question_fr')->first() }}
                @else
                  {{ $row['question_string'] }}
                @endif

              @else 
                {{ $row['question_string'] }}
              @endif    
            </th>
          </thead>
          <tbody>
            <?php 
              if($key == 0){ 
                $x = 0;
              }
              for($i = 0 ; $i <$option_questions[$key]['op_count']; $i++) { ?>
                    <tr>
                      <td class="green_td_glb">
                        @if(session('locale') == 'fr')   
                          {{$final_fr[$array_index]}}
                        @else
                          {{$final[$array_index]}}
                        @endif
                      </td>
                      <td class="check_icon text-right">
                        <i class='bx bx-check'></i>
                      </td>
                    </tr>
                  <?php  
                $array_index++; 
              } 
            ?>
          </tbody>
        </table>
        @endforeach
      </div>
    </div>
  </section>
@endsection