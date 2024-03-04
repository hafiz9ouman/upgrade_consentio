@extends(($user_type=='admin')?('admin.layouts.admin_app'):('admin.client.client_app'))
@section('content')
<style>
    .row-btn {
        margin-bottom:10px;
        display:flex;
        flex-direction:row;
        justify-content: flex-end;
    }
    .expired {
        color:#d73b3b;
    }
    #forms-list_wrapper {
        white-space: nowrap;
        padding-top: 15px;
    }
    .align_button {
        display: flex;
        justify-content: space-between;
    }
</style>
 <link href="{{ url('frontend/css/jquery.mswitch.css')}}"  rel="stylesheet" type="text/css">
{{-- <div class="container-fluid">
  <div class="align_button">    
     @if(!isset($all))
    <h3 class="tile-title">User Forms {{app('request')->input('ext_user_only')?'(External Users Only)':'(Internal and External Users)'}}</h3>
    @endif
    
    @if(!isset($all))
    <div class="row-btn">
        <button class="btn btn-primary" data-toggle="modal" data-target="#myModal">Send Link to External Users</button>
    </div>
    @endif
    </div>
</div>  --}}


    <section class="assets_list">
      <div class="main_custom_table">
        <div class="table_filter_section">
          <div class="select_tbl_filter">
            <div class="main_filter_tbl">
              <p>{{ __('Show') }}</p>
              <select>
                <option>10</option>
                <option>20</option>
                <option>30</option>
              </select>
              <p>{{ __('Entries') }}</p>
            </div>
          </div>
        </div>
        <div class="main_table_redisign">

          @section('page_title')
          {{-- <div class="table_breadcrumb"> --}}
            {{-- <h3> --}}
              {{ __('ACTIVITIES LIST') }}
            {{-- </h3> --}}
          @endsection
          {{-- <div class="table_breadcrumb">
            <h3>GENERATED FORMS</h3>
          </div> --}}
<?php
?>
          <div class="over_main_div">
            <table class="table table-striped text-center paginated" >
            <thead>
            <tr style = "text-transform:uppercase;">
                           <th> {{ __('Activity_Response') }}</th>
                           <th> {{ __('User Email') }} <strong>/</strong> {{ __('Name') }} </th>
                           <th>{{ __('User Type') }}</th>
                           <th> {{ __('Form Completion Date') }} </th>
            </tr>
        </thead>
        <tbody>
          <?php
          ?>

             @foreach($filled_questions as $fq)
                    <tr>
                           <td><a href="{{ $fq->form_link }}" target="_blank" > {{$fq->question_response}}</a></td>
                           <td > {{$fq->user_email}}</td>  
                           <td>  {{ $fq->form_type }} {{ __('user') }} </td>
                           <td> {{date('d', strtotime($fq->created))}} {{date(' F', strtotime($fq->created))}} {{date('Y  H:i', strtotime($fq->created))}} </td>
                        
                    </tr>
              @endforeach
        </tbody>
            </table>
            <div class="table_footer">
              <p>{{ __('Showing 1 to 9 of 9 entries') }}</p>
              <div class="table_custom_pagination">
                <p class="active_pagination">1</p>
                <p>2</p>
                <p>3</p>
              </div>
            </div>
        </div>
        </div>
      </div>
    </section>



<!-- </div> -->
<script src="{{url('frontend/js/jquery.mswitch.js')}}"></script>
<script>
    $(document).ready(() => {
        $('#activity-table').DataTable({dom: 'Bfrtip',
            buttons: [
                'copy', 'excel', 'pdf'
        ]});
    })
</script>
@endsection