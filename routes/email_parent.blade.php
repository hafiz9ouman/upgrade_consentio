<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
     <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<meta name="csrf-token" content="{{ csrf_token() }}">
    <title></title>
    <link rel="icon" href="{{ url('frontend/images/logo.png')}}" type="image/png">
    <link href="{{ url('frontend/css/slick.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ url('frontend/css/jquery.lineProgressbar.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ url('frontend/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ url('frontend/css/style.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script type="text/javascript" src="{{ url('frontend/js/jquery-2.2.3.min.js') }}"></script>
    

    </head>
   <body>
    <!-- header-->
    <main class="app-content">
      @yield('content')
    </main>
  
  </body>
</html>