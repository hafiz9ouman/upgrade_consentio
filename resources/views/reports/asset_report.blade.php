@extends('admin.client.client_app')
@section('page_title')
{{ __('Audit Report') }}
@endsection
@section('content')
<style>
    body {
        color: black;
    }
</style>

<div class="container-fluid" style="background-color: white;">
    <div class="row">
        <h4 class="mt-3" style="color:black;"><b>{{$company->name}} {{$group[0]->group_name}} - Security Assessment</b></h4>
    </div>
    <input type="hidden" class="group_id" value="{{$group_id}}">
    <div class="row">
        <div class="col-md-3">
            <div id="chart"></div>
        </div>
        <div class="col-md-5 pt-4">
            <div class="row">
            <div class="col">
                <h5><b>Data Classification</b></h5>
                @php
                $existingUnits = [];
                @endphp
                @foreach ($remediation_plans as $subform => $plans)
                @if (count($plans) > 0)
                @if (!in_array($plans[0]->classification_name_en, $existingUnits) && $plans[0]->classification_name_en!=null)
                <input type="checkbox" id="checkbox-group" class="class-group change" value="{{$plans[0]->classification_name_en}}"> {{$plans[0]->classification_name_en}}<br>
                @php
                $existingUnits[] = $plans[0]->classification_name_en;
                @endphp
                @endif
                @endif

                @endforeach
            </div>
            <div class="col">
                <h5><b>Impact</b></h5>
                @php
                $existingUnits = [];
                $counter = 1;
                @endphp
                @foreach ($remediation_plans as $subform => $plans)
                @if (count($plans) > 0)
                @if($plans[0]->impact_name_en)
                @if (!in_array($plans[0]->impact_name_en, $existingUnits) && $plans[0]->impact_name_en!=null)
                <input type="checkbox" id="checkbox-group" class="impact-group change" value="{{$plans[0]->impact_name_en}}"> {{$counter}} - {{$plans[0]->impact_name_en}}<br>
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
                <h5><b>Business Unit</b></h5>
                @php
                $existingUnits = [];
                @endphp
                @foreach ($remediation_plans as $subform => $plans)
                @if (count($plans) > 0)
                @if($plans[0]->business_unit)
                @if (!in_array($plans[0]->business_unit, $existingUnits) && $plans[0]->business_unit!=null)
                <input type="checkbox" id="checkbox-group" class="business-group change" value="{{$plans[0]->business_unit}}"> {{$plans[0]->business_unit}}<br>
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
        <table class="table table-striped table-bordered table-sm text-dark" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Asset Name</th>
                    <th>Asset Tier</th>
                    @foreach($data as $question)
                    <th>C{{$loop->iteration}} - {{$question->question_short}}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($remediation_plans as $subform => $plans)
                @if (count($plans) == 0)
                @continue;
                @endif
                <tr>
                    <td>{{$plans[0]->name}}</td>
                    <td>{{$plans[0]->tier}}</td>
                    @foreach ($plans as $plan)
                    <td style="color:{{$plan->text_color}}; background-color:{{$plan->color}};">{{$plan->rating}}</td>
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



<!-- jQuery -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<script type="text/javascript">
    // First Chart 
    google.charts.load("current", {
        packages: ["corechart"]
    });
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
            title: 'Assets Tier',
            titleTextStyle: {
                fontSize: 16
            },
            pieHole: 0.5,
            backgroundColor: 'transparent',
            is3D: true,
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
            title: 'Hosting Type',
            titleTextStyle: {
                fontSize: 16
            },
            pieHole: 0.5,
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
            title: 'Asset Hosting Location',
            titleTextStyle: {
                fontSize: 16
            },
            pieHole: 0.5,
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
                    $("tbody tr:not(:first)").remove();

                    // Iterate over the response and append data to the table
                    $.each(response, function(index, plan) {

                        if (plan.length === 0) {
                            return true; // Skip to the next iteration
                        }
                        // Create a new table row
                        var newRow = $("<tr>");

                        // Append table cells with data
                        newRow.append("<td>" + plan[0].name + "</td>");
                        newRow.append("<td>" + plan[0].tier + "</td>");

                        $.each(plan, function(key, plans) {
                            newRow.append("<td style='background:" + plans.color + "; color:" + plans.text_color + "'>" + plans.rating + "</td>");
                        });


                        // Append the new row to the tbody
                        $("tbody").append(newRow);
                    });


                    // For Tier
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
                        }
                    })
                    const tier = {
                        tier1: tier1,
                        tier2: tier2,
                        tier3: tier3,
                    };
                    // console.log('zetiertiertieree', tier)

                    tierchart.push(['Tier 1', tier.tier1]);
                    tierchart.push(['Tier 2', tier.tier2]);
                    tierchart.push(['Tier 3', tier.tier3]);


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

                    hostchart.push(['Cloud', host.cloud]);
                    hostchart.push(['On-Premise', host.premise]);
                    hostchart.push(['Not Sure', host.nsure]);
                    hostchart.push(['Hybrid', host.hybrid]);

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
@endsection