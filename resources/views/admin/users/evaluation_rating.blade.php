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
        <h3 class="tile-title">Evaluation Rating</h3>

        <table class="table table-striped" id="forms-table">
          <thead class="back_blue">
            <tr>
              
              <th>Assessment </th>
              <th>Rating</th>
              <th>Background Color</th>
              <th>Text Color</th>
              <th>Actions</th>

            </tr>
          </thead>
          <tbody>
              @foreach($data as $val)
                <tr>
                  <td class="w-25">{{$val->assessment}}</td>
                  <td>{{$val->rating}}</td>
                  <td>{{$val->color}}</td>
                  <td>{{$val->text_color}}</td>
                   <td>
                      <a href="{{url('edit-evaluation/'.$val->id)}}" class="btn btn-primary" ><i class="fas fa-pencil-alt"></i> Edit</a>        
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
