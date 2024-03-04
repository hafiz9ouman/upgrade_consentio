@extends('admin.client.client_app')
@section('page_title')
{{ __('Reports') }}
@endsection
@section('content')
<style>
    body{
        color:black;
    }
</style>

<div class="container">
    <div class="row">
        <h4 style="color:black;"><b>Critical Assets by Cyber Security Assessment</b></h4>
    </div>
    <div class="row">
        <div class="col-md-2 p-4">
            <h5><b>Business Unit</b></h5>
            @php
                $existingUnits = [];
            @endphp
            @foreach($data as $bu)
                @if (!in_array($bu->business_unit, $existingUnits))
                    <input type="checkbox" name="hello" id=""> {{$bu->business_unit}}<br>
                    @php
                        $existingUnits[] = $bu->business_unit;
                    @endphp
                @endif
            @endforeach
        </div>
        <div class="col-md-2 p-4" id="tier-section">
            <h5><b>Tier</b></h5>
            <input type="checkbox" name="tier[]" id="tier1" value="tier 1"> Tier 1<br>
            <input type="checkbox" name="tier[]" id="tier2" value="tier 2"> Tier 2<br>
            <input type="checkbox" name="tier[]" id="tier3" value="tier 3"> Tier 3
        </div>
        <div class="col-md-3">
            <div id="chart"></div>
        </div>
        <div class="col-md-2 p-4">
            <h5><b>Data Classification</b></h5>
            @php
                $exist_class = [];
            @endphp
            @foreach($data as $datac)
                @if(!in_array($datac->classification_name_en, $exist_class))
                    <input type="checkbox" name="hello" id=""> {{$datac->classification_name_en}}<br>
                    @php
                        $exist_class[] = $datac->classification_name_en;
                    @endphp 
                @endif
            @endforeach
        </div>
        <div class="col-md-3">
            <div id="chart-container"></div>
        </div>
    </div>
    <div class="row mt-3">
        <table>
            <thead>
                <th>Name</th>
                <th>Asset Type</th>
                <th>Hosting</th>
                <th>Provider</th>
                <th>Country</th>
                <th>State</th>
                <th>City</th>
                <th>Data Classification</th>
                <th>Impact</th>
                <th>Tier</th>
                <th>Business Unit</th>
                <th>Business Owner</th>
                <th>Internal 3rd Party</th>
                <th>Data Subject Volume</th>
            </thead>
            <tbody>
                @foreach($data as $datas)
                    <tr>
                        <td>{{$datas->name}}</td>
                        <td>{{$datas->asset_type}}</td>
                        <td>{{$datas->hosting_type}}</td>
                        <td>{{$datas->hosting_provider}}</td>
                        <td>{{$datas->country}}</td>
                        <td>{{$datas->state}}</td>
                        <td>{{$datas->city}}</td>
                        <td>{{$datas->tier}}</td>
                        <td>{{$datas->business_unit}}</td>
                        <td>{{$datas->business_owner}}</td>
                        <td>{{$datas->data_classification_id}}</td>
                        <td>{{$datas->internal_3rd_party}}</td>
                        <td>{{$datas->data_subject_volume}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<!-- counts -->
<?php
// Assuming you have an array of data in your Laravel controller
$chartData = [
    ['Tier', 'Tier Value'],
    
    
];

foreach ($data as $datas) {
    $name = $datas->classification_name_en;
    $datacount = 0;
    foreach ($chartData as $entry) {
        if ($entry[0] == $name) {
            $datacount = $entry[1];
            break;
        }
    }
    if ($datacount == 0) {
        foreach ($data as $count) {
            if ($name == $count->classification_name_en) {
                $datacount++;
            }
        }
        $chartData[] = [$name, $datacount];
    }
}

?>



<!-- Pie charts -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
      google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var chartData = @json($chartData);

        // Create an empty array to hold the dynamic data
        var dynamicData = [];

        // Add each row of data to the dynamicData array using a foreach loop
        chartData.forEach(function(row) {
            dynamicData.push(row);
        });

        // Create the data table using the dynamicData array
        var data = google.visualization.arrayToDataTable(dynamicData);

        var options = {
          title: 'Assets by Data Classification',
          titleTextStyle: { fontSize: 16 },
          pieHole: 0.5,
          backgroundColor: 'transparent',
          legend: 'none',
          chartArea: { left: 0, top: 40, width: '100%', height: '100%' }, // Add this line to remove margin and padding
          margin: 0, // Add this line to remove margin
          padding: 0 
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart'));
        chart.draw(data, options);

        
      }
    //   Second Charts
      google.charts.setOnLoadCallback(drawCharts);
      function drawCharts() {
        var data = google.visualization.arrayToDataTable([
          ['Tier', 'Tier Value'],
          ['Work',     17],
          ['Eat',      25],
          ['Commute',  24],
          ['Watch TV', 28],
          ['Sleep',    12]
        ]);

        var options = {
          title: 'Assets by Impact',
          titleTextStyle: { fontSize: 16 },
          pieHole: 0.5,
          backgroundColor: 'transparent',
          legend: 'none',
          chartArea: { left: 0, top: 40, width: '100%', height: '100%' }, // Add this line to remove margin and padding
          margin: 0, // Add this line to remove margin
          padding: 0 
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart-container'));
        chart.draw(data, options);
      }

      /////////
        $(document).ready(function() {
            $('input[name="tier[]"]').change(function() {
                var selectedTiers = [];
                
                // Loop through each checkbox and check if it's selected
                $('input[name="tier[]"]:checked').each(function() {
                    selectedTiers.push($(this).val());
                });
                
                // Send the selected tiers using AJAX
                $.ajax({
                    type: 'POST',
                    url: 'your_server_url',
                    data: { tiers: selectedTiers },
                    success: function(response) {
                        // Handle the response from the server
                        console.log(response);
                    },
                    error: function(xhr, status, error) {
                        // Handle the error
                        console.log(error);
                    }
                });
            });
        });

</script>
@endsection