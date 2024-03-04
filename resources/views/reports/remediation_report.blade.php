@extends('admin.client.client_app')
@section('page_title')
{{ __('Post Remediation Report') }}
@endsection
@section('content')
<style>
    body{
        color:black;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <h4 style="color:black;"><b>{{$group[0]->group_name}} - Assessment</b></h4>
    </div>
    <div class="row">
        <div class="col-md-2 p-4">
            <h5><b>Business Unit</b></h5>
            @php
                $existingUnits = [];
            @endphp
            @foreach ($remediation_plans as $subform => $plans)
                @if (count($plans) > 0)
                    @if (!in_array($plans[0]->business_unit, $existingUnits) && $plans[0]->business_unit!=null)
                        <input type="checkbox" class="checkbox-group" value="{{$plans[0]->business_unit}}"> {{$plans[0]->business_unit}}<br>
                        @php
                            $existingUnits[] = $plans[0]->business_unit;
                        @endphp
                    @endif
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
                $existingUnits = [];
            @endphp
            @foreach ($remediation_plans as $subform => $plans)
                @if (count($plans) > 0)
                    @if (!in_array($plans[0]->classification_name_en, $existingUnits) && $plans[0]->classification_name_en!=null)
                        <input type="checkbox" class="checkbox-group" value="{{$plans[0]->classification_name_en}}"> {{$plans[0]->classification_name_en}}<br>
                        @php
                            $existingUnits[] = $plans[0]->classification_name_en;
                        @endphp
                    @endif
                @endif
                
            @endforeach
        </div>
        <div class="col-md-3">
            <div id="chart-container"></div>
        </div>
    </div>
    <div class="row mt-3">
        <table class="table table-bordered table-sm" cellspacing="0"
        width="100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Business Unit</th>
                    <th>Tiering</th>
                    @foreach($data as $question)
                        <th>C{{$loop->iteration}} - {{$question->question_short}}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($remediation_plans as $subform => $plans)
                <tr>
                    @if (count($plans) > 0)
                        @if($plans[0]->other_id)
                            <td>{{$plans[0]->other_id}}</td>
                            <td></td>
                            <td></td>
                        @else
                            <td>{{$plans[0]->name}}</td>
                            <td>{{$plans[0]->business_unit}}</td>
                            <td>{{$plans[0]->tier}}</td>
                        @endif
                    @endif
                    
                    @foreach ($plans as $plan)
                        @if($plan->post_remediation_rating)
                            @php
                                $check= DB::table('evaluation_rating')->where('id',$plan->post_remediation_rating)->first();
                            @endphp
                            <td style="color:{{$check->text_color}}; background-color:{{$check->color}};">{{$check->rating}}</td>
                        @else
                            <td style="color:#fff; background-color:#73726e;">--</td>
                            <!-- <td style="color:{{$plan->text_color}}; background-color:{{$plan->color}};">{{$plan->rating}}</td> -->
                        @endif
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

<!-- counts -->
<?php
// Assuming you have an array of data in your Laravel controller
$chartData = [
    ['Tier', 'Tier Value'],
];
$impData = [
    ['impact', 'Value'],
];
?>
@foreach ($remediation_plans as $subform => $plans)
    @if (count($plans) > 0 && isset($plans[0]->classification_name_en))
        @php
            $name = $plans[0]->classification_name_en;
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
                @if (isset($count[0]->classification_name_en) && $name == $count[0]->classification_name_en)
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
@foreach ($remediation_plans as $subform => $plans)
    @if (count($plans) > 0 && isset($plans[0]->impact_name_en))
        @php
            $name = $plans[0]->impact_name_en;
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
                @if (isset($count[0]->impact_name_en) && $name == $count[0]->impact_name_en)
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



<!-- jQuery -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<script type="text/javascript">
    
    // First Chart 
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
          is3D: true,
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
          title: 'Assets by Impact',
          titleTextStyle: { fontSize: 16 },
          pieHole: 0.5,
          backgroundColor: 'transparent',
          is3D: true,
          chartArea: { left: 0, top: 40, width: '100%', height: '100%' }, // Add this line to remove margin and padding
          margin: 0, // Add this line to remove margin
          padding: 0 
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart-container'));
        chart.draw(data, options);
      }


</script>
@endsection