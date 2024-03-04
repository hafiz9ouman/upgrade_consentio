<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $page_title }}</title>
<meta content='width=device-width, initial-scale=1, maximum-scale=1' name='viewport'/>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" />
<?php 
	// load this css in client to match admin form style
	if (isset($load_admin_css) && $load_admin_css == true): 
?>
	<link rel="stylesheet" type="text/css" href="{{url('backend/css/main.css')}}">
<?php 
	endif; 
?>


</head>
<body>
<div class="container">
<br />
<h3 align="center">{{ $page_heading }}</h3>
<br />
@yield('user_form')
</div>
</body>
</html>
