@extends ('admin.client.client_app')
@section('page_title')
  {{ __('PENDING AUDITS') }}
@endsection
@section('content')
  <style>
    .table {
      margin-bottom: 3rem;
    }
  </style>
  <section class="assets_list">
    <div class="row">
      <div class="col-12">
        <div class="card">
        <div class="card-table">
          <table class="table fixed_header manage-assessments-table" id="datatable">
            <thead>
              <tr style = "text-transform:uppercase !important;">
                
                
                <!-- <th scope="col">{{ __('USER TYPE') }}</th> -->
                <th style="vertical-align: middle;" scope="col">{{ __('Audit Form Name') }}</th>
                <th style="vertical-align: middle;" scope="col">{{ __('Group Name') }}</th>
                <th style="vertical-align: middle;" scope="col">{{ __('Asset Number') }}</th>
                <th style="vertical-align: middle;" scope="col">{{ __('Asset Name') }}</th>
                <th style="vertical-align: middle;" scope="col">{{ __('ASSIGNED TO') }}</th>
                <th style="vertical-align: middle;" scope="col">{{ __('ACTION') }}</th>
                <th style="vertical-align: middle;" scope="col">{{ __('EXPIRY DATE') }}</th>
                <th style="vertical-align: middle;" scope="col">{{ __('SUBMISSION STATUS') }}</th>
                @if(Auth::user()->role == 2)
                <th style="vertical-align: middle;" scope="col">{{ __('LOCK/UNLOCK') }}</th>
                  <!-- <th scope="col" class="fs-12">{{ __('Total Organization Users of this subform') }}</th> -->
                  <!-- <th scope="col" class="fs-12">{{ __('Total External Users of this subform') }}</th> -->
                @endif
              </tr>
            </thead>
            <tbody>
              @foreach ($completed_forms as $key=>$form_info)
              @if($form_info->is_internal == 1)
                @php
                    $lu_utype  = 'in';
                @endphp
              @else
                @php
                    $lu_utype  = 'ex';
                @endphp
              @endif
              @php
              //dd($form_info);
              @endphp
              @if(strtotime(date('Y-m-d')) > strtotime($form_info->expiry_time))
                @php
                  $exp = "btn-secondary"
                @endphp
              @else
                @php
                  $exp  = 'btn-primary';
                @endphp
              @endif
              <tr>
                
                <!-- <td>
                  {!! __($form_info->user_type) !!}
                </td> -->
                <td> 
                    @if(session('locale') == 'fr' && $form_info->subform_title_fr != null)
                    <?php echo $form_info->subform_title_fr; ?>
                    @else
                    <?php echo $form_info->subform_title; ?>
                    @endif

                </td>
                <td>
                  @if(session('locale') == 'fr' && $form_info->form_title_fr != null)
                  <?php echo $form_info->group_name_fr; ?>
                  @else
                  <?php echo $form_info->group_name; ?>
                  @endif
                </td>
                <td>
                    @if(empty($form_info->other_number))
                      A-{{ $form_info->client_id }}-{{ $form_info->asset_number }}
                    @else
                      N-{{ $form_info->client_id }}-{{ $form_info->other_number }}
                    @endif
                </td>
                <td>
                    @if(empty($form_info->other_number))
                        {{ $form_info->asset_name }} 
                    @else
                        {{ $form_info->other_id }} 
                    @endif
                </td>
                <!-- @if(Auth::user()->role == 2)
                  <td>
                      <?php 
                          if (isset($form_info->total_internal_users_count ))
                          {
                              
                              if($form_info->total_internal_users_count > 0 )
                              {
                                echo $form_info->total_internal_users_count;
                              }
                              else {
                                echo '-';    
                              }
                          }
                          else
                              echo '-';            
                      ?>
                  </td>  
                  <td>
                  
                      <?php
                          if (isset($form_info->total_external_users_count ))
                          {
                            if($form_info->total_external_users_count > 0 )
                            {
                              echo $form_info->total_external_users_count;
                            }
                            else {
                              echo '-';  
                            }
                            
                          }
                          else
                              echo '-';            
                      ?>
                  </td>
                @endif      -->
                <td>
                  <?php echo $form_info->email;  ?>
                </td>
                <td>
                  @php
                      $form_link = ''; 
                      if ($form_info->user_type == 'Internal')
                          $form_link = url('/audit/internal/'.$form_info->form_link);
                      if ($form_info->user_type == 'External')
                          $form_link = url('/audit/external/'.$form_info->form_link);   
                  @endphp
                  <a class="btn {{$exp}} td_round_btn" href="<?php echo $form_link; ?>" target="_blank">{{ __('Open') }}</a>
                </td>
                <td>
                  @if(auth()->user()->role == 2)
                    @if(strtotime(date('Y-m-d')) > strtotime($form_info->expiry_time))
                      <input type="button" class="btn btn-sm btn-primary"  onclick="extend_expire('{{$form_info->form_link}}','{{$form_info->form_link_id}}')" value="{{__('Extend Expiry')}}">
                    @else
                      {{ date('Y-m-d', strtotime($form_info->expiry_time)) }}
                    @endif
                  @else
                    {{ date('Y-m-d', strtotime($form_info->expiry_time)) }}
                  @endif
                </td> 
                <td>
                  <span style="margin-right: 0px !important;color:#<?php echo ($form_info->is_locked)?('7bca94'):('f26924'); ?>">@if(strtotime(date('Y-m-d')) > strtotime($form_info->expiry_time)) {{__('Expired')}} @elseif($form_info->is_temp_lock) {{__('Locked')}} @elseif($form_info->is_locked) {{__('Submitted')}} @else {{__('Not Submitted')}} @endif</span>
                </td>
                @if(auth()->user()->role == 2)
                <td>
                  <input type="button" class="btn btn-sm btn-<?php echo ($form_info->is_temp_lock == 1)?("success"):("danger") ?>"  onclick="temp_lock('{{$form_info->form_link}}','{{$form_info->form_link_id}}')" value="{{($form_info->is_temp_lock == 1)? __('Unlocked'): __('Locked')}}">
                </td> 
                @endif
                <!-- <td>
                  <label class="switch switch-green">
                    <input type="checkbox"   class="switch-input"  onclick="lock_unlock('the_toggle_button-{{$key}}')"  @if(!$form_info->is_locked) checked="" @endif>
                    <span style="margin-right: 0px !important;" class="switch-label" data-toggle="tooltip" title="{{($form_info->is_locked)? __('Locked'): __('Unlocked')}}" data-on="{{ __('on') }}" data-off="{{ __('Off') }}"></span>
                    <span style="margin-right: 0px !important;" class="switch-handle" data-toggle="tooltip" title="{{($form_info->is_locked)? __('Locked'): __('Unlocked')}}"></span>
                  </label>
                  <div class="d-none"> <input style="display: none !important;" id="the_toggle_button-{{$key}}" type="checkbox"  title="{{($form_info->is_locked)? __('Locked'): __('Unlocked')}}" class="unlock-form " value="{{!$form_info->is_locked}}" u-type="{{$lu_utype}}" link="{{$form_info->form_link}}"></div>
                </td> -->
                
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        </div>
      </div>
    </div>
  </section>
  <script src="{{url('frontend/js/jquery.mswitch.js')}}"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
        $(document).ready(function() {
            $('#datatable').DataTable({
              // Disable auto-sort by name
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
        console.log(post_data)
        $.ajax({
          url:'{{ route('unlock_form') }}',
          method:'post',
          data:post_data,
          beforeSend:function () {

          },
          data:post_data,
          success: function (response) {
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
            //alert("Failed. "+emails[i]+" is not valid email");
            swal("{!! __('Invalid Email') !!}", emails[i]+" {!! __('is not valid email. Please enter email in correct format') !!}", "error");
            return 0;
          }
        }
        
        var post_data                 = {};
        
        post_data['_token']           = '{{csrf_token()}}';
        post_data['emails']           = emails;
        post_data['client_id']        = {{ Auth::user()->client_id }}; 
        
        
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
        console.log(post_data)

        $.ajax({
          url:'{{ route('change_form_access') }}',
          method:'post',
          data:post_data,
          beforeSend:function () {

          },
          data:post_data,
          success: function (response) {
            console.log("huhu243432423")
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