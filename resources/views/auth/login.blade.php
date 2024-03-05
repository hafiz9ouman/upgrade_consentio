<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Consentio | {{ __('We Manage Compliance') }}</title>
    <!-- Custom -->
    <link href="{{ url('public/assets-new/img/favicon.png')}}" rel="icon">
    <!-- Vendor CSS Files -->
    <link href="{{ url('public/assets-new/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{ url('public/assets-new/vendor/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com"> 
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <!-- Template Main CSS File -->
    <link href="{{ url('public/assets-new/css/style.css')}}" rel="stylesheet">
    <style>
      body{
            display: flex;
            flex-direction: column;
        }
        footer{
            margin-top: auto;
        }
        .login-option-page section.section.dashboard {
            height: 90vh;
        }
        body.dashboard section.section.dashboard {
            /* position: initial; */
        }
        body.dashboard {
            padding-top: 0px;
            padding-right: 0px;
        }
        .input-group-append{
            position: absolute;
            right: 15px;
            top: 20px;
            font-size: 20px;
        }
        @media (max-width: 800px){
            .login-option-page .form-login {
                background: none;
                border-radius: 0px;
                background: none;
                box-shadow: 0px 0px 0px 0px rgba(0, 0, 0, 0.15);
            }
            .login-option-page .form-login button {
                width: 50%;
            }
            .input-group-append{
            top: 10px;
        }
        }
        @media (max-width: 568px){
            footer{
                font-size:10px;
            }
        }
        
        
    </style>
</head>

<body class="dashboard login-option-page" style="background:#fbfbfd;">
    <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center"  style="background:transparent;">
    <div class="d-flex align-items-center justify-content-between">
      <a href="{{ url('/') }}" class="logo d-flex align-items-center" style="background: transparent;">
        <img src="{{url('public/assets-new/img/logo.webp')}}" style="height:40px;" alt="">
      </a>
    </div><!-- End Logo -->
  </header><!-- End Header -->

  <section class="section dashboard">
      <div class="row">
        <div class="col-12">
          <div class="form-login">
            <img src="{{url('public/assets-new/img/login-logo.webp')}}" class="login-logo">
            <h1>{{ __('Compliance Management') }}</h1>
            @if (Session::has('status'))
            <div class="alert alert-danger fw-bolder" style="color: red;">
                {{ __(session()->get('status')) }}
            </div>
            @endif
          <form class="login-form" method="POST" action="{{ route('login_post') }}" id="admin_login">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <div class="form-group row">
                  <div class="col-sm-12"> 
                      <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="{{ __('Email') }}" required autofocus>
                      @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                        @endif
                  </div>
                  <div class="col-sm-12">
                    <div class="input-group" style="position:relative;">
                        <input id="password" type="password" class="form-control" name="password" placeholder="{{ __('Password') }}" required>
                        <div class="input-group-append">
                            <span class="input-group-text" id="toggle-password">
                                <i class="fa fa-eye-slash" aria-hidden="true"></i>
                            </span>
                        </div>
                    </div>
                    @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                    @endif
                </div>
                  <div class="col-sm-12">
                      <button type="submit" class="btn btn-primary add-btn" id="sign-in">{{ __('SIGN IN') }}</button>
                  </div>
              </div>  
          </form> 
          <div class="form-flag">
            @if(session('locale')=='fr')
            <a href="{{ url('language/en') }}">EN</a>
            @elseif(session('locale')=='en')
            <a href="{{ url('language/fr') }}">FR</a>
            @endif</div>
        </div>
        </div>
      </div>
    </section>
    <footer style="background:#ced4da;padding:20px;color:#424245;opacity:0.6;position: fixed;bottom: 0;width:100%" class="fixed-bottom">
        <div class="container">
            <div class="row">
                <div class="col-6">
                    <a href="#" style="color:#424245;opacity:1;">{{__('Privacy Policy')}}</a> | <a href="#" style="color:#424245;">{{__('Terms & Conditions')}}</a>
                </div>
                <div class="col-6 text-right" style="color:#424245;opacity:1;">
                    {{__('Copyright © 2023 Consentio Inc. All rights reserved.')}}
                </div>
            </div>
        </div>
    </footer>


    <script src="{{url('public/assets-new/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.3.1/tinymce.min.js"></script>  
    <script src="{{url('public/assets-new/js/main.js')}}"></script>
    
    <script type="text/javascript">
        $('#reload').click(function(e) {
            e.preventDefault();
            $.ajax({
                type: 'GET',
                url: '{{url("reload-captcha")}}',
                success: function(data) {
                    $(".captcha span").html(data.captcha);
                }
            });
        });
    </script>
    <script>
        document.getElementById('toggle-password').addEventListener('click', function () {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        });
    </script>
</body>

</html>