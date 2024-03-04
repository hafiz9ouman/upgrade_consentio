@extends ((($user_type == 'admin')?('admin.layouts.admin_app'):('admin.client.client_app')), ['load_admin_css' => true])
@section('content')
<style>
.section-heading {
	margin-bottom:10px;
	display: flex;
    flex-direction: row	
}
.section-heading-edit {
    display:none;
}

.sec-ctgry {
	margin: 0 25px auto;
	width: 35%;
}

</style>
<div class="container" style="background-color:white;padding-bottom:20px;">
	<div class="form_pia">
		<h1>@if (!empty($questions)) {{ $questions[0]->title }} @endif</h1>
		<p>@if (!empty($questions) && !empty($questions[0]->description)) {{ $questions[0]->description }} @endif</p>
		<!--<h3>General</h3>-->
		<form>
		    <input type="hidden" id="form-id" name="form-id" value="{{ $form_id }}" >
			<?php $heading_recs = []; ?>
			@if (!empty($questions))
				@foreach ($questions as $key => $question)

				<?php
                $sec_id          = $question->afs_sec_id;
                $heading         = $question->admin_sec_title;

                    //echo "<h3>client title ".$question->client_sec_title."</h3><br>";

                if (Auth::user()->role != 1 && (isset($question->client_sec_title) && !empty($question->client_sec_title))) {
                    //$sec_id          = $question->cfs_sec_id;
                    $sec_id          = $question->afs_sec_id;
                    $heading         = $question->client_sec_title;
                }

	            //if ($question->question_section != null && !in_array($question->question_section, $heading_recs)):
			        //$heading_recs[] = $question->question_section;
				if ($heading != null && !in_array($heading, $heading_recs)):
                    $heading_recs[] = $heading;
				?>
				<div class="section-heading" id="{{ "section-heading-".$sec_id }}" >
					<h3>{{ $heading }}</h3>
                    <?php if (Auth::user()->role == 1 || Auth::user()->role == 2): ?>
                    <span id={{ "heading-edit-".$sec_id }}
                        style="margin-left:10px;"
                        class="heading-edit">
                        <a href="" class="change-heading-icon" sec-id="{{ $sec_id }}"  style="color:black"><i class="fas fa-pencil-alt"></i></a>
                    </span>
                    <?php endif; ?>

                    <?php if (Auth::user()->role == 1): ?>
					    <select name="section-{{ $question->question_section }}"
							sec="{{ $question->question_section }}"
							form-id="{{ $form_id }}"
							class="form form-control sec-ctgry">
    						<option value="">Select Category</option>
    						<option value="1" <?php if ($question->question_category == 1) echo 'selected'; ?>>Asset Inventory</option>
    						<option value="2" <?php if ($question->question_category == 2) echo 'selected'; ?>>Data Inventory</option>
					    </select>
				    <?php endif; ?>

                </div>

                <div id="{{"section-heading-edit-".$sec_id}}" class="section-heading-edit container">
                    <div class="row" style="margin-bottom:10px">
                        <div class="col-4" style="padding-left:0px;">
                            <input id="{{"new-section-heading-".$sec_id}}" type="text" class="form form-control" value="{{ $heading }}" old-value="{{ $heading }}" />
                        </div>
                        <button class="btn btn-primary change-heading-btn" sec-id="{{ $sec_id }}" >Save</button>
                    </div>
                </div>



				<?php endif; ?>
			
			
				<div class="form-group">
					<label><h5>{{ $question->question }}</h5></label>
					@if ($question->question_comment != null && $question->question_comment != '')
					<p>
						{{ $question->question_comment }}
					</p>
					@endif
					

					@switch($question->type)
			
						@case('sc')
							<?php $radio_options = explode(', ', $question->options); ?>
							@if (!empty($radio_options))
									@if ($question->question_assoc_type == 0 || $question->question_assoc_type == 1)
										@foreach ($radio_options as $option)
										<div class="form-group form-check">
											<label class="form-check-label">
												<input class="form-check-input" type="radio" name="{{ $question->form_key }}"   value="{{ $option }}" disabled> {{ $option}}
											</label>
										</div>
										@endforeach
									@endif

									@if ($question->question_assoc_type == 2)
										<div class="form-group radio">
											@foreach ($radio_options as $option)
											<label><input type="radio" name="{{ $question->form_key }}" disabled>  {{ $option }}</label>
											@endforeach
										</div>
									@endif	
							@endif
						@break

						@case('mc')
							<?php $chkbox_options = explode(', ', $question->options); ?>
							@if (!empty($chkbox_options))
								@foreach ($chkbox_options as $option)
								<div class="form-group form-check">
									<label class="form-check-label">
										<input class="form-check-input" type="checkbox" name="{{ $question->form_key }}"   value="{{ $option }}" disabled> {{ $option}}
									</label>
								</div>
								@endforeach
							@endif
						@break						

						@case('bl')
							<div class="form-group">
								<label></label>
								<input type="text" class="form-control" name="{{ $question->form_key }} " value="" disabled>
							</div>							
							
						@break

						@case('qa')
							<!--
							<br>
							<textarea name="{{ $question->form_key }}"  rows="4" cols="50" disabled></textarea><br><br>
							-->
						@break
			
					   @case('blanks')
					   @break
						
						Default case...
					@endswitch
					@if ($question->additional_comments)
						<div>
						<label>Additional comments below:</label>
						<input type="text" class="form-control" value="" disabled>	
						</div>
						
					@endif
				</div>
				@endforeach
				
			@endif		
		</form>
	</div>
	<script>
		$(document).ready(function(){

            $('.change-heading-icon').click(function(event){

                event.preventDefault();

                var heading_sec_id = $(this).attr('sec-id');

                $('div#section-heading-'+heading_sec_id).hide();

                $('div#section-heading-edit-'+heading_sec_id).show();

            });

            $('.change-heading-btn').click(function(event){

                event.preventDefault();

                var heading_sec_id = $(this).attr('sec-id');

                $('div#section-heading-edit-'+heading_sec_id).hide();

                var old_heading = $('#new-section-heading-'+heading_sec_id).attr('old-value');
                var new_heading = $('#new-section-heading-'+heading_sec_id).val();

                $('div#section-heading-'+heading_sec_id+' > h3').text(new_heading);
                $('div#section-heading-'+heading_sec_id).show();

                var form_id = $('#form-id').val();

                var post_data = {};

    			post_data['_token']          = '{{csrf_token()}}';
                post_data['form_id']         =    form_id;
                post_data['title']           =    new_heading;
                post_data['old_title']       =    old_heading;

                post_data['user_id']         =  {{(Auth::user()->client_id }};
                //post_data['user_id']         =  23;
                post_data['updated_by']      =  {{ Auth::id() }};
                post_data['form_section_id'] =    heading_sec_id;

                <?php if (Auth::user()->role==1): ?>
                post_data['is_admin']    = 1;
                <?php endif; ?>

                $.ajax({
    				url:'{{route('update_form_section_heading')}}',
    				method:'POST',
		            data: post_data,
		            success: function(response) {
			            console.log(response);
		            	//alert('Forms assigned to client');
		            }


                });


            });


            <?php if (Auth::user()->role == 1): ?>

    			$('.sec-ctgry').change(function(){
    				var section_name = $(this).attr('sec');
    				var form_id      = $(this).attr('form-id');
    				var category_id  = $(this).val();

    				if (category_id != '') {

    					var post_data = {};

    					post_data['_token']    = '{{csrf_token()}}';
    					post_data['form_id']   = form_id;
    					post_data['sec_name']  = section_name;
    					post_data['ctg_id']    = category_id;

    					$.ajax({
    						url:'{{route('asgn_sec_ctgry')}}',
    						method:'POST',
    						/*
    						headers: {
    							'X-CSRF-TOKEN': $( 'meta[name="csrf-token"]' ).attr( 'content' )
    						},
    						*/
    						data: post_data,
    						success: function(response) {
    							console.log(response);
    							//alert('Forms assigned to client');
    						}
    					});
    				}

    			});

            <?php endif; ?>

		});
	</script>

</div>
@endsection