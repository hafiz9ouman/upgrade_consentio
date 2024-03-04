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
			top: 6px !important;
			font-size: 15px;
			width: 50%;
			right: 35px !important;
		}
		.icons .gj-icon.clock{
			position: absolute !important;
    top: -27px !important;
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
	
	<div class="add_equality" style="border-radius:2%">
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
		<form action="{{url('update_inccident')}}" method="post">
			{{ csrf_field() }}
			{{-- <div class="heading">
				<h2>Update Incident</h2>
			</div> --}}
			@section('page_title')
			{{ __('EDIT INCIDENT') }}
			@endsection
			<div class="form-group">
				<label for="sel1">{{ __('Incident type') }}</label>
				<select name="incident_type" class="form-control selectpicker show-tick">
					<option>{{ __('Select Incident Type') }}</option>
					@foreach($incident_type as $incident)
					<option <?php if($data->incident_type == $incident->id) echo "selected"; ?> value="{{$incident->id}}">{{$incident->name}}</option>
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
					<option <?php if($data->organization_id == $org->id) echo "selected"; ?> value="{{$org->id}}">{{$org->company}}</option>
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
				<label for="usr">{{ __('Incident Name') }}</label>
				<input type="text" name="name" value="{{$data->name}}" class="form-control" placeholder="{{ __('Incident Name') }}">
				<input type="hidden" name="id" value="{{$data->id}}">
				
			</div>
			<div class="form-group">
				<label for="usr">{{ __('Assignee') }}</label>
				<input type="text" name="assignee" value="{{$data->assignee}}" class="form-control" placeholder="{{ __('Select an Assignee') }}">
			</div>
			<div class="form-group">
				<label for="comment">{{ __('Root Cause') }}</label>
				<textarea name="description" class="form-control" rows="5" placeholder="{{ __('Root Cause') }}">{{$data->description}}</textarea>
			</div>
			<div class="form-group">
				<label for="email">{{ __('Date Occurred') }}</label>
				<div class="icons"><input type="text" value="{{date('m/d/Y', strtotime($data->date_occurred))}}" name="date_occurred" class="form-control datePickera" style="width: 50%;"></div>
				<div class="icons">
					<input type="text" name="time_occured" value="{{$data->time_occured}}" class="form-control" id="timepickera" style="width: 48%;float: right;margin-top: -34px;"></div>
				</div>
				<div class="form-group">
					<label for="email">{{ __('Date Discovered') }}</label>
					<div class="icons"><input type="text" value="{{date('m/d/Y', strtotime($data->date_discovered))}}" name="date_discovered" class="form-control datePickerb" style="width: 50%;"></div>
					<div class="icons">
						<input type="text" id="timepickerb" value="{{$data->time_discovered}}"  name="time_discovered" class="form-control" style="width: 48%;float: right;margin-top: -34px;"></div>
					</div>
					<div class="form-group">
						<label for="email">{{ __('Deadline Date') }}</label>
						<div class="icons"><input name="deadline_date" value="{{date('m/d/Y', strtotime($data->deadline_date))}}" type="text" class="form-control datePickerc" style="width: 50%;"></div>
						<div class="icons">
							<input type="text" value="{{$data->time_deadline}}" name="time_deadline" id="timepickerc" class="form-control" style="width: 48%;float: right;margin-top: -34px;"></div>
						</div>
						<div class="form-group">
							<label for="comment">{{ __('Problem Description') }}</label>
							<textarea name="root_cause" class="form-control" rows="5" placeholder="{{ __('Problem Description') }}">{{$data->root_cause}}</textarea>
						</div>
						<div class="form-group">
							<label for="comment">{{ __('Resolution') }}</label>
							<textarea name="resolution" class="form-control" rows="5" placeholder="{{ __('Problem Description') }}">{{$data->resolution}}</textarea>
						</div>   
						<div class="form-group">
            				<label for="sel1">{{ __('Status') }}</label>
            				<select required name="incident_status" class="form-control selectpicker show-tick">
            					<option value="" @if($data->incident_status == "") selected @endif>{{ __('Select Status') }}</option>
            					<option value="Reported" @if($data->incident_status == "Reported") selected @endif>{{ __('Reported') }}</option>
            					<option value="Confirmed" @if($data->incident_status == "Confirmed") selected @endif>{{ __('Confirmed') }}</option>
            					<option value="Investigating" @if($data->incident_status == "Investigating") selected @endif>{{ __('Investigating') }}</option>
            					<option value="Resolved" @if($data->incident_status == "Resolved") selected @endif>{{ __('Resolved') }}</option>
            				</select>
            			</div>
            			<div class="form-group">
            				<label for="sel1">{{ __('Severity') }}</label>
            				<select required name="incident_severity" class="form-control selectpicker show-tick">
            					<option value="" @if($data->incident_severity == "") selected @endif>{{ __('Select Severity') }}</option>
            					<option value="Unknown" @if($data->incident_severity == "Unknown") selected @endif>{{ __('Unknown') }}</option>
            					<option value="Low" @if($data->incident_severity == "Low") selected @endif>{{ __('Low') }}</option>
            					<option value="Medium" @if($data->incident_severity == "Medium") selected @endif>{{ __('Medium') }}</option>
            					<option value="High" @if($data->incident_severity == "High") selected @endif>{{ __('High') }}</option>
            					<option value="Critical" @if($data->incident_severity == "Critical") selected @endif>{{ __('Critical') }}</option>
            				</select>
            			</div>
						<div class="buttons">
							<a href="{{url('incident')}}"><button type="button" class="Cancel">{{ __('Cancel') }}</button></a>
							<button type="sumbit" class="add">{{ __('Update') }}</button>
							</form>
						</div>
					</div>
                   <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
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