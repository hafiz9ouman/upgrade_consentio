@extends('admin.client.client_app')
@section('page_title')
{{ __('Remediation Report') }}
@endsection
@section('content')
<style>
    body{
        color:black;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <h4 style="color:black;"><b>Remediation Reports</b></h4>
    </div>
    <div class="row">
        <div class="col-md-2 p-4">
            <h5><b>Business Unit</b></h5>
            @php
                $existingUnits = [];
            @endphp
            @foreach ($remediation_plans as $plans)
                
                    @if (!in_array($plans->business_unit, $existingUnits) && $plans->business_unit!=null)
                        <input type="checkbox" class="checkbox-group" value="{{$plans->business_unit}}"> {{$plans->business_unit}}<br>
                        @php
                            $existingUnits[] = $plans->business_unit;
                        @endphp
                    @endif
                
                
            @endforeach
        </div>
        <div class="col-md-4">
            <div id="chart-status"></div>
        </div>
        <div class="col-md-3">
            <div id="chart"></div>
        </div>
        <div class="col-md-3">
            <div id="chart-container"></div>
        </div>
    </div>
    <div class="row mt-3">
        <table class="table table-bordered table-sm" cellspacing="0"
  width="100%">
            <thead>
                    <th>Name</th>
                    <th>Control ID</th>
                    <th>Control Title</th>
                    <th>Proposed Remediation </th>
                    <th>Completed Actions </th>
                    <th>ETA</th>
                    <th>Person In Charge </th>
                    <th>Remediation status</th>
                    <th>Initial Rating</th>
                    <th>POST Rating</th>
                </thead>
            <tbody>
                @foreach($remediation_plans as $plan)
                    <tr>
                    <td>
                        @if($plan->asset_name)
                            {{$plan->asset_name}}
                        @else
                            {{$plan->other_id}}
                        @endif
                    </td>
                    <td>{{$plan->control_id}}</td>
                    <td>{{$plan->question_short}}</td>
                    <td>
                        @if($plan->proposed_remediation)
                            {{$plan->proposed_remediation}}
                        @else
                            <span style="margin-left:47%;">--</span>
                        @endif
                    </td>
                    <td>
                        @if($plan->completed_actions)
                            {{$plan->completed_actions}}
                        @else
                            <span style="margin-left:47%;">--</span>
                        @endif
                    </td>
                    <td>
                        @if($plan->eta)
                            {{$plan->eta}}
                        @else
                            <span style="margin-left:47%;">--</span>
                        @endif
                    </td>
                    <td>{{$plan->user_name}}</td>
                    <td>
                        @if($plan->status == "0")
                            <span style="margin-left:47%;">--</span>
                        @else
                            {{$plan->status}}
                        @endif
                    </td>
                    @php
                        $check=DB::table('evaluation_rating')->where('id', $plan->rating)->first();
                    @endphp
                    <td style="background:{{$check->color}};color:{{$check->text_color}}">
                        {{$check->rating}}
                    </td>
                    <?php
                        $var = DB::table('evaluation_rating')->where('id', $plan->post_remediation_rating)->first();
                    ?>
                    <td style="background:<?php
                        if ($var) {
                            echo $var->color;
                        }
                        ?>; color:<?php
                        if ($var) {
                            echo $var->text_color;
                        }
                        ?>">
                    <?php
                        if ($var) {
                            echo $var->rating;
                        }
                        ?>
                    </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- counts -->
<?php
// Assuming you have an array of data in your Laravel controller
$chartStatus = [
    ['Tier', 'Tier Value'],
];
$chartData = [
    ['Rating', 'Value'],
];
$impData = [
    ['Postrat', 'Value'],
];
?>
<!-- For Pre-Remediation -->
@foreach ($remediation_plans as $plans)
    @if (isset($plans->status))
        @php
            $name = ($plans->status === '0' || $plans->status === null) ? 'Blank' : $plans->status;
            $datacount = 0;
            $exists = false;
        @endphp

        @foreach ($chartStatus as $entry)
            @if ($entry[0] == $name)
                @php
                    $exists = true;
                    break;
                @endphp
            @endif
        @endforeach

        @if (!$exists)
            @foreach ($chartData as $entry)
                @if ($entry[0] == $name)
                    @php
                        $datacount = $entry[1];
                        break;
                    @endphp
                @endif
            @endforeach

            @if ($datacount == 0)
                @php
                    $remediation_plans_count = $remediation_plans->where('status', $plans->status)->count();
                    $chartStatus[] = [$name, $remediation_plans_count];
                @endphp
            @endif
        @endif
    @endif
@endforeach


@php
    echo json_encode($chartStatus);
@endphp

<!-- For Pre-Remediation -->
@foreach ($remediation_plans as $plans)
    @if (isset($plans->rating))
        @php
            $check = DB::table('evaluation_rating')->where('id', $plans->rating)->first();
        @endphp
        @php
            $name = $check->rating;
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
            @php
                $remediation_plans_count = $remediation_plans->where('rating', $plans->rating)->count();
                $chartData[] = [$name, $remediation_plans_count];
            @endphp
        @endif
    @endif
@endforeach

<!-- @php
    echo json_encode($chartData);
@endphp -->

<!-- For Post-Remediation -->
@foreach ($remediation_plans as $plans)
    @php
        $postRating = isset($plans->post_remediation_rating) ? $plans->post_remediation_rating : null;
    @endphp

    @php
        $check = DB::table('evaluation_rating')->where('id', $postRating)->first();
    @endphp

    @php
        $name = $check ? $check->rating : 'Blank';
        $datacount = 0;
        $exists = false;
    @endphp

    @foreach ($impData as $entry)
        @if ($entry[0] == $name)
            @php
                $exists = true;
                break;
            @endphp
        @endif
    @endforeach

    @if (!$exists)
        @php
            $datacount = $remediation_plans->where('post_remediation_rating', $postRating)->count();
            $impData[] = [$name, $datacount];
        @endphp
    @endif
@endforeach




<!-- @php
    echo json_encode($impData);
@endphp -->







<!-- jQuery -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<script type="text/javascript">
    
    // Status Chart 
      google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChartstatus);
      function drawChartstatus() {
        var chartData = @json($chartStatus);

        // Create an empty array to hold the dynamic data
        var dynamicData = [];

        // Add each row of data to the dynamicData array using a foreach loop
        chartData.forEach(function(row) {
            dynamicData.push(row);
        });

        // Create the data table using the dynamicData array
        var data = google.visualization.arrayToDataTable(dynamicData);

        var options = {
          title: 'Application by Evaluation Pre-Remediation',
          titleTextStyle: { fontSize: 16 },
          pieHole: 0.5,
          is3D: true,
          backgroundColor: 'transparent',
          chartArea: { left: 0, top: 40, width: '100%', height: '100%' }, // Add this line to remove margin and padding
          margin: 0, // Add this line to remove margin
          padding: 0 
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart-status'));
        chart.draw(data, options);
      }

    // First Chart 
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
          title: 'Application by Evaluation Pre-Remediation',
          titleTextStyle: { fontSize: 16 },
          pieHole: 0.5,
          is3D: true,
          backgroundColor: 'transparent',
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
        var chartData = @json($impData);

        // Create an empty array to hold the dynamic data
        var dynamicData = [];

        // Add each row of data to the dynamicData array using a foreach loop
        chartData.forEach(function(row) {
            dynamicData.push(row);
        });

        // Create the data table using the dynamicData array
        var data = google.visualization.arrayToDataTable(dynamicData);

        var options = {
          title: 'Application by Evaluation Post-Remediation',
          titleTextStyle: { fontSize: 16 },
          pieHole: 0.5,
          is3D: true,
          backgroundColor: 'transparent',
          chartArea: { left: 0, top: 40, width: '100%', height: '100%' }, // Add this line to remove margin and padding
          margin: 0, // Add this line to remove margin
          padding: 0 
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart-container'));
        chart.draw(data, options);
      }


</script>
@endsection