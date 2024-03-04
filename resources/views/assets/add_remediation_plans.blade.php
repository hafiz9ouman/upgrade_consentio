@extends('admin.client.client_app')
@section('content')
<div class="row">
	<div class="col-lg-12">
		<h2>Add Remediation Plans</h2>
		<div class="my-4 border"></div>
		<form action="{{url('add_remediation')}}" method="post">
			{{csrf_field()}}
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="assset_name">Asset Name</label>
						<select name="asset_name" id="asset_name" class="form-control">
							
								@foreach($data as $val)
									<option value="{{$val->id}}">{{$val->name}}</option>
								@endforeach
								
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="business_unit">Business Unit</label>
						<input type="text" name="business_unit" id="business_unit" class="form-control" />
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="control_name">Control Name</label>
						<input type="text" name="control_name" id="control_name" class="form-control" />
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="initial_review">Initial Review</label>
						<input type="text" name="initial_review" id="initial_review" class="form-control" />
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="proposed_remediation">Proposed Remediation</label>
						<textarea cols="1" rows="1" id="proposed_remediation" class="form-control" name="proposed_remediation"></textarea>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="post_remediation_assessment">Post Remediation Assessment</label>
						<input type="text" name="post_remediation_assessment" id="post_remediation_assessment" class="form-control" />
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="person_in_charge">Person in Charge</label>
						<input type="text" name="person_in_charge" id="person_in_charge" class="form-control" />
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="status">Status</label>
						<select name="status" id="status" class="form-control">
							<option value="#"></option>
							<option value="1">Analysis in Progress</option>
							<option value="2">Remediation in Progress</option>
							<option value="3">Remediation Applied</option>
							<option value="4">Risk Acceptance</option>
							<option value="5">Other</option>
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="initial_rating">Initial Rating</label>
						<input type="text" name="initial_rating" id="initial_rating" class="form-control" />
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="post_remediation_rating">Post Remediation Rating</label>
						<input type="text" name="post_remediation_rating" id="post_remediation_rating" class="form-control" />
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<input type="submit" class="btn btn-primary float-right" />
					</div>
				</div>
			</div>

		</form>
	</div>
</div>
@endsection 