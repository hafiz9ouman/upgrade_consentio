@extends('admin.client.client_app')
@section('content')
<div class="row">
	<div class="col-lg-12">
		<h2>Update Remediation Plans</h2>
		<div class="my-4 border"></div>
		<form action="{{url('update-remediation')}}" method="post">
			{{csrf_field()}}
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<input type="hidden" value="{{$data->id}}" name="reme_id" />
						<label for="assset_name">Asset Name</label>
						<select name="asset_name" id="asset_name" class="form-control">
							
								@foreach($asset as $val)
									<option value="{{$val->id}}" {{$val->id == $data->asset_name ? "selected":""}}>{{$val->name}}</option>
								@endforeach
								
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="business_unit">Business Unit</label>
						<input type="text" name="business_unit" id="business_unit" class="form-control" value="{{$data->business_unit}}" />
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="control_name">Control Name</label>
						<input type="text" name="control_name" id="control_name" class="form-control" value="{{$data->control_name}}" />
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="initial_review">Initial Review</label>
						<input type="text" name="initial_review" id="initial_review" class="form-control" value="{{$data->initial_review}}" />
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="proposed_remediation">Proposed Remediation</label>
						<textarea cols="1" rows="1" id="proposed_remediation" class="form-control" name="proposed_remediation" value="{{$data->proposed_remediation}}">{{$data->proposed_remediation}}</textarea>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="post_remediation_assessment">Post Remediation Assessment</label>
						<input type="text" name="post_remediation_assessment" id="post_remediation_assessment" class="form-control" value="{{$data->post_remediation_assessment}}" />
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="person_in_charge">Person in Charge</label>
						<input type="text" name="person_in_charge" id="person_in_charge" class="form-control" value="{{$data->person_in_charge}}" />
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="status">Status</label>
						<select name="status" id="status" class="form-control">
							<option value="#"></option>
							<option value="1" {{$data->status == 1 ? "selected":""}}>Analysis in Progress</option>
							<option value="2" {{$data->status == 2 ? "selected":""}}>Remediation in Progress</option>
							<option value="3" {{$data->status == 3 ? "selected":""}}>Remediation Applied</option>
							<option value="4" {{$data->status == 4 ? "selected":""}}>Risk Acceptance</option>
							<option value="5" {{$data->status == 5 ? "selected":""}}>Other</option>
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="initial_rating">Initial Rating</label>
						<input type="text" name="initial_rating" id="initial_rating" class="form-control" value="{{$data->initial_rating}}" />
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="post_remediation_rating">Post Remediation Rating</label>
						<input type="text" name="post_remediation_rating" id="post_remediation_rating" class="form-control" value="{{$data->post_remediation_rating}}" />
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<input type="submit" class="btn btn-primary float-right" value="Update" />
					</div>
				</div>
			</div>

		</form>
	</div>
</div>
@endsection 