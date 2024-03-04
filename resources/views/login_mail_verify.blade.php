<!DOCTYPE html>
<html>
  <head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

	  
	  <!-- <link rel="shortcut icon" type="image/x-icon" href="{{url('images/favicon-1.ico')}}"> -->
	  <link rel="icon" href="{{ url('image/favicon.ico')}}" type="image/png">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="{{url('backend/css/main.css')}}">
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <!-- <link rel="stylesheet" href="https://demos.creative-tim.com/argon-design-system-pro/assets/css/nucleo-icons.css" type="text/css">
    <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/vendor/animate.css/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/argon.min.css?v=1.0.0" type="text/css">
    <link href="https://demos.creative-tim.com/argon-design-system-pro/assets/css/nucleo-icons.css" rel="stylesheet">
     -->
    <link rel="stylesheet" href="../../assets/demo.css" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    
    <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
    <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/vendor/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <title>Consentio | {{ __('We Manage Compliance') }}</title>
  </head>
  <body>

    <style>
.login-content .login-box{
    min-width: 506px;
    min-height: 500px;

}
@media only screen and (min-width: 480px) {
.login-content .login-box{
	min-width: 359px;
    	min-height: 537px;
}	

}

@media only screen and (min-width: 768px) {
.login-content .login-box{
	min-width: 359px;
    	min-height: 537px;
}	

}




      .main_align_item_form {

      }
      .backarrow {
          position: absolute;
          top: 9px;
          left: 14px;
          font-size: 23px;
      }

      .language_dropdown {
        position: absolute;
    /* top: 9px; */
    left: 7px;
    font-size: 22px;
    bottom: 4px;
      }
    </style>

    <section class="material-half-bg">
      <div class="cover"></div>
    </section>
    <section class="login-content">
     
      <div class="login-box">
       
		 <div class="login-form">
                        

                        <div class="backarrow">
                          <a href="{{ url('logout') }}"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i></a>
                        </div>
						
          <h3 class="login-head">
            
            <img src="{{ url('_organisation.png') }}" style="width: 258px;">
          </h3>
           1.Go to the inbox of <strong><?php //echo $email;?></strong> and open the e-mail from <strong>noreply@consentio.cloud</strong> with the subject line "Consentio 2FA Code".<br>
    (Can't find the e-mail? Please check your spam folder as well.)<br>
    2.Enter the authentication code provided in the e-mail. 


          <div class="form-group">
            <label class="control-label">{{ __('Email Verification Code') }}</label>
             <input id="data-id"  type="text"  class="form-control" name="code" required autofocus>
                @if ($errors->has('code'))
                    <span class="help-block">
                        <strong>{{ $errors->first('code') }}</strong>
                    </span>
                @endif
                <span style="vertical-align: text-bottom;" ><input style="margin-bottom: 10px;" type="checkbox" name="rememberme" id="rememberme" />&nbsp;Do not ask me for code for 90 days.</span><br>

          </div>
          <div class="form-group main_align_item_form">
            <div class="mb-2">          
          <div class="form-group btn-container">
            <button onclick="varify_user_code('{{ auth()->user()->id }}')"  class="btn btn-primary btn-block float-right">{{ __('Verify') }}</button>
          </div>
        </div>

        <div class="form-group btn-container">
            <a    class="btn btn-primary btn-block float-left resend_code" style="background: white"> {{ __('Resend Code') }}</a>
          </div>      
      </div>
      <div class="language_dropdown">
      @if(session('locale')=='fr')
      <a class="  btn-sm" style=" display:flex; align-items:center;" href="{{ url('language/en') }}"><img src="{{url('public/img/eng.png')}}" style=" width: 23px; margin-right: 5px;">EN</a>
      @elseif(session('locale')=='en')
      <a class=" btn-sm" style=" display:flex; align-items:center;" href="{{ url('language/fr') }}"><img src="{{url('public/img/fr.png')}}" style=" width: 23px; margin-right: 5px;">FR</a>
      @endif
      </div>
    </section>



<button class="btn btn-success" title="{{ __('Verification Code Sent, Please Check Your Email') }}" id="notification_show" style="display: none" data-toggle="notify" data-placement="top" data-align="center" data-type="success" data-icon="ni ni-bell-55"></button>

    <!-- Essential javascripts for application to work-->
    <script src="{{url('backend/js/jquery-3.2.1.min.js')}}"></script>
    
    <script src="{{url('backend/js/popper.min.js')}}"></script>
    <script src="{{url('backend/js/bootstrap.min.js')}}"></script>
    <script src="{{url('backend/js/main.js')}}"></script>
    <!-- The javascript plugin to display page loading on top-->
    <script src="{{url('backend/js/plugins/pace.min.js')}}"></script>
        <!-- <script src="https://demos.creative-tim.com/argon-dashboard-pro/assets/vendor/jquery/dist/jquery.min.js" type="text/javascript"></script>
    <script src="https://demos.creative-tim.com/argon-dashboard-pro/assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js" type="text/javascript"></script>
    <script src="https://demos.creative-tim.com/argon-dashboard-pro/assets/vendor/js-cookie/js.cookie.js" type="text/javascript"></script>
    {{--  --}}
    <script src="https://demos.creative-tim.com/argon-dashboard-pro/assets/vendor/anchor-js/anchor.min.js" type="text/javascript"></script>
    <script src="https://demos.creative-tim.com/argon-dashboard-pro/assets/vendor/clipboard/dist/clipboard.min.js" type="text/javascript"></script>
    <script src="https://demos.creative-tim.com/argon-dashboard-pro/assets/vendor/prismjs/prism.js" type="text/javascript"></script>
    <script src="https://demos.creative-tim.com/argon-design-system-pro/assets/demo/vendor/holder.min.js" type="text/javascript"></script>
    <script src="https://demos.creative-tim.com/argon-dashboard-pro/assets/vendor/moment.min.js" type="text/javascript"></script>
    <script src="https://demos.creative-tim.com/argon-dashboard-pro/assets/vendor/bootstrap-notify/bootstrap-notify.min.js" type="text/javascript"></script>
    <script src="https://demos.creative-tim.com/argon-dashboard-pro/assets/js/argon.min.js" type="text/javascript"></script>
    <script src="https://demos.creative-tim.com/argon-dashboard-pro/assets/js/demo.min.js" type="text/javascript"></script> -->
    <script type="text/javascript">

    </script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/js/bootstrap-notify.min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-notify@3.1.3/bootstrap-notify.js" type="text/javascript"></script>





  </body>


  <script type="text/javascript">
         function varify_user_code(id){

if ($('#rememberme').is(':checked')) {

var rememberme = 'Yes';
}else{
var rememberme = 'No';
}

      
 var code = $('#data-id').val();

if(code == ''){
alert('Please enter code.');return false;
} 




           var form_data = {
                id: id,
                rememberme:rememberme,
                code: $('#data-id').val(),
            };
             if($('#data-id').val() == ''){
                show_alert_message( title = "{!! __('Code Can Not Be Empty') !!}" , message = "{{ __('Please Enter Code.') }}" , type = "danger" , icon = "glyphicon glyphicon-remove-sign");
             } else{
                    $.ajax( {
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $( 'meta[name="csrf-token"]' ).attr( 'content' )
                        },
                        url: '{{url('verify_code')}}',
                        data: form_data,
                        success: function ( response ) {
                            if(response['status'] == 'success'){
                              show_alert_message( 
                                                 title = "{!! __('Success') !!}" ,
                                                 message = response['message'] ,
                                                 type = response['status'] ,
                                                 icon = "",
                                  );  
                              setTimeout(function() {
                                    window.location.href = '{{ url('dashboard') }}';
                              }, 1000);
                            }
                            else{
                              show_alert_message(  
                                                 title = "{!! __('Woops!') !!}" ,
                                                 message = response['message'] ,
                                                 type = "danger" , icon = "glyphicon glyphicon-remove-sign",
                                                 icon = "",
                                                 );
                            }
                            
                        }
                    } );
                  }
         }     

         $('.resend_code').click(function () {
             $.ajax( {
                        type: 'GET',
                        url: '{{ url('send_code') }}',
                        success: function ( ) {
                            $.notify({
                                  // options
                                  title: '<strong>{!! __('Success') !!}</strong>',
                                  message: "<br>{{ __('Verification Code Sent, Please Check Your Email') }}",
                                  icon: 'glyphicon glyphicon-ok',
                                  url: '',
                                  target: ''
                                },{
                                  // settings
                                  element: 'body',
                                  //position: null,
                                  type: "success",
                                  allow_dismiss: true,
                                  newest_on_top: false,
                                  showProgressbar: false,
                                  placement: {
                                    from: "top",
                                    align: "center"
                                  },
                                  offset: 20,
                                  spacing: 10,
                                  z_index: 1031,
                                  delay: 3300,
                                  timer: 1000,
                                  url_target: '_blank',
                                  mouse_over: null,
                                  animate: {
                                    enter: 'animated fadeInDown',
                                    exit: 'animated fadeOutRight'
                                  },
                                  onShow: null,
                                  onShown: null,
                                  onClose: null,
                                  onClosed: null,
                                  icon_type: 'class',
                                }); 
                            // setTimeout( function () {
                                 // $('#resend').prop('disabled', false);
                            // },20000);
                        }
                    });      
        });
   
          function show_alert_message( title = "" , message = "" , type = "" , icon = "glyphicon glyphicon-ok"){
            $.notify({
                                  // options
                                  title: '<strong>'+title+'</strong><br>',
                                  message: message,
                                  icon: icon, 
                                  url: '',
                                  target: ''
                                },{
                                  // settings
                                  element: 'body',
                                  //position: null,
                                  type: type,
                                  allow_dismiss: true,
                                  newest_on_top: false,
                                  showProgressbar: false,
                                  placement: {
                                    from: "top",
                                    align: "center"
                                  },
                                  offset: 20,
                                  spacing: 10,
                                  z_index: 1031,
                                  delay: 3300,
                                  timer: 1000,
                                  url_target: '_blank',
                                  mouse_over: null,
                                  animate: {
                                    enter: 'animated fadeInDown',
                                    exit: 'animated fadeOutRight'
                                  },
                                  onShow: null,
                                  onShown: null,
                                  onClose: null,
                                  onClosed: null,
                                  icon_type: 'class',
                                });
          }
  </script>
</html>
