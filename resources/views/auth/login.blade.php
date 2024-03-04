<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <title>Consentio | {{ __('We Manage Compliance') }}</title>
    <link rel="icon" href=" {{ url('newfavicon.png') }}" type="image/png">
</head>
<style>
    body {
        font-family: 'Montserrat', sans-serif;
        background: #fff;
    }

    .login_image img {
        height: 530px !important;
        margin-left: -11px;
        margin-top: 22px;
    }

    .logo_login img {
        width: 300px;
    }

    .login_fields h3 {
        text-align: center;
        font-weight: 700;
        font-size: 25px;
        margin: 2rem 0;
    }

    .filed_log label {
        margin: 0;
        color: #7D7D7D;
        padding-left: 6px;
        font-weight: 600;
        font-size: 14px;
    }

    .filed_log input {
        background: #EFEFEF;
        border-radius: 10px;
        border: none;
        height: 44px;
        outline: none;
        font-size: 14px;
    }

    .filed_log input:focus {
        box-shadow: 0px 2px 0 1px rgb(0 123 255 / 25%);
    }

    .forgot_link a {
        font-size: 12px;
        color: #0f75bd;
        text-decoration: underline;
        font-weight: 500;
    }

    .add_zi {
        z-index: 99999;
    }

    .filed_log {
        margin-bottom: .8rem;
    }

    .sign_in_btn button {
        background: #73b84d;
        font-size: 16px;
        font-weight: 600;
        color: #fff !important;
        padding: 8px 6rem;
        border-radius: 10px;
        transition: .3s;
        margin-top: 1.3rem;
    }

    .sign_in_btn button:hover {
        transform: scale(.9);
    }

    .sign_in_btn button:active {
        transform: scale(.8);
    }

    .login_box {
        filter: drop-shadow(6.364px -6.364px 43px rgba(177, 177, 177, 0.549));
        background: #fff;
        padding: 4rem 2rem 1rem;
        border-radius: 14px;
        border-top: 6px solid #0f75bd;
    }

    .login_section {
        padding: 3rem 2rem 2rem;
    }

    .add_custom_width {
        max-width: 37.5%;
    }

    .login_image {
        position: relative;
    }

    .login_image p {
        position: absolute;
        top: 54px;
        left: 18px;
        color: #fff;
        font-weight: 500;
        font-size: 16px;
    }

    .main_change_language_login a {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #0f75bd;
        color: #fff !important;
        border-radius: 6px;
        padding: 7px;
        /*width: fit-content;*/
        margin: 0 auto;
    }

    .language_dropdown {
        margin-top: 3rem;
    }

    @media screen and (max-width: 991px) {
        .add_custom_width {
            display: none;
        }

        .login_fields h3 {
            margin: 2rem 0 1rem;
        }

        .login_fields p {
            font-size: 14px;
        }

        .sign_in_btn button {
            width: 100%;
            padding: 8px 0rem;
        }

        .add_zi {
            padding-right: 15px !important;
        }

        .login_section {
            padding: 2rem 1rem 1rem;
        }
    }
</style>
<body>
    <!-- <img src="./new_images/login_side_img.png" alt="" class="img-fluid"> -->
    <section class="login_section">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="col-md-12 col-lg-5 pr-0 add_zi">
                    @if (Session::has('status'))
                    <div class="alert alert-danger">
                        {{ session()->get('status') }}
                    </div>
                    @endif
                    <div class="login_box">
                        <form class="login-form" method="POST" action="{{ route('login_post') }}" id="admin_login">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="logo_login text-center">
                                <img src="new_images/blue_logo.png" alt="" class="img-fluid">
                            </div>
                            <div class="login_fields">
                                <h3>{{ __('Compliance Management') }}</h3>
                                <p class="d-lg-none d-md-block" style="text-align: center;">
                                    {{ __('Consentio is an innovative one-stop platform that simplifies data protection processes and compliance management activities for organisations.') }}
                                </p>
                                <div class="filed_log">
                                    <label>{{ __('Email') }}</label>
                                    <input id="email" type="email" class="form-control" name="email"
                                        value="{{ old('email') }}" required autofocus>
                                    @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="filed_log">
                                    <label>{{ __('Password') }}</label>
                                    <input id="password" type="password" class="form-control" name="password" required>
                                    @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="sign_in_btn text-center" style="margin-bottom: 17px;">
                                    <button type="submit" class="btn">{{ __('SIGN IN') }}</button>
                                </div>
                                
                            </div>
                        </form>
                        <!-- {{-- main_change_language_login --}} -->
                        <div class="language_dropdown">
                            @if(session('locale')=='fr')
                            <a class="  btn-sm" style=" display:flex; align-items:center;"
                                href="{{ url('language/en') }}"><img src="{{url('public/img/eng.png')}}"
                                    style=" width: 23px; margin-right: 5px;">EN</a>
                            @elseif(session('locale')=='en')
                            <a class=" btn-sm" style=" display:flex; align-items:center;"
                                href="{{ url('language/fr') }}"><img src="{{url('public/img/fr.png')}}"
                                    style=" width: 23px; margin-right: 5px;">FR</a>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- <div class="col-lg-7 pl-0 add_custom_width">
                    <div class="login_image">
                        <img src="./new_images/login_image2.png" alt="" class="img-fluid" style="height: 523px;    ">
                        <p style="right: 21px; text-align: center;">
                            {{ __('Consentio is an innovative one-stop platform that simplifies data protection processes and compliance management activities for organisations.') }}
                        </p>
                    </div>
                </div> -->
            </div>
        </div>
    </section>

    
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
</body>

</html>