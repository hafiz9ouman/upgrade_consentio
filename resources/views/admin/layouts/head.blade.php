<head>
    
    <title>Consentio | {{ __('We Manage Compliance') }}</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="{{ url('newfavicon.png') }}" type="image/png">
    	<meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="{{url('backend/css/main.css')}}">
    <link rel="stylesheet" type="text/css" href="https://foliotek.github.io/Croppie/croppie.css">
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	
	<link rel="stylesheet" href="{{url('backend/css/sweetalert.css')}}">
    
	<link rel="stylesheet" type="text/css" href="{{url('backend/css/custom.css')}}">
	
    <!-- datatables css-->	
	<link rel="stylesheet" type="text/css" href="{{url('backend/css/dataTables.min.css')}}">
	
	<script src="{{url('backend/js/jquery-3.2.1.min.js')}}"></script>
	<script src="{{url('backend/js/plugins/bootstrap-datepicker.min.js')}}"></script>
	<script src="{{url('backend/js/plugins/bootstrap-datepicker.min.js')}}"></script>
	<script src="{{url('backend/js/sweetalert.js')}}"></script>
    <!-- datatables js-->	
	<script src="{{url('backend/js/jquery.dataTables.js')}}"></script>
    <script src="https://foliotek.github.io/Croppie/croppie.js"></script>
    <style>
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.current, .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background-color: transparent !important;
            color: black !important;
            border: 1px solid black !important;
            padding: 5px 12px !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button{
            color:black !important;
            padding: 5px 12px !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover{
            color:white !important;
            background:rgb(51, 36, 36) !important;
            border: 1px solid black !important;
            cursor: pointer;
        }
        a.paginate_button {
            text-decoration: none;
        }
        .paginate_button.next{
            padding: 5px !important;
        }
        .paginate_button.previous{
            padding: 5px !important;
        }

        
        div.dataTables_length{
            float: left;
        }
        div.dataTables_length select {
            border: 1px solid black;
        }
        
        .dataTables_info{
            float:left;
        }
        .dataTables_paginate{
            display:flex;
            justify-content:end;
            align-items:center;
        }
        @media screen and (max-width: 767px){
            div.dataTables_wrapper div.dataTables_length, div.dataTables_wrapper div.dataTables_filter, div.dataTables_wrapper div.dataTables_info, div.dataTables_wrapper div.dataTables_paginate {
                text-align: center;
                /* display: inline-flex; */
            }
            .dataTables_paginate{
                justify-content:center;
                align-items:center;
            }
            .dataTables_info{
                float:none;
            }
            div.dataTables_length{
                float: none;
            }
            div.dataTables_filter{
                display:flex;
                justify-content:center;
                align-items:center;
            }
        }

    </style>
  </head>