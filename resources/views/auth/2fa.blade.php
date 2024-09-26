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
     
      <div class="login-box col-4">
       
      <div class="container  text-center">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading mt-3 mb-2" style="font-size: 20px;"><strong>Two Factor Authentication</strong></div>
				<div class="panel-body">
				<p style="text-align: justify;">Two factor authentication (2FA) strengthens access security by requiring two methods (also referred to as factors) to verify your identity. Two factor authentication protects against phishing, social engineering and password brute force attacks and secures your logins from attackers exploiting weak or stolen credentials.</p>
					
	
		@if (session('error'))
		<div class="alert alert-danger">
		{{ session('error') }}
		</div>
		@endif
		@if (session('success'))
		<div class="alert alert-success">
		{{ session('success') }}
		</div>
		@endif
		@if(empty($data['user']->passwordSecurity))
		<form class="form-horizontal" method="POST" action="{{ route('generate2fasecret') }}">
		{{ csrf_field() }}
		<div class="form-group">
		<div class="col-md-6 col-md-offset-4">
		<button type="submit" class="btn btn-primary">
		Generate Secret Key to Enable 2FA
		</button>
		</div>
		</div>
		</form>
		@elseif(!$data['user']->passwordSecurity->google2fa_enable)
		<strong>1. Scan this QRCODE using Google/Microsoft Authenticator App:</strong><br/>
		<img src="data:image/png;base64,{{$data['google2fa_image'] }}" alt="" />
		<br/><br/>
		<strong>2.Enter the pin the code to Enable 2FA</strong><br/><br/>
		<form class="form-horizontal" method="POST" action="{{ route('enable2fa') }}">
		{{ csrf_field() }}
		<div class="form-group{{ $errors->has('verify-code') ? ' has-error' : '' }}">
		<label for="verify-code" class="col-md-4 control-label"></label>
		<div class="col-md-6">
		<input id="verify-code" type="password" class="form-control" name="verify-code" required>
		@if ($errors->has('verify-code'))
		<span class="help-block">
		<strong>{{ $errors->first('verify-code') }}</strong>
		</span>
		@endif
	</div>
	</div>
	<div class="form-group">
	<div class="col-md-6 col-md-offset-4">
	<button type="submit" class="btn btn-primary">
	Enable 2FA
	</button>
	</div>
	</div>
	</form>
	@elseif($data['user']->passwordSecurity->google2fa_enable)
	<strong>1. Scan this QRCODE using Google/Microsoft Authenticator App:</strong><br/>
		<img src="data:image/png;base64,{{$data['google2fa_image'] }}" alt="" />
		<br/>
		<strong>2. Enter the Pin Code to Verify 2FA</strong><br/><br/>
		<form class="form-horizontal" method="POST" action="{{ route('enable2fa') }}">
		{{ csrf_field() }}
		<div class="form-group{{ $errors->has('verify-code') ? ' has-error' : '' }}" style="text-align: center;">
		<!-- <label for="verify-code" class="col-md-4 control-label"></label> -->
		<div class="col-md-6" style="margin: 0 auto;">
		<input id="verify-code" type="password" class="form-control" name="verify-code" required>
		@if ($errors->has('verify-code'))
		<span class="help-block">
		<strong>{{ $errors->first('verify-code') }}</strong>
		</span>
		@endif
		</div>
	</div>
	<div class="form-group" style="text-align: center;">
	<div class="col-md-6 col-md-offset-4" style="margin: 0 auto;">
	<button type="submit" class="btn btn-primary">
	Verify
	</button>
	</div>
	</div>
	<!-- <div class="alert alert-success">
	2FA is Currently <strong>Enabled</strong> for your account.
	</div>
	<p>If you are looking to disable Two Factor Authentication. Please confirm your password and Click Disable 2FA Button.</p>
	<form class="form-horizontal" method="POST" action="{{ route('enable2fa') }}">
	<div class="form-group{{ $errors->has('current-password') ? ' has-error' : '' }}">
	<label for="change-password" class="col-md-4 control-label">Current Password</label>
	<div class="col-md-6">
	<input id="current-password" type="password" class="form-control" name="current-password" required>
	@if ($errors->has('current-password'))
	<span class="help-block">
	<strong>{{ $errors->first('current-password') }}</strong>
	</span> -->
	@endif
	</div>
	</div>
	<div class="col-md-6 col-md-offset-5">
	{{ csrf_field() }}
	<button type="submit" class="btn btn-primary ">Disable 2FA</button>
	</div>
	</form>
	@endif
	</form>
	</div>
	</div>
	</div>
	</div>
	</div>
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
@if(session('locale')=='fr')
alert('Veuillez saisir le code.');return false;
@else
alert('Please enter code.');return false;
@endif
} 



//alert(rememberme);
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
							//alert(response['status']);return false;
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
