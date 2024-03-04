@extends('admin.client.client_app')
@section('content')
<style>
    .orange_c0lor{
  background-color: #f26924;
  color: #fff;
}
.blue_c0lor,.active{
  background-color: #0f75bd;
  color: #fff;
}

.tablinks {
	cursor:pointer;
}

/* Style the tab content */
.tabcontent {
  display: none;
  border: 1px solid #ccc;
  border-top: none;
}
#cust-data-table{
    overflow:auto;
}
#cust-data-table table td:first-child,#cust-data-table table th:first-child{
  width:33%;
  text-align: left !important;
}
#cust-data-table .table td,#cust-data-table  .table th{
  text-align: center;
  border: 0;
}
.table thead th{
  border-bottom: 0;
  padding: 4px 12px;
}
.table tbody tr{
  border-bottom: 1px solid #eee;
}
.table{
  margin: 0;
  font-size: 13px;
  white-space: nowrap;
}

</style>
<div class="row" style="padding-left:20px; padding-right:12px;">
	<div class="col-md-12">
		<div class="tile">
			<h3 class="tile-title">Summary Reports</h3>
		</div>
		
		<div id="cust-data-table">
		    <div class="table-responsive">
                <table class="table ">
                  <thead>
                    <!-------------------main-head--------->
                    <tr class="orange_c0lor">
                      <th>Department</th>
                      <th class="tablinks" onclick="openCity(event, 'user1')" id="defaultOpen">Finance</th>
                      <th class="tablinks" onclick="openCity(event, 'user2')">HR</th>
                      <th>PR</th>
                      <th>Marketing</th>
                      <th>Legal</th>
                      <th>Madano</th>
                      <th>Insights</th>
                      <th>Insights</th>
                      <th>Insights</th>
                    </tr>
                    <tr class="blue_c0lor">
                      <th>Person who completed the Form</th>
                      <th>user 1</th>
                      <th>user 2</th>
                      <th>user 3</th>
                      <th>user 4</th>
                      <th>user 5</th>
                      <th>user 6</th>
                      <th>user 7</th>
                      <th>user 8</th>
                      <th>user 9</th>
                    </tr>
                    <!------------------------------------>
                  </thead>
                </table>
              </div>

              <div id="user1" class="tabcontent table-responsive">
                <table class="table ">
                  <tbody >
                    <tr>
                      <td>Type of private data processed</td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                    </tr>
                    <!---------head------->
                    <tr class="blue_c0lor">
                      <th>Background Checks</th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                    </tr>
                    <!---------------------->
                    <tr>
                      <td>References and referee details</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                    </tr>
                    <tr>
                      <td>References and referee details</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                    </tr>
                    <!------------head--------->
                    <tr class="blue_c0lor">
                      <th>Education</th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                    </tr>
                    <!-------------------------->
                    <tr>
                      <td>Grade</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                    </tr>
                    <!------------head------------->
                     <tr class="blue_c0lor">
                      <th>Personal Identification</th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                    </tr>
                    <!--------------------------->
                    <tr>
                      <td>Full name</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                    </tr>
                    <!------------head------------>
                     <tr class="blue_c0lor">
                      <th>Lawfulness (What allows you to have the data)</th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                    </tr>
                    <!-------------------------->
                    <tr>
                      <td>Performance contrat (operational requirement)</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                    </tr>
                    <!-------------head---------->
                    <tr class="blue_c0lor">
                      <th>Collection Method (How is the data collected)</th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                    </tr>
                    <!-------------------------->
                    <tr>
                      <td>Electronic forms</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                      <td>one</td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div id="user2" class="tabcontent table-responsive">
                <table class="table ">
                  <tbody>
                    <tr>
                      <td>Type of private data processed</td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                    </tr>
                    <!-------------head---------->
                    <tr class="blue_c0lor">
                      <th>Background Checks</th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                    </tr>
                    <!---------------------->
                    <tr>
                      <td>References and referee details</td>
                      <td>two</td>
                      <td>two</td>
                      <td>two</td>
                      <td>two</td>
                      <td>two</td>
                      <td>two</td>
                      <td>two</td>
                      <td>two</td>
                      <td>two</td>
                    </tr>
                    <!-------------head---------->
                    <tr class="blue_c0lor">
                      <th>Education</th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                    </tr>
                    <!-------------------------->
                    <tr>
                      <td>Grade</td>
                      <td>two</td>
                      <td>two</td>
                      <td>two</td>
                      <td>two</td>
                      <td>two</td>
                      <td>two</td>
                      <td>two</td>
                      <td>two</td>
                      <td>two</td>
                    </tr>
                    <!-------------head---------->
                     <tr class="blue_c0lor">
                      <th>Personal Identification</th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                    </tr>
                    <!---------------------------->
                    <tr>
                      <td>Full name</td>
                      <td>two</td>
                      <td>two</td>
                      <td>two</td>
                      <td>two</td>
                      <td>two</td>
                      <td>two</td>
                      <td>two</td>
                      <td>two</td>
                      <td>two</td>
                    </tr>
                    <!-------------head---------->
                     <tr class="blue_c0lor">
                      <th>Lawfulness (What allows you to have the data)</th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                    </tr>
                    <!----------------------->
                    <tr>
                      <td>Performance contrat (operational requirement)</td>
                      <td>two</td>
                      <td>two</td>
                      <td>two</td>
                      <td>two</td>
                      <td>two</td>
                      <td>two</td>
                      <td>two</td>
                      <td>two</td>
                      <td>two</td>
                    </tr>
                    <!-------------head---------->
                    <tr class="blue_c0lor">
                      <th>Collection Method (How is the data collected)</th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                    </tr>
                    <!----------------------->
                    <tr>
                      <td>Electronic forms</td>
                      <td>two</td>
                      <td>two</td>
                      <td>two</td>
                      <td>two</td>
                      <td>two</td>
                      <td>two</td>
                      <td>two</td>
                      <td>two</td>
                      <td>two</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>


<script>
function openCity(evt, cityName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
}

// Get the element with id="defaultOpen" and click on it
document.getElementById("defaultOpen").click();
</script>
   
		
	</div>
</div>
@endsection