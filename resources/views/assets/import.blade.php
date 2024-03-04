@extends ('admin.client.client_app')

@section('content') 
@section('page_title')
  {{ __(' Import Asset ') }}
  @endsection
		@if (Session::has('msg'))
			<div class="alert alert-danger alert-dismissible fade show mx-5" role="alert">
				<strong>{{ Session::get('msg') }}</strong>
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
		@endif	
		<form action="{{route('import')}}" method="post" enctype="multipart/form-data">
			{{ csrf_field() }}
			 <div class="form-group" >
			 	<input type="file" name="import_file" id="import_file" class="form-control">
			 </div>
			 <div class="form-group">
			 	<button type="submit" class="btn btn-primary">Import Data</button>
			 	<a href="{{url('export-sample-data')}}" class="btn btn-success float-right">Sample Data</a>
			 </div>
		</form>
		

@endsection