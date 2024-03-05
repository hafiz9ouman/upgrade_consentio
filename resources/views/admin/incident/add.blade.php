@extends(($user_type=='1')?('admin.layouts.admin_app'):('admin.client.client_app'))
@section( 'content' )

<style>
		.card {
			padding: 30px;
		}
		.form-control {
			transition: .5s;
		}
		.form-control:focus {
			
			transition: .5s;
		}
		.form {
			text-transform: capitalize;
		}
		.heading h2 {
			border-bottom: 1px solid gainsboro;
		}
		.icons {
			position: relative;
		}
		.icons .gj-icon {
			position: absolute !important;
			top: 13px !important;
			font-size: 15px;
			width: 50%;
			right: 35px !important;
		}
		.icons .gj-icon.clock{
			position: absolute !important;
			top: -35px !important;
			font-size: 15px;
			width: 50%;
			right: 12px !important;
			display: flex;
			justify-content: flex-end;
		}
		.gj-icon.clock:before {
			font-size: 20px !important;
		}
		.buttons {
			    display: flex;
    			justify-content: flex-end;
		}
		.Cancel {
		border: none;
		background: transparent;
		padding: 5px 30px;
		cursor: pointer;
		}
		.add {
		border: 1px solid #0f75bd;
		background: #0f75bd;
		color: #fff;
		padding: 5px 30px;
		border-radius: 15px;
		font-weight: bold;
		}
		.add:focus {
		outline: none;
		}
		.add:hover {
		background: #0089ff;
		transition: .5s;
		cursor: pointer;
		}
		input:hover {
	    color: #333;
	    background-color: #e6e6e6;
	    border-color: #adadad;
		}
		.dropdown-toggle {
			    background: white;
    border: 2px solid #ced4da;
		}
		.datepicker.dropdown-menu{
			margin-top: 53px;
		}
		.datePickera {
			margin-top: 10px !important;
		}
		.gj-textbox-md  {
		    border: 2px solid #ced4da !important;
		    padding: 0.375rem 0.75rem !important;

		}
		.bord{
			border-radius:2%;
		}
		/*.form-group label {
			font-weight: bold !important;
		}*/
		.add_equality {
			padding: 30px;
		    background-color: #fff;
		}
		.dropdown-toggle {
			border: none;
		}
		@media screen and (max-width: 580px) {
			.add_equality {
				margin: 0 30px 30px;
			}
		}
</style>
	@section('page_title')
	{{ __('ADD INCIDENT') }}
	@endsection	

		@if(Session::has('error'))
      	<div class="alert alert-danger">
      		<a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    		{{ Session::get('error') }}
    	</div>
    	@endif
    	@if(Session::has('success'))
      	<div class="alert alert-success">
      		<a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    		{{ Session::get('success') }}
  		</div>
    	@endif
     	@if(Session::has('alert'))
      	<div class="alert alert-danger">
      		<a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    		{{ Session::get('alert') }}
    	</div>
    	@endif 

		@if (count($errors) > 0)
			<div class="alert alert-danger">
				<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif
				
		<section>
			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-body">
						<form action="{{url('save_inccident')}}" method="post">
						{{ csrf_field() }}
						
						<div class="form-group">
							<label for="sel1">{{ __('Incident type') }}<span style="color:red;">*</span></label>
							<select required name="incident_type" class="form-control selectpicker show-tick">
								<option value="">{{ __('Select Incident Type') }}</option>
								@foreach($incident_type as $incident)
								<option value="{{$incident->id}}">{{ __($incident->name) }}</option>
								@endforeach
							</select>
						</div>
						@if($user_type=='1')
						<div class="form-group">
							<label for="sel1">{{ __('Orgnaization') }}</label>
							<select name="organization_id" class="form-control selectpicker show-tick">
								<option>{{ __('Select an Orgnaization group') }}</option>
								@foreach($organization as $org)
								@if($org->company != "")
								<option value="{{$org->id}}">{{$org->company}}</option>
								@endif
								@endforeach
							</select>
						</div>
						@else
						<?php $org = Auth::user()->client_id;
						?>
						<input type="hidden" name="organization_id" value="{{$org}}">
						@endif
						
						<div class="form-group">
							<label for="usr">{{ __('Incident Name') }}<span style="color:red;">*</span></label>
							<input type="text" value="{{ old('name')}}" name="name" maxlength="50" class="form-control" placeholder="{{ __('Incident Name') }}" required>
						</div>
						<div class="form-group">
							<label for="usr">{{ __('Assignee') }}<span style="color:red;">*</span></label>
							<input type="text" value="{{ old('assignee')}}" name="assignee" maxlength="100" class="form-control" placeholder="{{ __('Select an Assignee') }}" required>
						</div>
						<div class="form-group">
							<label for="comment">{{ __('Root Cause') }}</label>
							<textarea name="root_cause" class="form-control" rows="5" placeholder="{{ __('Root Cause') }}"> {{ old('root_cause') }} </textarea>
						</div>
						<div class="form-group">
							<label for="email">{{ __('Date Occurred') }}</label>
							<div class="icons">
								<input type="text" value="{{ old('date_occurred')}}" readonly="" name="date_occurred" class="form-control datePickera" style="width: 50%;">
							</div>
							<div class="icons">
								<input type="text" name="time_occured" value="{{ old('time_occured')}}" readonly=""   class="form-control" id="timepickera" style="width: 48%;float: right;margin-top: -50px;"></div>
							</div>
							<div class="form-group">
								<label for="email">{{ __('Date Discovered') }}<span style="color:red;">*</span></label>
								<div class="icons">
									<input type="text" value="{{ old('date_discovered')}}" name="date_discovered" readonly="" class="form-control datePickerb" style="width: 50%;" required>
								</div>
								<div class="icons">
									<input type="text" id="timepickerb" readonly="" name="time_discovered" value="{{ old('time_discovered')}}" class="form-control" style="width: 48%;float: right;margin-top: -50px;"></div>
								</div>
								<div class="form-group">
									<label for="email">{{ __('Deadline Date') }}</label>
									<div class="icons">
										<input name="deadline_date" value="{{ old('deadline_date')}}" type="text" readonly="" class="form-control datePickerc" style="width: 50%;">
									</div>
									<div class="icons">
										<input type="text" name="time_deadline" value="{{ old('time_deadline')}}"  id="timepickerc" readonly="" class="form-control" style="width: 48%;float: right;margin-top: -50px;"></div>
									</div>
									<div class="form-group">
										<label for="comment">{{ __('Problem Description') }}<span style="color:red;">*</span></label>
										<textarea name="description"  class="form-control" rows="5" placeholder="{{ __('Problem Description') }}" maxlength="255" required>{{ old('description') }}</textarea>
									</div>
									<div class="form-group">
										<label for="comment">{{ __('Resolution') }}</label>
										<textarea name="resolution" class="form-control" rows="5" placeholder="{{ __('Resolution') }}">{{ old('resolution') }}</textarea>
									</div> 
									<div class="form-group">
										<label for="sel1">{{ __('Status') }}<span style="color:red;">*</span></label>
										<select required name="incident_status" class="form-control selectpicker show-tick">
											<option value="">{{ __('Select Status') }}</option>
											<option value="Reported">{{ __('Reported') }}</option>
											<option value="Confirmed">{{ __('Confirmed') }}</option>
											<option value="Investigating">{{ __('Investigating') }}</option>
											<option value="Resolved">{{ __('Resolved') }}</option>
										</select>
									</div>
									<div class="form-group">
										<label for="sel1">{{ __('Severity') }}<span style="color:red;">*</span></label>
										<select required name="incident_severity" class="form-control selectpicker show-tick">
											<option value="">{{ __('Select Severity') }}</option>
											<option value="Unknown">{{ __('Unknown') }}</option>
											<option value="Low">{{ __('Low') }}</option>
											<option value="Medium">{{ __('Medium') }}</option>
											<option value="High">{{ __('High') }}</option>
											<option value="Critical">{{ __('Critical') }}</option>
										</select>
									</div>
									<div class="buttons">
										<a href="{{url('incident')}}" class="btn btn-secondary bg-dark p-2 px-5" style="border-radius:35px;">{{ __('Cancel') }}</a>
										<button type="sumbit" class="btn btn-info p-2 px-5 ml-1" style="border-radius:35px;background:#0f75bd;">{{ __('Add') }}</button>
										</form>
						</div>
					</div>
				</div>
			</div>
		</section>
						
					
    <!-- <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script> -->
	@if(session('locale') == 'fr')
	<script src="{{ url('backend/js/dtpicker.min.js') }}"></script>
	@else
	<script src="{{ url('backend/js/dtpicker-en.min.js') }}"></script>
	@endif
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<script>
        $('#timepickera').timepicker();
    </script>
    <script>
        $('#timepickerb').timepicker();
    </script>
    <script>
        $('#timepickerc').timepicker();
    </script>
	
			
					<script type="text/javascript">
						$(function () {
						$('.datePickera').datepicker({ dateFormat: 'm-d-Y' });
						});
						</script>
						<script type="text/javascript">
						$(function () {
						$('.datePickerb').datepicker({ dateFormat: 'm-d-Y' });
						});
						</script>
						<script type="text/javascript">
						$(function () {
						$('.datePickerc').datepicker({ dateFormat: 'm-d-Y' });
						});
						</script>
<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/css/bootstrap-select.min.css" rel="stylesheet" />
		<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/js/bootstrap-select.min.js"></script> -->

					@endsection