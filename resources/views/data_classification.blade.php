@extends('admin.layouts.admin_app')

@section('content')

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
        <h3 class="tile-title">Data Classification
          {{-- <a href="{{ route('add_new_form') }}" class="btn btn-sm btn-success pull-right cust_color" style="margin-right: 10px;"><i class="fa fa-plus" aria-hidden="true"></i>Add New Form</a> --}}
        </h3>

        <table class="table" id="forms-table">
          <thead class="back_blue">
            <tr>
              
              <th scope="col" col-span="2" >Data Classification Name English </th>
              <th scope="col" col-span="2" >Data Classification Name French </th>
              <?php if (Auth::user()->role == 1): ?>
              <th scope="col">Actions</th>
              <?php endif; ?>

            </tr>
          </thead>
          <tbody>
            <?php foreach ($data as $class): ?>
            <tr>
              <td>{{ $class->classification_name_en }}</td>
               <td>{{ $class->classification_name_fr }}</td>
             
               
             
               <?php if (Auth::user()->role == 1): ?>
              <td><a href="{{url('edit-classification/'.$class->id)}}"> <i class="fas fa-pencil-alt"></i> Edit</a></td>
              <?php endif; ?>
              
            </tr>
            <?php endforeach; ?>
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
