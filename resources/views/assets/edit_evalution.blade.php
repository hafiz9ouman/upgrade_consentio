@extends('admin.client.client_app')
@section('content')
@section('page_title')
	{{ __('Edit Evaluation') }}
@endsection
	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-body">
					<form action="{{url('update_evalution_rating')}}" method="post">
						{{ csrf_field() }}
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="">{{__('Assessment')}}</label>
									<input type="hidden" name="id" id="id" class="form-control" placeholder="Assessment" value="{{$data->id}}">
									<input type="text" name="assessment" id="Assessment" class="form-control" placeholder="Assessment" value="{{__($data->assessment)}}" disabled>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="">{{__('Rating')}}</label>
									<input type="text" name="rating" id="rating" class="form-control" placeholder="Rating" value="{{__($data->rating)}}" disabled>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="">{{__('Background Color')}}</label>
									<input type="text" name="color" id="color" class="form-control" placeholder="Background Color" value="{{$data->color}}">
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="">{{__('Text Color')}}</label>
									<input type="text" name="text_color" id="text_color" class="form-control" placeholder="Text Color" value="{{$data->text_color}}">
								</div>
							</div>
							<div class="col-md-12">
								<div class="form-group">
									<a href="{{ url('evaluation_rate') }}" class="btn btn-secondary bg-dark p-2 px-5" style="border-radius:35px;"><b>{{__('Cancel')}}</b></a>
									<input type="submit" class="btn btn-info p-2 px-5 ml-1" style="border-radius:35px;background:#0f75bd;" value="{{__('Update')}}" />
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection