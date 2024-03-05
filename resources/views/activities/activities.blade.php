@extends(($user_type=='admin')?('admin.layouts.admin_app'):('admin.client.client_app'))
@section('content')

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


    <section class="section dashboard">
      <div class="row">
        <div class="col-12">
          <div class="card">

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
          <div class="card-table">
            <table class="table fixed_header manage-assessments-table" id="datatable">
            <thead>
            <tr style = "text-transform:uppercase;">
                           <th style="vertical-align: middle;"> {{ __('Activity_Response') }}</th>
                           <th style="vertical-align: middle;"> {{ __('User Email') }} <strong>/</strong> {{ __('Name') }} </th>
                           <th style="vertical-align: middle;">{{ __('User Type') }}</th>
                           <th style="vertical-align: middle;"> {{ __('Form Completion Date') }} </th>
            </tr>
        </thead>
        <tbody>
          <?php
          ?>

             @foreach($filled_questions as $fq)
                    <tr>
                           <td><a href="{{ $fq->form_link }}" target="_blank" > {{$fq->question_response}}</a></td>
                           <td > {{$fq->user_email}}</td>  
                           <td>  {{ __($fq->form_type) }}</td>
                           <td> {{date('d', strtotime($fq->created))}} {{date(' F', strtotime($fq->created))}} {{date('Y  H:i', strtotime($fq->created))}} </td>
                        
                    </tr>
              @endforeach
        </tbody>
            </table>
            <!-- <div class="table_footer">
              <p>{{ __('Showing 1 to 9 of 9 entries') }}</p>
              <div class="table_custom_pagination">
                <p class="active_pagination">1</p>
                <p>2</p>
                <p>3</p>
              </div>
            </div> -->
        </div>
        </div>
      </div>
    </section>



<!-- </div> -->
<script src="{{url('frontend/js/jquery.mswitch.js')}}"></script>
<script>
    $(document).ready(function() {
      $('#datatable').DataTable({
        "order": []
      });
    });
</script>
@endsection