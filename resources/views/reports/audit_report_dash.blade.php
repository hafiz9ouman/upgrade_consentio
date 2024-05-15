<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="gb18030">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <style>
        body{
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        footer{
            margin-top: auto;   
        }
        #datatable {
            border: none;
        }
        #datatable-container {
            width: 100%; /* Adjust this as needed */
            overflow-x: scroll; /* Use "scroll" to always show the scrollbar */
            white-space: nowrap; /* Prevent text wrapping */
        }
        #datatable td {
            border: none;
        }
        #datatable th {
            border: none;
        }
        .dataTables_wrapper .dataTables_length {
            float: right !important;
            margin-right: 10px;
        }
        .dataTables_wrapper .dataTables_length select{
            border-radius: 20px;
            border: 1px solid #DADADA;
            background: #FEFEFE;
            width: 72px;
            height: 66px;
            color: #343434;
            font-size: 16px;
            text-align: center;
            margin: 0 5px;
        }
        
        .dataTables_wrapper .dataTables_filter {
            float: left !important;
        }
        #datatable_filter input {
            /* Your styling properties go here */
            border: 1px solid #ccc;
            padding: 15px 50px;
            border-radius: 35px;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
        }
        .button{
            padding: 12px 30px 12px 22px;
            border-radius: 110px;
            background: #0F75BD;
            color: #FFF;
            text-align: center;
            font-size: 18px;
            font-weight: 600;
            line-height: 20px;
        }
        .button:hover{
            color: #FFF;
        }
        .buton{
            padding: 5px 15px;
            border-radius: 110px;
            border: 1px solid #0F75BD;
            background: #0F75BD;
            color: #FFF;
            text-align: center;
            font-size: 15px;
            font-weight: 500;
            line-height: 20px;
        }
        .buton:hover{
            color: #FFF;
            background: #71BA4F;
            border: 1px solid #71BA4F;
        }

        #datatable th, td {
            max-width: 210px; /* Adjust this value as needed */
            word-wrap: break-word;
            white-space: normal;
        }
        @media screen and (max-width: 400px) {
            .xxx{
            display: flex !important;
            flex-direction: column !important ;
            gap: 10px !important;
            justify-content: center;
        }.buton{
            width: 180px !important;
        }.xxx2{
            width: 206 !important;
      }
  }
        
    </style>
    <title>
        @if (View::hasSection('title'))
        @yield('title')
        @else
        Consentio | {{ __('We Manage Compliance') }}
        @endif

    </title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href=" {{ url('assets-new/img/favicon.png') }}" type="image/png">
    <script src="{{ url('backend/js/sweetalert.js') }}"></script>
    <link rel="stylesheet" href="{{ url('backend/css/sweetalert.css') }}">
    <!--  -->
    <!--  -->
    <!-- Custom fonts for this template-->
    <link href="{{ url('frontend/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet"> -->
    <!-- Custom styles for this template-->
    <!-- <link href="{{ url('frontend/css/sb-admin-2.min.css') }}" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css" integrity="sha512-1PKOgIY59xJ8Co8+NE6FZ+LOAZKjy+KY8iq0G4B3CyeY6wYHN3yt9PW0XpSriVlkMXe40PTKnXrLnZ9+fkDaog==" crossorigin="anonymous" />
    <!--///////////////mycss////////-->

    <link href="{{ url('frontend/css/table.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-html5-1.6.1/datatables.min.css" />
    <?php
    // load this css in client to match admin form style
    if (isset($load_admin_css) && $load_admin_css == true) : ?>
        <link rel="stylesheet" type="text/css" href="{{ url('backend/css/main.css') }}">
    <?php endif; ?>


    <!-- BOXicon -->
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://unpkg.com/boxicons@latest/dist/boxicons.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ url('assets-new/vendor/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ url('assets-new/css/style.css') }}">
</head>

<body class="dashboard">



<script src="{{ url('frontend/js/jquery.min.js') }}"></script>

<script src="{{ url('frontend/js/bootstrap.bundle.min.js') }}"></script>


    
<div class="container-fluid mt-5" style="background-color: white;" id="myDiv">
    <div class="row align-items-end">
        <div class="col-6">
            <h4 class="mt-3" style="color:black;"><b><span id="client" class="d-none">{{$company->name}} - </span>@if(session('locale')=='fr') {{$group[0]->group_name_fr}} @else {{$group[0]->group_name}} @endif - {{__('Audit Report')}}</b></h4>
        </div>
        <div class="col d-flex justify-content-end xxx">
            <img class="d-none mb-3" id="report-logo" src="{{ url('img/' . $company_logo) }}" style="max-height: 70px;max-width:280px;" alt="logo">
            <p class="d-none" id="date">{{__('Report Date')}}: {{ now()->format('d-m-Y') }}</p>
            <a class="report-change mr-1" href="{{ url('/dash/remediation/' . $group_id) }}" class="xxx2"><button class="btn btn-secondary xxx2" style="border-radius:30px;font-weight: 500;font-size: 15px; white-space:nowrap">{{__('Remediation Report')}}</button></a>
            <button id="screenshotButton" class="buton mr-1">{{__('Download')}}</button>
            <div>
                <input type="hidden" id="fav_id" name="fav_id" value="{{$group_id}}">
                @php
                    $fav=DB::table('forms')->where('group_id', $group_id)->pluck('is_fav');
                    //echo $fav[0];
                @endphp
                <div class="favorite-buttons">
                    <button id="add_favorite" class="buton"><img src="{{url('assets-new/rstar.png')}}" style="width: 22px;" alt=""></button>
                    <button id="rem_favorite" class="buton"><img src="{{url('assets-new/star.png')}}" style="width:22px;" alt=""></button>
                </div>
                
            </div>
        </div>
    </div>
    <br>
    <input type="hidden" class="group_id" value="{{$group_id}}">
    <div class="row">
        <div class="col-md-3">
            <div id="chart"></div>
        </div>
        <div class="col-md-5 pt-2">
            <div class="row">
            <div class="col">
                <span style="font-size: 14px;"><b>{{__('Data Classification')}}</b></span><br>
                @php
                    $existingUnits = [];
                @endphp
                @foreach ($remediation_plans as $subform => $plans)
                @if (count($plans) > 0)
                @if (!in_array($plans[0]->classification_name_en, $existingUnits) && $plans[0]->classification_name_en!=null)
                <input type="checkbox" id="checkbox-group" class="class-group change" value="{{$plans[0]->classification_name_en}}"><span style="font-size: 14px;">@if(session('locale')=='fr') {{$plans[0]->classification_name_fr}} @else {{$plans[0]->classification_name_en}} @endif</span><br>
                @php
                $existingUnits[] = $plans[0]->classification_name_en;
                @endphp
                @endif
                @endif

                @endforeach
            </div>
            <div class="col">
                <span style="font-size: 14px;"><b>{{__('Impact')}}</b></span><br>
                @php
                $existingUnits = [];
                $counter = 1;
                @endphp
                @foreach ($remediation_plans as $subform => $plans)
                @if (count($plans) > 0)
                @if($plans[0]->impact_name_en)
                @if (!in_array($plans[0]->impact_name_en, $existingUnits) && $plans[0]->impact_name_en!=null)
                <input type="checkbox" id="checkbox-group" class="impact-group change" value="{{$plans[0]->impact_name_en}}"><span style="font-size: 14px;"> {{$counter}} - @if(session('locale')=='fr') {{$plans[0]->impact_name_fr}} @else {{$plans[0]->impact_name_en}} @endif</span><br>
                @php
                $existingUnits[] = $plans[0]->impact_name_en;
                $counter++;
                @endphp
                @endif
                @endif
                @endif

                @endforeach
            </div>
            <div class="col">
                <span style="font-size: 14px;"><b>{{__('Business Unit')}}</b></span><br>
                @php
                $existingUnits = [];
                @endphp
                @foreach ($remediation_plans as $subform => $plans)
                @if (count($plans) > 0)
                @if($plans[0]->business_unit)
                @if (!in_array($plans[0]->business_unit, $existingUnits) && $plans[0]->business_unit!=null)
                <input type="checkbox" id="checkbox-group" class="business-group change" value="{{$plans[0]->business_unit}}"><span style="font-size: 14px;"> {{$plans[0]->business_unit}}</span><br>
                @php
                $existingUnits[] = $plans[0]->business_unit;
                @endphp
                @endif
                @endif
                @endif
                @endforeach
            </div>
            </div>
        </div>
        <div class="col-md-2">
            <div id="chart-container"></div>
        </div>
        <div class="col-md-2">
            <div id="bus-chart"></div>
        </div>

    </div>
    <div class="row mt-3 overflow-auto">
        <div class="col-12">
            <table id="datatable" class="table table-striped table-sm text-dark border" width="100%">
                <thead>
                    <tr class="border">
                        <th>{{__('Asset Name')}}</th>
                        <th>{{__('Asset Tier')}}</th>
                        @foreach($data as $question)
                        <th>C{{$loop->iteration}} - @if(session('locale')=='fr') {{$question->question_short_fr}} @else {{$question->question_short}} @endif</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($remediation_plans as $subform => $plans)
                    @if (count($plans) == 0)
                    @continue;
                    @endif
                    <tr class="border">
                        <td>{{$plans[0]->name}}</td>
                        <td>{{__($plans[0]->tier)}}</td>
                        @foreach ($plans as $plan)
                        <td style="color:{{$plan->text_color}} !important; background-color:{{$plan->color}} !important;">{{__($plan->rating)}}</td>
                        @endforeach
                    </tr>
                    @endforeach
                    <!-- @foreach($remediation_plans as $plan)
                            {{-- <th style="color:{{$plan->text_color}}; background-color:{{$plan->color}};">{{$plan->rating}}</th> --}}
                    @endforeach -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- counts -->
<?php
// Assuming you have an array of data in your Laravel controller
$chartData = [
    ['Tier', 'Value'],
];
$impData = [
    ['hosting', 'Value'],
];
$busData = [
    ['business', 'Value'],
];
?>

<!-- For Tier Chart -->
@foreach ($remediation_plans as $subform => $plans)
    @if (count($plans) > 0 && isset($plans[0]->tier))
        @php
            $name = $plans[0]->tier;
            $datacount = 0;
        @endphp

        @foreach ($chartData as $entry)
            @if ($entry[0] == $name)
                @php
                    $datacount = $entry[1];
                    break;
                @endphp
            @endif
        @endforeach

        @if ($datacount == 0)
            @foreach ($remediation_plans as $count)
                @if (isset($count[0]->tier) && $name == $count[0]->tier)
                    @php
                        $datacount++;
                    @endphp
                @endif
            @endforeach
            @php
                $chartData[] = [$name, $datacount];
            @endphp
        @endif
    @endif
@endforeach
@if(session('locale')=='fr')
    @php
        $chartData = array_map(function ($item) {
            if ($item[0] == "Crown Jewels") {
                $item[0] = "Les joyaux de la couronne";
            } elseif ($item[0] == "tier 1") {
                $item[0] = "Niveau 1";
            } elseif ($item[0] == "tier 2") {
                $item[0] = "Niveau 2";
            } elseif ($item[0] == "tier 3") {
                $item[0] = "Niveau 3";
            }
            return $item;
        }, $chartData);
    @endphp
@endif

<!-- For Hosting Type -->
@foreach ($remediation_plans as $subform => $plans)
    @if (count($plans) > 0 && isset($plans[0]->hosting_type))
        @php
            $name = $plans[0]->hosting_type;
            $datacount = 0;
        @endphp

        @foreach ($impData as $entry)
            @if ($entry[0] == $name)
                @php
                    $datacount = $entry[1];
                    break;
                @endphp
            @endif
        @endforeach

        @if ($datacount == 0)
            @foreach ($remediation_plans as $count)
                @if (isset($count[0]->hosting_type) && $name == $count[0]->hosting_type)
                    @php
                        $datacount++;
                    @endphp
                @endif
            @endforeach
            @php
                $impData[] = [$name, $datacount];
            @endphp
        @endif
    @endif
@endforeach
@if(session('locale')=='fr')
    @php
        $impData = array_map(function ($item) {
            if ($item[0] == "Cloud") {
                $item[0] = "Nuage";
            } elseif ($item[0] == "On-Premise") {
                $item[0] = "Sur site";
            } elseif ($item[0] == "Hybrid") {
                $item[0] = "Hybride";
            } elseif ($item[0] == "Not Sure") {
                $item[0] = "Pas certain";
            }
            return $item;
        }, $impData);
    @endphp
@endif

<!-- For Business Location -->
@foreach ($remediation_plans as $subform => $plans)
    @if (count($plans) > 0 && isset($plans[0]->country))
        @php
            $name = $plans[0]->country;
            $datacount = 0;
        @endphp

        @foreach ($busData as $entry)
            @if ($entry[0] == $name)
                @php
                    $datacount = $entry[1];
                    break;
                @endphp
            @endif
        @endforeach

        @if ($datacount == 0)
            @foreach ($remediation_plans as $count)
                @if (isset($count[0]->country) && $name == $count[0]->country)
                    @php
                        $datacount++;
                    @endphp
                @endif
            @endforeach
            @php
                $busData[] = [$name, $datacount];
            @endphp
        @endif
    @endif
@endforeach


<!-- @php
    echo json_encode($busData);
@endphp -->

<!-- Google Charts library -->
<script src="https://www.gstatic.com/charts/loader.js"></script>

<!-- html2pdf.js library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>


<script>
    document.getElementById('screenshotButton').addEventListener('click', function() {
        // Destroy the DataTable
        if ($.fn.DataTable.isDataTable("#datatable")) {
            $("#datatable").DataTable().destroy();
        }

        // Add the d-none class to the button
        $(this).addClass('d-none');
        $('.report-change').addClass('d-none');
        $('.favorite-buttons').addClass('d-none');
        
        // Add Logo date and company name
        $('#date').removeClass('d-none');
        $('#client').removeClass('d-none');
        $('#report-logo').removeClass('d-none');
        $('#myDiv').removeClass('mt-5');
        $('#myDiv').attr("style", "padding:7%;");


        // Capture screenshot and download report
        captureScreenshot();
    });

    function captureScreenshot() {
        // Get the target table element
        const targetTable = document.getElementById('datatable');

        // // Get the width of the table
        // const tableWidth = targetTable.offsetWidth; // This gives the width in pixels

        // // Use the table width as the screen width for the report download
        // const screenWidth = tableWidth;
        // console.log(screenWidth);
        // Get the screen dimensions
        const screenWidth = 900;
        const screenHeight = 550;

        // Specify the ID of the div you want to capture
        const divId = 'myDiv';

        // Get the target div element
        const targetDiv = document.getElementById(divId);

        // Create a container element to hold the target div temporarily
        const container = document.createElement('div');
        container.appendChild(targetDiv.cloneNode(true));

        // Convert the container element to PDF
        const options = {
            filename: 'Asset_Report.pdf',
            image: { type: 'jpeg', quality: 0.99 },
            html2canvas: { scale: 1 },
            jsPDF: {
                orientation: 'landscape',
                format: [screenWidth, screenHeight] // Set the page size to the screen dimensions
            }
        };

        html2pdf().set(options).from(container).save().then(function() {

            // Remove the d-none class from the button
            $('#screenshotButton').removeClass('d-none');
            $('.report-change').removeClass('d-none');
            $('.favorite-buttons').removeClass('d-none');
            // Remove Logo
            $('#client').addClass('d-none');
            $('#date').addClass('d-none');
            $('#report-logo').addClass('d-none');
            $('#myDiv').attr("style", "padding:0;");
            $('#myDiv').addClass('mt-5');

            // Reinitialize the DataTable after capturing the screenshot
            initializeDataTable();
        });
    }

    function initializeDataTable() {
        $('#datatable').DataTable({
            "order": [],
            "language": {
                "search": "",
                @if(session('locale')=='fr')
                "sLengthMenu":    "Montrer _MENU_ Entrées",
                "sZeroRecords":   "Aucun résultat trouvé",
                "sEmptyTable":    "aucune donnée disponible",
                "sInfo":          "Présentation de _START_ à _END_ d'un total de _TOTAL_ Entrées",
                "sInfoEmpty":     "Présentation de 0 à 0 d'un total de 0 Entrées",
                "sInfoFiltered": "(filtré à partir de _MAX_ nombre total d'entrées)",
                "sInfoPostFix":  "",
                "oPaginate": {
                    "sNext":    "Suivant",
                    "sPrevious": "Précédent"
                },
                "searchPlaceholder": "Cherche ici"
                @else
                "searchPlaceholder": "Search Here"
                @endif
                
            }
        });
    }
</script>



<!-- jQuery -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<!-- <script>
$(document).ready(function() {
    $('#datatable').DataTable({
        "order": [],
            "language": {
            "search": "",
            "searchPlaceholder": "Search Here"
        }
    });
});
</script> -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>


<script type="text/javascript">
    // First Chart 
    @if(session('locale') == 'fr')
    google.charts.load("current", {
        packages: ["corechart"], language: 'fr'
    });
    @else
    google.charts.load("current", {
        packages: ["corechart"]
    });
    @endif
    google.charts.setOnLoadCallback(function() {
        // Call drawChart with the chartData array as a parameter

        drawChart(@json($chartData));
        drawCharts(@json($impData));
        drawChartsz(@json($busData));
    });

    function drawChart(chartData) {
        // var chartData = @json($chartData);

        // Create an empty array to hold the dynamic data
        var dynamicData = [];

        // Add each row of data to the dynamicData array using a foreach loop
        chartData.forEach(function(row) {
            dynamicData.push(row);
        });

        // Create the data table using the dynamicData array
        var data = google.visualization.arrayToDataTable(dynamicData);

        var options = {
            title: '{{__('Asset Tier')}}',
            titleTextStyle: {
                fontSize: 14
            },
            // pieHole: 0.5,
            backgroundColor: 'transparent',
            // is3D: true,
            chartArea: {
                left: 0,
                top: 40,
                width: '100%',
                height: '100%'
            }, // Add this line to remove margin and padding
            margin: 0, // Add this line to remove margin
            padding: 0
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart'));
        chart.draw(data, options);
    }

    //   Second Charts
    function drawCharts(impData) {
        // var chartData = @json($impData);

        // Create an empty array to hold the dynamic data
        var dynamicData = [];

        // Add each row of data to the dynamicData array using a foreach loop
        impData.forEach(function(row) {
            dynamicData.push(row);
        });

        // Create the data table using the dynamicData array
        var data = google.visualization.arrayToDataTable(dynamicData);

        var options = {
            title: '{{__('Hosting Type')}}',
            titleTextStyle: {
                fontSize: 14
            },
            pieHole: 0.4,
            backgroundColor: 'transparent',
            colors: ['#6aa7f8', '#fdab89', '#3599b8', '#deee91', '#f6c7b6'],
            chartArea: {
                left: 0,
                top: 40,
                width: '100%',
                height: '100%'
            }, // Add this line to remove margin and padding
            margin: 0, // Add this line to remove margin
            padding: 0
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart-container'));
        chart.draw(data, options);
    }

    //   For Business Unit Chart
    function drawChartsz(busData) {
        // var chartData = @json($impData);

        // Create an empty array to hold the dynamic data
        var dynamicData = [];

        // Add each row of data to the dynamicData array using a foreach loop
        busData.forEach(function(row) {
            dynamicData.push(row);
        });

        // Create the data table using the dynamicData array
        var data = google.visualization.arrayToDataTable(dynamicData);

        var options = {
            title: '{{__('Asset Hosting Location')}}',
            titleTextStyle: {
                fontSize: 14
            },
            pieHole: 0.4,
            backgroundColor: 'transparent',
            colors: ['#3599b8', '#6aa7f8', '#bbd53b', '#fdab89', '#ff5500'],
            chartArea: {
                left: 0,
                top: 40,
                width: '100%',
                height: '100%'
            }, // Add this line to remove margin and padding
            margin: 0, // Add this line to remove margin
            padding: 0
        };

        var chart = new google.visualization.PieChart(document.getElementById('bus-chart'));
        chart.draw(data, options);
    }

    //   Other JS Code
    $(document).ready(function() {

        // Get the value of $fav[0] from PHP
        var favValue = <?php echo $fav[0]; ?>;

        // Get references to the elements by their IDs
        var addFavoriteButton = document.getElementById('add_favorite');
        var remFavoriteButton = document.getElementById('rem_favorite');

        // Check the value and add/remove the d-none class accordingly
        if (favValue === null || favValue === 0) {
            addFavoriteButton.classList.remove('d-none'); // Show add favorite button
            remFavoriteButton.classList.add('d-none');    // Hide remove favorite button
        } else {
            addFavoriteButton.classList.add('d-none');    // Hide add favorite button
            remFavoriteButton.classList.remove('d-none'); // Show remove favorite button
        }

        if ($.fn.DataTable.isDataTable("#datatable")) {
            $("#datatable").DataTable().destroy();
        }
        // Initialize DataTable
        var dataTable = $("#datatable").DataTable({
            "order": [],
            "language": {
                "search": "",
                @if(session('locale')=='fr')
                "sLengthMenu":    "Montrer _MENU_ Entrées",
                "sZeroRecords":   "Aucun résultat trouvé",
                "sEmptyTable":    "aucune donnée disponible",
                "sInfo":          "Présentation de _START_ à _END_ d'un total de _TOTAL_ Entrées",
                "sInfoEmpty":     "Présentation de 0 à 0 d'un total de 0 Entrées",
                "sInfoFiltered": "(filtré à partir de _MAX_ nombre total d'entrées)",
                "sInfoPostFix":  "",
                "oPaginate": {
                    "sNext":    "Suivant",
                    "sPrevious": "Précédent"
                },
                "searchPlaceholder": "Cherche ici"
                @else
                "searchPlaceholder": "Search Here"
                @endif
                
            }
        });

        // js code for report favorite
        $("#add_favorite").click(function(){
            var group_id= $("#fav_id").val();
            console.log(group_id);
            // Retrieve CSRF token from meta tag
            var token = $('meta[name="csrf-token"]').attr('content');

            Swal.fire({
                title: '{{__('Mark Report as favorite?')}}',
                // text: 'Mark Report as favorite?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '{{__('Yes')}}',
                cancelButtonText: '{{__('No')}}',
            }).then((result) => {
                if (result.isConfirmed) {
                    // User clicked "Yes," proceed with the AJAX request
                    $.ajax({
                        url: "/make-favorite",
                        method: "POST",
                        data: {
                            group_id: group_id,
                            _token: token
                        },
                        dataType: "json",
                        success: function (response) {
                            console.log(response);
                            $("#add_favorite").addClass("d-none");
                            $("#rem_favorite").removeClass("d-none");
                            swal.fire('{{__('Mark as Favorite')}}', '', 'success');
                        }
                    });
                }
            });
        })
        $("#rem_favorite").click(function(){
            var group_id= $("#fav_id").val();
            console.log(group_id);
            // Retrieve CSRF token from meta tag
            var token = $('meta[name="csrf-token"]').attr('content');

            Swal.fire({
                title: '{{__('Remove Report from Favorites?')}}',
                // text: 'Mark Report as favorite?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '{{__('Yes')}}',
                cancelButtonText: '{{__('No')}}',
            }).then((result) => {
                if (result.isConfirmed) {
                    // User clicked "Yes," proceed with the AJAX request
                    $.ajax({
                        url: "/remove-favorite",
                        method: "POST",
                        data: {
                            group_id: group_id,
                            _token: token
                        },
                        datatype: "json",
                        success: function(response){
                            console.log(response)
                            $("#rem_favorite").addClass("d-none");
                            $("#add_favorite").removeClass("d-none");
                            // console.log("ok");
                            swal.fire('{{__('Remove as Favorite')}}', '', 'success');
                            /// Hit the Button in Parent Document to reload fav reports. 
                            var parentDocument = window.parent.document;
                            var parentButton = parentDocument.getElementById('load');
                            parentButton.click();
                            // console.log("ok");
                        }
                    });
                }
            });

            
        })

        // Listen for change event on checkboxes with class "checkbox-group"
        $(".change").change(function() {
            var classUnits = [];
            var impactUnits = [];
            var businessUnits = [];
            var id = $(".group_id").val();
            // Iterate over each checkbox with class "checkbox-group" that is checked
            $(".class-group:checked").each(function() {
                // Add the value (business unit) to the selectedUnits array
                classUnits.push($(this).val());
            });
            $(".impact-group:checked").each(function() {
                // Add the value (business unit) to the selectedUnits array
                impactUnits.push($(this).val());
            });
            $(".business-group:checked").each(function() {
                // Add the value (business unit) to the selectedUnits array
                businessUnits.push($(this).val());
            });

            // Retrieve CSRF token from meta tag
            var token = $('meta[name="csrf-token"]').attr('content');

            // Make the AJAX call
            $.ajax({
                url: "/your-ajax-endpoints/" + id,
                method: "POST",
                data: {
                    class: classUnits,
                    impact: impactUnits,
                    business: businessUnits,
                    _token: token // Include the CSRF token in the data
                },
                dataType: "json",
                success: function(response) {
                    // Handle the response from the server
                    // console.log(response);

                    // Clear existing table rows except the first one (header row)
                    dataTable.clear().draw();
                    // // Clear existing table rows except the first one (header row)
                    // $("tbody tr:not(:first)").remove();

                    // Iterate over the response and append data to the table
                    $.each(response, function(index, plan) {

                        if (plan.length === 0) {
                            return true; // Skip to the next iteration
                        }
                        // Create a new table row
                        var newRow = $("<tr>");

                        var custom_tier;
                        @if(session('locale')=='fr')
                            if(plan[0].tier == 'Crown Jewels'){
                                custom_tier = "Les joyaux de la couronne";
                            }else if(plan[0].tier == 'tier 1'){
                                custom_tier = "niveau 1";
                            }else if(plan[0].tier == 'tier 2'){
                                custom_tier = "niveau 3";
                            }else if(plan[0].tier == 'tier 3'){
                                custom_tier = "niveau 3";
                            }
                        @else
                            custom_tier = plan[0].tier;
                        @endif

                        // Append table cells with data
                        newRow.append("<td>" + plan[0].name + "</td>");
                        newRow.append("<td>" + custom_tier + "</td>");

                        $.each(plan, function(key, plans) {
                            @if(session('locale')=='fr')
                                if (plans.rating == "Marginal") {
                                    rate = "Marginale";
                                } else if (plans.rating == "Weak") {
                                    rate = "Faible";
                                } else if (plans.rating == "Good") {
                                    rate = "Bonne";
                                } else if (plans.rating == "Satisfactory") {
                                    rate = "Satisfaisante";
                                } else if (plans.rating == "N/A") {
                                    rate = "N/A";
                                }
                            @else
                                rate=plans.rating;
                            @endif
                            newRow.append("<td style='background:" + plans.color + " !important; color:" + plans.text_color + " !important'>" + rate + "</td>");
                        });


                        // Append the new row to the DataTable
                        dataTable.row.add(newRow).draw();
                    });


                    // For Tier
                    let Crown_Jewels = 0;
                    let tier1 = 0;
                    let tier2 = 0;
                    let tier3 = 0;

                    var tierchart = [
                        ['Tier', 'Count'],
                    ];
                    const arrays = Object.values(response);

                    arrays?.forEach((item) => {
                        // console.log('jjjj', item)
                        if (item?.length) {
                            // console.log('tierrrrrrr', item[0].tier)
                            if (item[0].tier == 'tier 1') {
                                tier1 += 1;
                                // console.log("9---------", tier1)
                            }
                            if (item[0].tier == 'tier 2') {
                                tier2 += 1;
                            }
                            if (item[0].tier == 'tier 3') {
                                tier3 += 1;
                            }
                            if (item[0].tier == 'Crown Jewels') {
                                Crown_Jewels += 1;
                            }
                        }
                    })
                    const tier = {
                        Crown_Jewels: Crown_Jewels,
                        tier1: tier1,
                        tier2: tier2,
                        tier3: tier3,
                    };
                    // console.log('zetiertiertieree', tier)

                    @if(session('locale')=='fr')
                    tierchart.push(['Les joyaux de la couronne', tier.Crown_Jewels]);
                    tierchart.push(['Niveau 1', tier.tier1]);
                    tierchart.push(['Niveau 2', tier.tier2]);
                    tierchart.push(['Niveau 3', tier.tier3]);
                    @else
                    tierchart.push(['Crown Jewels', tier.Crown_Jewels]);
                    tierchart.push(['Tier 1', tier.tier1]);
                    tierchart.push(['Tier 2', tier.tier2]);
                    tierchart.push(['Tier 3', tier.tier3]);
                    @endif


                    //for hosting

                    let cloud = 0;
                    let premise = 0;
                    let nsure = 0;
                    let hybrid = 0;

                    var hostchart = [
                        ['Hosting', 'Count'],
                    ];

                    arrays?.forEach((item) => {
                        // console.log('jjjj', item)
                        if (item?.length) {
                            // console.log('tierrrrrrr', item[0].hosting_type)
                            if (item[0].hosting_type == 'Cloud') {
                                cloud += 1;
                                // console.log("9---------", tier1)
                            }
                            if (item[0].hosting_type == 'On-Premise') {
                                premise += 1;
                            }
                            if (item[0].hosting_type == 'Not Sure') {
                                nsure += 1;
                            }
                            if (item[0].hosting_type == 'Hybrid') {
                                hybrid += 1;
                            }
                        }
                    })
                    const host = {
                        cloud: cloud,
                        premise: premise,
                        nsure: nsure,
                        hybrid: hybrid,
                    };
                    // console.log('zetiertiertieree', tier)
                    @if(session('locale')=='fr')
                    hostchart.push(['Nuage', host.cloud]);
                    hostchart.push(['Sur site', host.premise]);
                    hostchart.push(['Pas certain', host.nsure]);
                    hostchart.push(['Hybride', host.hybrid]);
                    @else
                    hostchart.push(['Cloud', host.cloud]);
                    hostchart.push(['On-Premise', host.premise]);
                    hostchart.push(['Not Sure', host.nsure]);
                    hostchart.push(['Hybrid', host.hybrid]);
                    @endif

                    const dataArray = Object.values(response)
                    const countriesObject = {}
                    dataArray?.forEach((item) => {
                        if (item?.length) {
                            countriesObject[item[0]?.country] = 0
                        }
                    })
                    dataArray?.forEach((item) => {
                        if (item?.length) {
                            countriesObject[item[0]?.country] += 1
                        }
                    })
                    var country = [
                        ['Country', 'Value']
                    ];
                    Object.keys(countriesObject)?.forEach((item) => {
                        country.push([item, countriesObject[item]])
                    })
                    console.log('edfs', country)
                    drawChart(tierchart);
                    drawCharts(hostchart);
                    drawChartsz(country);

                },
                error: function(xhr, status, error) {
                    // Handle the error
                    console.error(error);
                }
            });
        });
    });
</script>


    <!-- Core plugin JavaScript-->

    <script src="{{ url('frontend/js/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->

    <script src="{{ url('frontend/js/sb-admin-2.min.js') }}"></script>

    <!-- Page level plugins -->

    <script src="{{ url('frontend/js/Chart.min.js') }}"></script>

    <!-- Page level custom scripts -->

    <script src="{{ url('frontend/js/chart-area-demo.js') }}"></script>

    <script src="{{ url('frontend/js/chart-pie-demo.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.3.1/tinymce.min.js"></script>
    <script src="{{ url('assets-new/js/main.js') }}"></script>

    <!-- Datatables scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.3.1/tinymce.min.js"></script>
    

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-html5-1.6.1/datatables.min.js"></script>
<!-- @if(Request::segment(1) == 'dash')
<script>
    $(document).ready(function() {

        if ($.fn.DataTable.isDataTable('#datatable')) {
        // If DataTable is already initialized, destroy it
        $('#datatable').DataTable().destroy();
        }
        
        $('#datatable').DataTable({
        "order": [],
        "language": {
            "search": "",
            "searchPlaceholder": "Search Here"
        }
        });
    });
    </script>
@endif -->

    <script>
        // JavaScript function to toggle the collapse
        function toggleCollapse(targetId) {
            var targetElement = document.getElementById(targetId);
            if (targetElement) {
                targetElement.classList.toggle('show');
            }
        }
    </script>
    <script>
        window.addEventListener("load", function() {
            var imgs = document.querySelectorAll("img");
            for (var a = 0; a < imgs.length; a++) {
                var src = imgs[a].getAttribute("src");
                imgs[a].setAttribute("onerror", src);
                imgs[a].setAttribute("src", imgs[a].getAttribute("src").replace("/img/", "/public/img/"));
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            $('.overlay_sidebar').click(function() {
                $('.sidebar').removeClass('toggled');
                $('body').removeClass('sidebar-toggled');
            });
        });
    </script>
    @stack('scripts')
</body>



</html>