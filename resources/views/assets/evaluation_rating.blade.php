@extends('admin.client.client_app')
@section('content')
<div class="row" style="margin-left:10px;">
  <div class="col-md-12">
    <div class="tile">
   
      <div class="table-responsive cust-table-width">

        <h3 class="tile-title">
          Evalution Rating
        </h3>
      	

        <table class="table" id="forms-table">
          <thead class="back_blue">
            <tr>
              
              <th scope="col" col-span="2" >Assessment</th>
              <th scope="col" col-span="2" >Rating</th>
              <th scope="col" col-span="2" >Background Color</th>
              <th scope="col" col-span="2" >Text Color</th>
              <th scope="col" col-span="2" >Action</th>

            </tr>
          </thead>
          <tbody>
            	@foreach($data as $val)
            	<tr>
            		<td>{{$val->assessment}}</td>
            		<td>{{$val->rating}}</td>
            		<td>{{$val->color}}</td>
            		<td>{{$val->text_color}}</td>
                <td><a href="{{url('edit-evalution/'.$val->id)}}" class="btn btn-primary">Edit</a></td>
            	</tr>
            	@endforeach
          </tbody>
        </table>
         
      </div>
    </div>
  </div>
</div> 
 <script>
    $(document).ready(function(){
        $('#forms-table').DataTable({
                "order": [[ 0, "desc" ]]
        });

        $(function () {
        $('[data-toggle="tooltip"]').tooltip()
      })
    })
</script> 
@endsection