@extends ('admin.client.client_app')

@section('content') 
@section('page_title')
  {{ __('Import Asset') }}
  @endsection
		@if (Session::has('msg'))
			<div class="alert alert-danger alert-dismissible fade show mx-5" role="alert">
				<strong>{{ Session::get('msg') }}</strong>
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
		@endif
		@if ($errors->any())
			<div class="alert alert-danger">
				<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif	
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-body">
						<form action="{{route('import')}}" method="post" enctype="multipart/form-data">
							{{ csrf_field() }}
							<div class="form-group" >
								<input type="file" name="import_file" id="import_file" class="form-control" style="padding: 6px 15px;font-size: 22px;">
							</div>
							<div class="form-group">
								<button type="submit" class="button">{{__('Import Data')}}</button>
								@if(session('locale')=='fr')
								<a href="{{url('assets-new/Sample_Asset_French.xlsx')}}" class="button float-right" download>{{__('Sample Data')}}</a>
								@else
								<a href="{{url('assets-new/Sample_Asset_English.xlsx')}}" class="button float-right" download>{{__('Sample Data')}}</a>
								@endif
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		

@endsection