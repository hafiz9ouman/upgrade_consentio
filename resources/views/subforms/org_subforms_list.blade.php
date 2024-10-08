@extends(($user_type=='admin')?('admin.layouts.admin_app'):('admin.client.client_app'))
@section('content')
@if(!isset($all))
@section('page_title')
  {{ __('ASSESSMENT FORM ASSIGNEES') }}
@endsection
@else
@section('page_title')
  {{ __('PENDING FORMS') }}
@endsection
@endif
  <link href="{{ url('frontend/css/jquery.mswitch.css')}}"  rel="stylesheet" type="text/css">
  
  <section class="assets_list">
    <div class="row">
      <div class="col-12">
      <div class="align_button">    
    @if(!isset($all))
      <div class="row-btn">
          <button class="buton mb-2" data-toggle="modal" data-target="#myModal">{{ __('Send Link to External Users') }}</button>
      </div>
    @endif
  </div>
      </div>
    </div>

  <div class="row">
    <div class="col-12">
      <div class="card">
      @if(!isset($all))
        <div class="table_breadcrumb">
          <h3 class="tile-title">{{ __('User Forms') }} {{app('request')->input('ext_user_only')? __('(External Users Only)'): __('(Internal and External Users)')}}</h3>
        </div> 
      @endif
      <div class="card-table">
        <table id="datatable" class="table fixed_header manage-assessments-table" >
          <thead>
            <tr style = "text-transform:uppercase !important;">
              <th style="vertical-align: middle;" scope="col">{{ __('FORM LINKS') }}</th>
              <th style="vertical-align: middle;" scope="col">{{ __('EMAIL') }}</th>
              <th style="vertical-align: middle;" scope="col">{{ __('USER TYPE') }}</th>
              <th style="vertical-align: middle;" scope="col">{{ __('SUBFORM NAME') }}</th>
              <th style="vertical-align: middle;" scope="col">{{ __('FORM NAME') }}</th>
              <th style="vertical-align: middle;" scope="col">{{ __('SENT DATE') }}</th>
              <!-- <th style="vertical-align: middle;" scope="col">{{ __('TOTAL DAYS') }}</th> -->
              <th style="vertical-align: middle;" scope="col">{{ __('REMAINING DAYS') }}</th>
              <th style="vertical-align: middle;" scope="col">{{ __('EXPIRY DATE') }}</th>
              <th style="vertical-align: middle;" scope="col">{{ __('SUBMISSION STATUS') }}</th>
              @if(auth()->user()->role == 2)
              <th style="vertical-align: middle;" scope="col">{{ __('LOCK/UNLOCK') }}</th>
              @endif
              <!-- <th style="vertical-align: middle;" scope="col">{{ __('CHANGE ACCESS') }}</th> -->
            </tr>
          </thead>
          <tbody>
            <?php 
              $i = 0; 
              $form_link = ''; 
              $forms = 'Forms';
              if (Request::segment(1) =='SAR'){
                $forms = 'SAR';
              }
            ?>
            @if($form_user_list)
              <?php 
                foreach ($form_user_list as $key=>$form_info):
                  if(isset($form_info->internal)){
                    $form_link = $form_info->form_link_id;
                    $url = $forms.'/CompanyUserForm/'.$form_link;
                    $int_user = __('Organization User');
                    $user_type = '<span style="margin-right: 0px !important;color:#5bc858">'.$int_user.'</span>';
                    $lu_utype  = 'in';
                  }
                  if(isset($form_info->external)){
                    $form_link = $form_info->form_link;
                    $url = $forms.'/ExtUserForm/'.$form_link;
                    $ex_user = __('External User');
                    $user_type = '<span style="margin-right: 0px !important;color:#f88160">'.$ex_user.'</span>';
                    $lu_utype  = 'ex';
                      
                  }
                ?><?php
                  $now = time(); 
                  $expiry = strtotime($form_info->uf_expiry_time);
                  $datediff = $expiry - $now;
                  $rem_days  = round($datediff / (60 * 60 * 24));
                  $expired = '';
                  if ($rem_days < 0)
                    $expired = 'expired';
                ?>   
                @if(strtotime(date('Y-m-d')) > strtotime($form_info->uf_expiry_time))
                  @php
                    $exp = "btn-secondary";
                  @endphp
                @else
                  @php
                    $exp  = 'btn-primary';
                  @endphp
                @endif
                <tr>
                  <td style="color:#<?php echo ($form_info->is_locked)?('7bca94'):('f26924'); ?>">
                    <a type="button" class="btn {{$exp}} td_round_btn" href="{{ url($url) }}" target="_blank">{{ __('Open') }}</a>
                  </td>
                  <td>{{ isset($form_info->email)?($form_info->email):($form_info->user_email) }}</td>
                  <td>{!! $user_type !!}</td>
                  @php
                   $subform = DB::table('sub_forms')->where('id', $form_info->sub_form_id)->select('title', 'title_fr')->get();
                  @endphp
                  <td>  @if(session('locale') == 'fr' && $subform[0]->title_fr != null)
                    <?php echo $subform[0]->title_fr; ?>
                    @else
                    <?php echo $subform[0]->title; ?>
                    @endif
                  </td>
                  <td>
                    @if($form_info->title_fr != null && session('locale')=='fr')
                      {{ $form_info->title_fr }}
                    @elseif($form_info->title_fr == null && session('locale')=='fr')
                      {{ $form_info->title }}
                    @elseif (session('locale')=='en')
                      {{ $form_info->title }}
                    @endif
                  </td>
                  <td>{{ date('Y-m-d', strtotime($form_info->created)) }}</td>
                  <?php
                    $created = strtotime($form_info->uf_created);
                    $expiry = strtotime($form_info->uf_expiry_time);
                    $datediff = $expiry - $created;
                    $total_days  = round($datediff / (60 * 60 * 24));
                  ?>      
                  <!-- <td>{{ $total_days }}</td> -->
                  <td><span style="margin-right: 0px !important;" class="{{$expired}}">{{$rem_days }}</span></td>
                  <td>
                    @if(auth()->user()->role == 2)
                      @if(strtotime(date('Y-m-d')) > strtotime($form_info->uf_expiry_time))
                        <input type="button" class="btn btn-sm btn-primary"  onclick="extend_expire('{{$form_info->form_link}}','{{$form_info->form_link_id}}')" value="{{__('Extend Expiry')}}">
                      @else
                        {{ date('Y-m-d', strtotime($form_info->uf_expiry_time)) }}
                      @endif
                    @else
                      {{ date('Y-m-d', strtotime($form_info->uf_expiry_time)) }}
                    @endif
                  </td> 
                  <td>
                    <span style="margin-right: 0px !important;color:#<?php echo ($form_info->is_locked)?('7bca94'):('f26924'); ?>">@if(strtotime(date('Y-m-d')) > strtotime($form_info->uf_expiry_time)) {{__('Expired')}} @elseif($form_info->is_locked) {{__('Submitted')}} @elseif($form_info->is_temp_lock) {{__('Locked')}} @else {{__('Not Submitted')}} @endif</span>
                  </td>
                  @if(auth()->user()->role == 2)
                  <td>
                    <input type="button" class="btn btn-sm btn-<?php echo ($form_info->is_temp_lock == 1)?("success"):("danger") ?>"  onclick="temp_lock('{{$form_info->form_link}}','{{$form_info->form_link_id}}')" value="{{($form_info->is_temp_lock == 1)? __('Unlocked'): __('Locked')}}">
                  </td> 
                  @endif
                  <!-- <td>
                    <label class="switch switch-green">
                    <input type="button" class="btn btn-sm btn-<?php echo ($form_info->is_locked)?("danger"):("success") ?>"  onclick="lock_unlock('the_toggle_button-{{$key}}')" value="{{($form_info->is_locked)? __('Unlocked'): __('Locked')}}">
                      <span style="margin-right: 0px !important;" class="switch-label" data-toggle="tooltip" title="{{($form_info->is_locked)? __('Locked'): __('Unlocked')}}" data-on="{{ __('on') }}" data-off="{{ __('Off') }}"></span>
                      <span style="margin-right: 0px !important;" class="switch-handle" data-toggle="tooltip" title="{{($form_info->is_locked)? __('Locked'): __('Unlocked')}}"></span>
                    </label>
                  </td>
                  <div class="d-none"> <input style="display: none !important;" id="the_toggle_button-{{$key}}" type="checkbox"  title="{{($form_info->is_locked)? __('Locked'): __('Unlocked')}}" class="unlock-form " value="{{!$form_info->is_locked}}" u-type="{{$lu_utype}}" link="{{$form_link}}"></div> -->
                  
                  <!-- <td><button class="change-access btn-sm btn btn-<?php echo ($form_info->is_accessible)?("danger"):("success") ?>" type="{{$lu_utype}}" link="{{$form_link}}" action="<?php echo ($form_info->is_accessible)?(0):(1) ?>" ><?php echo ($form_info->is_accessible)? __("Remove"): __("Allow") ?></button></td> -->
                </tr>
                <?php $i++; ?>
              <?php endforeach; ?>
            @endif
          </tbody>
        </table>
      </div>
      </div>
    </div>
  </div>
  </section>


  <!-- Modal -->
  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="padding:10% 10%;">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="myModalLabel">{{ __('Separate emails with comma')}} (,) {{ __('or new line')}} ( {{ __('by pressing Enter key')}} ) {{ __('for multiple emails') }}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        </div>
        <div class="modal-body">
          <textarea id="email-list" class="form-control"></textarea>
        </div>
        <div class="modal-footer">
          <span id="wait-msg" class="text-primary" style="display:none">{{ __('Please wait while request is being processed') }}...</span>  
          <button type="button" id="send-email" class="btn btn-primary">
              <div class="spinner-border text-light" id="spinner" role="status" style="display:none">
                <span class="sr-only">{{ __('Loading') }}...</span>
              </div>
              <span id="send-email-span">{{ __('Send Email') }}</span>
          </button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
  <!-- </div> -->
  <script src="{{url('frontend/js/jquery.mswitch.js')}}"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    $(document).ready(function() {
      $('#datatable').DataTable({
        "order": []
      });
    });
</script>
  <script>
    function lock_unlock(val){
      console.log(val);
      Swal.fire({
            title: "{{__('Are you sure you want to Unlocked this Form?')}}",
            icon: "warning",
            showCancelButton: true, // This will automatically generate "Yes" and "No" buttons
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "{{__('Yes')}}",
            cancelButtonText: "{{__('No')}}"
        }).then((result) => {
            if (result.isConfirmed) {
                console.log("confirmed")
                $('#'+val).click();
            } else {
                console.log("not confirmed");
            }
        });
    }
    function extend_expire(ex_from, in_form) {
        console.log(ex_from);
        console.log(in_form);
        var post_data = {};

        post_data['_token'] = '{{csrf_token()}}';
        post_data['in_link'] = in_form;
        post_data['ex_link'] = ex_from;

        Swal.fire({
            title: "{{__('Are you sure you want to extend the expiration date?')}}",
            icon: "warning",
            showCancelButton: true, // This will automatically generate "Yes" and "No" buttons
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "{{__('Yes')}}",
            cancelButtonText: "{{__('No')}}"
        }).then((result) => {
            if (result.isConfirmed) {
                console.log("confirmed")
                $.ajax({
                    url: '{{ route('extend_expire') }}',
                    method: 'post',
                    data: post_data,
                    beforeSend: function () {

                    },
                    success: function (response) {
                        Swal.fire("{!! __('Expire Date Extended') !!}", response.msg, response.status);
                        setTimeout(function () {
                            location.reload();
                        }, 3000);
                    }
                });
            } else {
                console.log("not confirmed");
            }
        });
    }
    function temp_lock(ex_from, in_form) {
        console.log(ex_from);
        console.log(in_form);
        var post_data = {};

        post_data['_token'] = '{{csrf_token()}}';
        post_data['in_link'] = in_form;
        post_data['ex_link'] = ex_from;

        Swal.fire({
            title: "{{__('Are you sure you want to change Lock Status?')}}",
            icon: "warning",
            showCancelButton: true, // This will automatically generate "Yes" and "No" buttons
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "{{__('Yes')}}",
            cancelButtonText: "{{__('No')}}"
        }).then((result) => {
            if (result.isConfirmed) {
                console.log("confirmed")
                $.ajax({
                    url: '{{ route('temp_lock') }}',
                    method: 'post',
                    data: post_data,
                    beforeSend: function () {

                    },
                    success: function (response) {
                        Swal.fire("{!! __('Lock Status') !!}", response.msg, response.status);
                        setTimeout(function () {
                            location.reload();
                        }, 3000);
                    }
                });
            } else {
                console.log("not confirmed");
            }
        });
    }

    $(document).ready(function (){
      <?php
          $search_filter = '';
          $search = '';
          if (app('request')->input('search_filter')) 
          {
              $search_parameter = app('request')->input('search_filter');
              $search = '"search": {"search": "'.$search_parameter.'"},';
          }
      ?>

      $('[data-toggle="tooltip"]').tooltip();

      function changeFormLockStatus (elem, lockStatus){
        var post_data                 = {};

        post_data['_token']           = '{{csrf_token()}}';       
        post_data['action']           = elem.attr('action');
        post_data['user_type']        = elem.attr('u-type');
        post_data['link']             = elem.attr('link');
        post_data['lock_status']      = lockStatus;   
        console.log(post_data);
        $.ajax({
          url:'{{ route('unlock_form') }}',
          method:'post',
          data:post_data,
          beforeSend:function () {

          },
          data:post_data,
          success: function (response) {
            console.log(response);
              swal.fire("{!! __('Lock Status') !!}", response.msg, response.status);
              var color;
              var status;
              if (lockStatus) {
                  color  = '7bca94';
                  status = 'Submitted'; 
              }
              else {
                  color  = 'f26924';
                  status = 'Not Submitted';             
              }
              
              var new_lock_status_html = '<span style="color:#'+color+'">'+status+'</span>';
              elem.parent().parent().prev().html(new_lock_status_html);
              setTimeout( function () {
                      location.reload();
                    }, 4000 ); 
          }
        }); 
      }        
      
      $('#send-email').click(function(){
        var emails; 
        emails = $('#email-list').val();
        if (emails.trim() === '') {
            // Stop processing if the email list is empty
            console.log("Email list is empty. Stopping.");
            swal.fire("{!! __('Empty Field') !!}"," {!! __('Please Enter Email in Text Box') !!}", "error");
            return 0;
        }
        var new_line_match = /\r|\n/.exec(emails);
        if (new_line_match) {
          console.log('new line pattern');
          emails = emails.split('\n');
          for (i = 0; i < emails.length; i++) {
              var comma_emails = emails[i].split(',');
              if (comma_emails.length > 1) {
                  for (j = 0; j < comma_emails.length; j++) {
                      if (j) {
                          emails.splice(i,0,comma_emails[j]);
                      }
                      else {
                          emails.splice(i,1,comma_emails[j]);
                      }
                  }
              }
          }
        }
        else {
          emails = emails.split(',');
        }
        var emails = emails.filter(function(el) { return el; });
        for (i = 0; i < emails.length; i++) {
          var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
          if (re.test(String(emails[i].trim()).toLowerCase())) {
            console.log('t'); 
          }
          else {  
            console.log('invalid mail');
            swal.fire("{!! __('Invalid Email') !!}", emails[i]+" {!! __('is not valid email. Please enter email in correct format') !!}", "error");
            return 0;
          }
        }
        
        var post_data                 = {};
        
        post_data['_token']           = '{{csrf_token()}}';
        post_data['emails']           = emails;
        post_data['subform_id']       = {{ $subform_id }};
        post_data['client_id']        = {{ Auth::user()->client_id }}; 
        post_data['parent_form_id']   = {{ $parent_form_id }};
        
        
        $.ajax({
          url:'{{ route('assign_subforms_to_external_users') }}',
          method:'post',
          beforeSend:function () {
              $('#send-email').prop('disabled', true);
              $('#send-email-span').hide();
              $('#wait-msg').show();
              $('#spinner').show();
          },
          data:post_data,
          success: function (response) {
            //response = JSON.parse(response);
            //console.log(response);
            //console.log(response.msg);

            $('#send-email').prop('disabled', false);
              $('#send-email-span').show();
              $('#wait-msg').hide();
              $('#spinner').hide();       
                    $('#myModal').modal('hide')
            
            $('#act-msg').hide();
                    if (response.status == 'success') {
            swal.fire("{!! __('Sub-Form(s) Sent') !!}",  response.msg, response.status);
                setTimeout( function () {
                      location.reload();
                    }, 4000 );            
            }
            else if (response.status == 'fail') {
                response.status = 'error';
                swal.fire('Error', response.msg, response.status);
            }
            else {
                swal.fire('Error', "{!! __('Something went wrong. Please try again later') !!}", 'error');
            }
            


          }
        });
        
      });    
      
      $('.change-access').click(function(){  
        var post_data                 = {};
        post_data['_token']           = '{{csrf_token()}}';       
        post_data['action']           = $(this).attr('action');
        post_data['user_type']        = $(this).attr('type');
        post_data['link']             = $(this).attr('link');

        $.ajax({
          url:'{{ route('change_form_access') }}',
          method:'post',
          data:post_data,
          beforeSend:function () {

          },
          data:post_data,
          success: function (response) {
              
                    swal.fire({
                      title: "{!! __('Form Access Status Updated') !!}",
                      text: "{!! __('Form Access Changed!') !!}",
                      type: "info",
                      showCancelButton: false,
                      confirmButtonClass: "btn-primary",
                      confirmButtonText: "OK!",
                      closeOnConfirm: true,
                    },
                    function(){
                      //swal("Deleted!", "Your imaginary file has been deleted.", "success");
              location.reload();
                    });         
              
              
              
          }
        });       
      });

      $(".unlock-form:checkbox").mSwitch({
        onRender:function(elem){
          if (elem.val() == '1') {
            $.mSwitch.turnOn(elem);
          }
          else {
            $.mSwitch.turnOff(elem);
          }
        },
        onTurnOn:function(elem){
          changeFormLockStatus(elem,0);  
          elem.attr('data-original-title', 'Unlocked');
          elem.attr('data-on', 'on');
        },
        onTurnOff:function(elem){
          changeFormLockStatus(elem,1);
          elem.attr('data-off', 'off');
          elem.attr('data-original-title', 'Locked');
        }
      }); 
    });
  </script>
@endsection