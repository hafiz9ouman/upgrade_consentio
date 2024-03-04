@extends( 'admin.layouts.admin_app' )
@section( 'content' )

	<style>
  @media screen and (max-width: 580px)
 {
   .add_responsive {
     overflow: scroll;
     display: block;
   }
 }

 </style>

  
 @if(auth()->user()->role == 1) 
<div class="row" style="margin-left:10px;">
  <div class="col-md-12">
    <div class="tile">
   
      <div class="table-responsive cust-table-width">


        @if(Session::has('message'))
           <p class="alert alert-info">{{ Session::get('message') }}</p>
        @endif
        <h3 class="tile-title">Data Element
          <button data-toggle="modal" data-target="#exampleModal" class="btn btn-sm btn-success pull-right cust_color" style="margin-right: 10px;"><i class="fa fa-plus" aria-hidden="true" ></i>Add New Element</button>
        </h3>
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
							  <div class="modal-dialog">
							    <div class="modal-content">
							      <div class="modal-header">
							        <h5 class="modal-title" id="exampleModalLabel">Data Element</h5>
							        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
							          <span aria-hidden="true">&times;</span>
							        </button>
							      </div>
							      <div class="modal-body">
							       	<form action="{{url('admin-data-element-group')}}" method="post">
							       		{{ csrf_field() }}
							       		<div class="form-group">
							       			<label for="#">New Element</label>
							       			<input type="text" name="new_element" class="form-control" placeholder="New Element">
							       		</div>
							       		<div class="form-group">
							       			<label for="#">Data Element Group</label>
							       			<select name="element_group" id="" class="form-control">
							       				@foreach($section as $val)
							       					<option value="{{$val->id}}" >{{$val->section_name}}</option>
							       				@endforeach
							       			</select>
							       		</div>
                        {{--<div class="form-group">
                          <label for="#">Data Classification name</label>
                          <select name="d_c_name" id="" class="form-control">
                            @foreach($dc_result as $dc)
                              <option value="{{$dc->id}}" >{{$dc->classificaion_name_en}}</option>
                            @endforeach
                          </select>
                        </div>--}}
								  </div>
								      <div class="modal-footer">
								        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
								        <button type="submit" class="btn btn-primary">Submit</button>
								      </div>
							    	</form>  
							    </div>
							  </div>
							</div>

        <table class="table" id="forms-table">
          <thead class="back_blue">
            <tr>
              
              <th scope="col" col-span="2" >Element Name </th>
              <th scope="col" col-span="2" >Data Element Group </th>
              <th scope="col" col-span="2" >Data Classification Name </th>
              <th scope="col">Actions</th>

            </tr>
          </thead>
          <tbody>
              @foreach($data as $val)
                <tr>
                  <td class="w-25">{{$val->name}}</td>
                  <td>
                      {{$val->section_name}}       
                  </td>
                  <td>
                    {{$val->classification_name_en}}
                  </td>
                   <td>
                      <a href="{{url('edit-data-element-group/'.$val->id)}}" class="btn btn-primary" >Edit</a>        
                  </td>
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
@endif	
@endsection
