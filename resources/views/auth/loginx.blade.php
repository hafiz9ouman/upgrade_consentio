<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	  
	  <!-- <link rel="shortcut icon" type="image/x-icon" href="{{url('images/favicon-1.ico')}}"> -->
	  <link rel="icon" href="{{ url('image/favicon.ico')}}" type="image/png">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="{{url('backend/css/main.css')}}">
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>D3GDPR</title>
  </head>
  <body>
    <section class="material-half-bg">
      <div class="cover"></div>
    </section>
    <section class="login-content">
      <div class="logo">
        <?php /*?><h1>VEDI</h1><?php */?>
        <!-- <a class="app-header__logo" href="" style="background: none; font-size: 40px;">I Just Won</a> -->
        <img src="{{url('image/D3GDPR41.png')}}">
		  <!-- <img src="{{url('/logo-vedi.png')}}" alt="" width="200"> -->
      </div>
      <div class="login-box">
        
		 <form class="login-form" method="POST" action="{{ route('login') }}" id="admin_login">
                        {{ csrf_field() }}
						
          <h3 class="login-head"><i class="fa fa-lg fa-fw fa-user"></i> SIGN IN </h3>
          <div class="form-group">
            <label class="control-label">EMAIL</label>
             <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
			   @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
          </div>
          <div class="form-group">
            <label class="control-label">PASSWORD</label>
           <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
          </div>
          <div class="form-group">
            <div class="utility">
            <!--   <div class="animated-checkbox">
              
              <p class="semibold-text mb-2"><a href="{{url('register')}}"> Register Now </a></p>
              
              </div> -->
              <!-- <p class="semibold-text mb-2"><a href="javascript:void(0);" data-toggle="flip">Forgot Password ?</a></p> -->
            </div>
          </div>
          <div class="form-group btn-container">
            <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-sign-in fa-lg fa-fw"></i>SIGN IN</button>
          </div>
        </form>

        <form class="forget-form" action="index.html">
          <!-- <h3 class="login-head"><i class="fa fa-lg fa-fw fa-lock"></i>Forgot Password ?</h3>-->
          <div class="form-group">
            <label class="control-label">EMAIL</label>
            <input class="form-control" type="text" placeholder="Email">
          </div>
          <div class="form-group btn-container">
            <button class="btn btn-primary btn-block"><i class="fa fa-unlock fa-lg fa-fw"></i>RESET</button>
          </div>
          <div class="form-group mt-3">
            <p class="semibold-text mb-0"><a href="#" data-toggle="flip"><i class="fa fa-angle-left fa-fw"></i> Back to Login</a></p>
          </div>
          
        </form>
      </div>
   <!--    <div class="row">
         <div class="form-group">
            <div class="col-md-8 col-md-offset-4">
              <a href="{{url('/redirect')}}" class="btn btn-primary">Login with Facebook</a>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-8 col-md-offset-4">
              <a href="{{url('/redirect_google')}}" class="btn btn-primary">Login with Google</a>
            </div>
        </div>
      </div> -->
    </section>
    <!-- Essential javascripts for application to work-->
    <script src="{{url('backend/js/jquery-3.2.1.min.js')}}"></script>
    <script src="{{url('backend/js/popper.min.js')}}"></script>
    <script src="{{url('backend/js/bootstrap.min.js')}}"></script>
    <script src="{{url('backend/js/main.js')}}"></script>
    <!-- The javascript plugin to display page loading on top-->
    <script src="{{url('backend/js/plugins/pace.min.js')}}"></script>
    <script type="text/javascript">
      // Login Page Flipbox control
	  
	  
/*       $('.login-content [data-toggle="flip"]').click(function() {
      	$('.login-box').toggleClass('flipped');
      	return false;
      });


  $(document).on('submit','#admin_login',function(e){
    e.preventDefault();
    $.ajax({
      headers: {
            'X-CSRF-TOKEN': $( 'meta[name="csrf-token"]' ).attr( 'content' )
          },
      type:'POST',
      url: $(this).attr('action'),
      data: $(this).serialize(),
      success: function(msg){
        if(msg.status==1){
          window.location.href='{{ url("/admin")}}';
        }else{
          alert(msg.msg);
        }
      }
    });
  }); */




    </script>
  </body>
</html>