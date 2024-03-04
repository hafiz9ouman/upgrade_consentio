@extends('admin.client.client_app')
@section('content')
@section('page_title')
	{{ __('Update Data Element') }}
@endsection
<div class="row">
	<div class="col-md-10 offset-1 card shadow p-4">
		<form class="form-horizontal" method="POST" action="{{ url('update_data_element') }}">
			{{ csrf_field() }}
			@foreach($data as $val)
				<div class="row">
					<div class="col-sm-8 col-md-6">
						<input id="id"   type="hidden" class="form-control" name="id"  value="{{$val->id}}">
						<div class="form-group">
							<label class="form-control-label">Asset Element</label>
							<input id="name" type="text"   class="form-control" name="name" value="{{$val->name}}" required autofocus>
						</div>

						<div class="form-group">
							<label for="#">Data Element Group</label>
							<select name="element_group" id="" class="form-control">
								@foreach($section as $se)
									<option value="{{$se->id}}" {{$se->id == $val->section_id ? "selected":""}} >{{$se->section_name}}</option>
								@endforeach
							</select>
						</div>

						<div class="form-group">
							<label for="#">Data Classification Name</label>
							<select name="d_c_name" id="" class="form-control">
								@foreach($dc_result as $dc)
									<option value="{{$dc->id}}" {{$dc->id == $val->d_classification_id ? "selected":""}} >{{$dc->classification_name_en}}</option>
								@endforeach
							</select>
						</div>

					</div>
				</div>
			@endforeach
			<div class="row">
				<div class="tile-footer col-sm-12  text-right px-4">
					<button type="submit" class="btn btn-primary">Update</button>
				</div>
			</div>
		</form>
	</div>
</div>

@endsection