@extends ('admin.client.client_app')
@section('page_title')
    {{ __('Manage Remediations') }}
@endsection
@section('content')
    <style>
        td, th {
            text-align: center;
            vertical-align: middle !important;
        }
        .back_blue {
            background-color: #0f75bd !important;
            color:#fff;
        }
    </style>
    <section class="assets_list">
        <div class="row">
            <div class="col-12">
            <div class="card">
            
            {{--<td>{{Auth::user()->client_id}}-{{$plan->asset_number}}</td> <td>{{$plan->assets_name}}</td> --}}

            <div class="col-12">
                <h2 align="center">@if(session('locale') == 'fr') {{ $remediation_plans[0]->sub_form_title_fr }} @else {{ $remediation_plans[0]->sub_form_title }} @endif</h2>
            </div>

            <div class="col-12 px-5 py-2 d-flex justify-content-between">
                <h4>{{__("remediation_item_name")}} : @if($remediation_plans[0]->type == 'others') {{ $remediation_plans[0]->other_name }} @else {{ $remediation_plans[0]->assets_name }}@endif</h4>
                <h4>{{__("Group Name")}} : @if(session('locale') == 'fr') {{ $remediation_plans[0]->group_name_fr }} @else {{ $remediation_plans[0]->group_name }} @endif</h4>
                <h4>{{__("remediation_item_number")}} : 
                    @if($remediation_plans[0]->type == 'others')
                        N-
                    @else
                        A-
                    @endif
                    {{Auth::user()->client_id}}-
                    @if($remediation_plans[0]->type == 'others') {{ $remediation_plans[0]->other_number }} @else {{ $remediation_plans[0]->assets_number }}@endif</h4>
            </div>
            <div class="card-table">
            <div class="col-12 overflow-auto py-3">
                <table class="table" id="datatable" style="min-width:800px">
                    <thead>
                        <tr>
                            <th> #</th>
                            <th> {{__('Control ID')}}</th>
                            <th> {{__('Control Title')}}</th>
                            <th> {{__('Control Question')}}</th>
                            <th> {{__('User Response')}}</th>
                            <th> {{__('Review Comment')}}</th>
                            <th style="min-width: 350px"> {{__('Proposed Remediation')}} </th>
                            <th style="min-width: 350px"> {{__('Completed Actions')}} </th>
                            <th>{{__('ETA')}}</th>
                            <th> {{__('Person In Charge')}} </th>
                            <th>{{__('Remediation status')}}</th>
                            <th> {{__('Initial Rating')}}</th>
                            <th>{{__('POST Assessment')}}</th>
                            <th>{{__('POST Rating')}}</th>
                        </tr>
                </thead>
                    <tbody id="render_questions">
                        @foreach($remediation_plans as $plan)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$plan->control_id}}</td>
                                <td>
                                    @if(session('locale') == 'fr')
                                        {{$plan->question_short_fr}}
                                    @else
                                        {{$plan->question_short}}
                                    @endif
                                </td>
                                <td>
                                    @if(session('locale') == 'fr')
                                        {{$plan->question_fr}}
                                    @else
                                        {{$plan->question}}
                                    @endif
                                </td>
                                <td>{{$plan->question_response}}</td>
                                <td>{{$plan->admin_comment}}</td>
                                <td><textarea rows="3" class="form-control handle_onChange" plan_id="{{$plan->id}}" name="proposed_remediation" >{{$plan->proposed_remediation}}</textarea></td>
                                <td><textarea rows="3" class="form-control handle_onChange" plan_id="{{$plan->id}}" name="completed_actions"    >{{$plan->completed_actions}}</textarea></td>
                                <td>
                                    <input type="date" class="form-control handle_onChange" plan_id="{{$plan->id}}" name="eta" value='{{ $plan->eta }}'>
                                </td>
                                <td>
                                    <select class="form-control handle_onChange" plan_id="{{$plan->id}}" name="person_in_charge">
                                        @foreach($users as $user)
                                            <option value="{{$user->id}}"
                                            @if($user->id == $plan->person_in_charge)
                                            selected
                                            @endif
                                            >{{$user->name}}</option>
                                        @endforeach
                                    <select>
                                </td>
                                <td style="min-width: 220px">
                                    <select class="form-control handle_onChange" plan_id="{{$plan->id}}" name="status">
                                        <option value="Analysis in Progress" @if($plan->status == "Analysis in Progress") selected @endif >{{__('Analysis in Progress')}}</option>
                                        <option value="Remediation in Progress" @if($plan->status == "Remediation in Progress") selected @endif >{{__('Remediation in Progress')}}</option>
                                        <option value="Remediation Applied" @if($plan->status == "Remediation Applied") selected @endif >{{__('Remediation Applied')}}</option>
                                        <option value="Risk Acceptance" @if($plan->status == "Risk Acceptance") selected @endif >{{__('Risk Acceptance')}}</option>
                                        <option value="Other" @if($plan->status == "Other") selected @endif >{{__('Other')}}</option>
                                    </select>
                                </td>
                                <td> 
                                @php
                                    $client_id = Auth()->user()->client_id;
                                    $rating = $plan->rate_level;
                                    $rat = DB::table('evaluation_rating')
                                        ->where('rate_level', $rating)
                                        ->where('owner_id', $client_id)
                                        ->first();
                                @endphp
                                    <b class="px-3 py-2 rounded" style="background-color: {{$rat->color}}; color: {{$rat->text_color}} ">{{__($rat->rating)}} </b>
                                </td>
                                <td style="min-width: 200px">
                                    <select class="form-control handle_onChange" onchange="$(this)." q_type="rating" plan_id="{{$plan->id}}" class="form-control handle_onChange" name="post_remediation_rating" >
                                        @if(true)
                                            <option value="">{{__('Select Assessment')}}</option>
                                        @endif
                                        @foreach($eval_ratings as $rate)
                                            <option value="{{ $rate->id }}" rating ="{{ $rate->rating }}" @if($plan->post_remediation_rating ==  $rate->id) selected @endif>{{__($rate->assessment)}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <p id="post_ratting_{{$plan->id}}">
                                        @foreach($eval_ratings as $rate) 
                                            @if($plan->post_remediation_rating ==  $rate->id) 
                                                <b class="px-3 py-2 rounded" style="background-color: {{$rate->color}}; color: {{$rate->text_color}}">{{__($rate->rating)}}</b>
                                            @endif
                                        @endforeach
                                        @if($plan->post_remediation_rating ==  "")
                                            --
                                        @endif
                                    </p>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
            </div>
            </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script>
        
        $(function(){
            $('#remediation_details').DataTable();
        });
        $(function(){
            $('.handle_onChange').on("change", function(){
                var plan_id     = $(this).attr('plan_id');
                var name        = $(this).attr('name');
                var val         = $(this).val();
                var q_type      = $(this).attr('q_type');
                

                var information = {
                    name,
                    val
                }
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '/audit/remediation/update/'+plan_id,
                    data: information,
                    type:'post',
                    success: function (res) {
                        console.log(res);

                        if (true && q_type == "rating") {
                            // alert("rating")
                            // $(`post_ratting_${plan_id}`).html("")
                            location.reload();
                            
                        }
                        // if (!res.status) {
                        //     swal('', res.error, 'warning');
                        // }else{
                        //     $('#addQuestionModel').modal('hide');
                        //     swal('', res.success, 'success');
                        //     $('textarea').val("");
                        //     $('input').val("");
                            
                        //     setTimeout(() => {
                        //         location.reload();
                        //     }, 500);
                        // }  
                    }
                });
            })
        });
    </script>
@endpush
