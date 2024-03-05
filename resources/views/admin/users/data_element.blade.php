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
<div class="row">
  <div class="col-md-12">
    <div class="tile">
   
      <div class="">


        @if(Session::has('message'))
           <p class="alert alert-info">{{ Session::get('message') }}</p>
        @endif
        @if(Session::has('alert'))
           <p class="alert alert-danger">{{ Session::get('alert') }}</p>
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
							       			<input type="text" name="name" class="form-control" placeholder="New Element" required>
							       		</div>
                         <div class="form-group">
							       			<label for="#">New Element French</label>
							       			<input type="text" name="name_fr" class="form-control" placeholder="New Element French" required>
							       		</div>
							       		<div class="form-group">
							       			<label for="#">Data Element Group</label>
							       			<select name="element_group" id="" class="form-control">
							       				@foreach($section as $val)
							       					<option value="{{$val->id}}" >{{$val->section_name}} ({{$val->section_name_fr}})</option>
							       				@endforeach
							       			</select>
							       		</div>
                        <div class="form-group">
                          <label for="#">Data Classification name</label>
                          <select name="d_c_name" id="" class="form-control">
                            @foreach($dc_result as $dc)
                              <option value="{{$dc->id}}" >{{$dc->classification_name_en}}({{$dc->classification_name_fr}})</option>
                            @endforeach
                          </select>
                        </div>
								  </div>
								      <div class="modal-footer">
								        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
								      </div>
							    	</form>  
							    </div>
							  </div>
							</div>

        <table class="table" id="forms-table">
          <thead class="back_blue">
            <tr>
              
              <th scope="col" col-span="2" >Element Name</th>
              <th scope="col" col-span="2" >Nom de l'élément</th>
              <th scope="col" col-span="2" >Group</th>
              <th scope="col" col-span="2" >Groupe</th>
              <th scope="col" col-span="2" >Classification</th>
              <th scope="col" col-span="2" >Classification</th>
              <th scope="col">Actions</th>

            </tr>
          </thead>
          <tbody>
              @foreach($data as $val)
                <tr>
                  <td class="w-25">{{$val->name}}</td>
                  <td class="w-25">{{$val->name_fr}}</td>
                  <td>{{$val->section_name}}</td>
                  <td>{{$val->section_name_fr}}</td>
                  <td>{{$val->classification_name_en}}</td>
                  <td>{{$val->classification_name_fr}}</td>
                   <td class="d-flex justify-content-around">
                      <a href="{{url('edit-data-element-group/'.$val->id)}}" class="" ><i class="fa fa-edit"></i></a>      
                      <a id="{{$val->id}}" class="delete-button text-danger" ><i class="fa fa-trash"></i></a>        
                  </td>
                </tr>
              @endforeach
          </tbody>
        </table>
         
      </div>
    </div>
  </div>
</div> 

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>
  $('.delete-button').on('click', function(){
    var id=this.id;
    console.log(id);
    Swal.fire({
            title: 'Are you sure?',
            text: 'You are Deleting the Data Element.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
          if(result.isConfirmed){
            $.ajax({
              url: "delete-data-element-group/" + id,
              method: "GET",
              success: function(response){
                console.log(response)
                if(response.status==200){
                  Swal.fire('Data Element is Deleted', '', 'success');
                  setTimeout( function () {
                    location.reload();
                  }, 2000 );
                }
                else{
                  Swal.fire('Section has only one Data Element', '', 'error');
                }
              },
            });
          }
        });
    
  });
</script>
 <script>
    $(document).ready(function(){
        $('#forms-table').DataTable({
                "order": [],
                "scrollX": true,
			          "autoWidth": false
        });

        $(function () {
        $('[data-toggle="tooltip"]').tooltip()
      })
    })
</script> 
@endif	
@endsection
