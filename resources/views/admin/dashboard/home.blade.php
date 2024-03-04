@extends('admin.layouts.app')



@section('content')

     <div class="app-title">

       

        <ul class="app-breadcrumb breadcrumb">

          <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>

          <li class="breadcrumb-item"><a href="#">Dashboard</a></li>

        </ul>

      </div>

      <div class="row">

        <div class="col-md-6 col-lg-3">

          <div class="widget-small primary coloured-icon"><i class="icon fa fa-users fa-3x"></i>

            <div class="info">

              <h4>Users</h4>

              <p><b>{{$total_users}}</b></p>

            </div>

          </div>

        </div>

        <div class="col-md-6 col-lg-3">

          <div class="widget-small info coloured-icon"><i class="icon fa fa-thumbs-o-up fa-3x"></i>

            <div class="info">

              <h4>Plans</h4>

              <p><b></b></p>

            </div>

          </div>

        </div>

        <div class="col-md-6 col-lg-3">

          <div class="widget-small warning coloured-icon"><i class="icon fa fa-users fa-3x"></i>

            <div class="info">

              <h4>Drivers</h4>

              <p><b></b></p>

            </div>

          </div>

        </div>

        <div class="col-md-6 col-lg-3">

          <div class="widget-small danger coloured-icon"><i class="icon fa fa-star fa-3x"></i>

            <div class="info">

              <h4>Cars</h4>

              <p><b></b></p>

            </div>

          </div>

        </div>

      </div>

      <div class="row">

        <div class="col-md-6">

          <div class="tile">

            <h3 class="tile-title">Montly Plans</h3>

            <div class="embed-responsive embed-responsive-16by9">

              <canvas class="embed-responsive-item" id="lineChartDemo"></canvas>

            </div>

          </div>

        </div>

        <div class="col-md-6">

          <div class="tile">

            <h3 class="tile-title">Plan Status</h3>

            <div class="embed-responsive embed-responsive-16by9">

              <canvas class="embed-responsive-item" id="pieChartDemo"></canvas>

            </div>

          </div>

        </div>

      </div>

      





       <script type="text/javascript" src="{{url('backend/js/plugins/chart.js')}}"></script>

    <script type="text/javascript">

      var data = {

      	labels: ["January", "February", "March", "April", "May","June","July", "Auguest", "September", "October", "November", "December"],

      	datasets: [

      		{

      			label: "Plan1",

      			fillColor: "rgba(220,220,220,0.2)",

      			strokeColor: "rgba(220,220,220,1)",

      			pointColor: "rgba(220,220,220,1)",

      			pointStrokeColor: "#fff",

      			pointHighlightFill: "#fff",

      			pointHighlightStroke: "rgba(220,220,220,1)",

      			data: [65, 59, 80, 81, 56,23,45,109,68,89,23,100]

      		},

      		{

      			label: "Plan2",

      			fillColor: "rgba(151,187,205,0.2)",

      			strokeColor: "rgba(151,187,205,1)",

      			pointColor: "rgba(151,187,205,1)",

      			pointStrokeColor: "#fff",

      			pointHighlightFill: "#fff",

      			pointHighlightStroke: "rgba(151,187,205,1)",

      			data: [28, 48, 40, 19, 86, 80, 81, 56,23,45,56,90]

      		}

      	]

      };

      var pdata = [

      	{

      		value: 200,

      		color: "#46BFBD",

      		highlight: "#5AD3D1",

      		label: "Upcoming"

      	},

      	{

      		value: 112,

      		color:"#F7464A",

      		highlight: "#FF5A5E",

      		label: "In-Progress"

      	}

        

      ]

      

      var ctxl = $("#lineChartDemo").get(0).getContext("2d");

      var lineChart = new Chart(ctxl).Line(data);

      

      var ctxp = $("#pieChartDemo").get(0).getContext("2d");

      var pieChart = new Chart(ctxp).Pie(pdata);

    </script>



    

@endsection

