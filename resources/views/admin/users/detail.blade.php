@extends( 'layouts.app' )

@section( 'content' )
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css">
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>
<script>
	$( function () {

		$( "#rateYo" ).rateYo( {
			rating: {{ $user_data->rating }},
			readOnly: true
		} );

	} );
</script>
<!-- Page Header-->
<header class="page-header">
	<div class="container-fluid">
		<h2 class="no-margin-bottom">@lang('users.user_detail')</h2>
	</div>
</header>
<!-- Breadcrumb-->
<div class="breadcrumb-holder container-fluid">
	<ul class="breadcrumb">
		<li class="breadcrumb-item">
			<a href="{{url('dasboard')}}">@lang('general.home')</a>
		</li>
		<li class="breadcrumb-item">
			<a href="{{url('users')}}">@lang('users.users')</a>
		</li>
		<li class="breadcrumb-item active">{{$user_data->name}}</li>
	</ul>
</div>

<section class="client">
	<div class="container-fluid">
		<div class="row justify-content-center">
			<div class="col-lg-6">
				<div class="client card">
					<div class="card-body text-center">
						<div class="client-avatar">
							<?php
                            $file = 'storage/profiles/' . $user_data->id . '.jpg';
       if (file_exists($file) ) {
           $img = url($file);
} else {
            $img = url('backend/img/avatar-2.jpg');
        }
        ?>

                            <img src="{{  $img }}" alt="..." class="img-fluid rounded-circle">
                        </div>
                        <div class="client-title">
                            <h3>{{$user_data->name}}</h3>
                            <p style="line-height: 2;">
                                <span><i class="fa fa-envelope"></i> {{$user_data->email}}</span> @if($user_data->phone)
                                <span><i class="fa fa-phone"></i>  {{$user_data->phone}}</span> @endif @if(isset($user_data->package->name))
                                <span>@lang('users.package'):  {{$user_data->package->name}}</span> @endif @if(isset($user_data->package->time_period))
                                <span>@lang('packages.time_period'):  {{$user_data->package->time_period}}</span> @endif @if($user_data->rating)
                                <p class="text-center">@lang('Rating'): <br><span id="rateYo">  {{$user_data->rating}}</span>
                                </p>

                                @endif
                            </p>
                            <a href="{{url('users/edit/' . $user_data->id)}}">@lang('users.edit')</a>
                        </div>
                    </div>
                </div>
                <!-- <p class="text-center"><a href="{{url('partners')}}" class="btn btn-sm btn-secondary">Back to Staff</a></p> -->
            </div>
        </div>
        <div class="articles card">
            
            <div class="card-header d-flex align-items-center">
                <h2 class="h3">@lang('users.friends')</h2>
                <!-- <div class="badge badge-rounded bg-green">4 New       </div> -->
            </div>
            <div class="card-body">
                <div class="row">
                    @if(count($friends))
                    @foreach($friends as $friend)
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <div class="item d-flex align-items-center">
                                <div class="image"><img src="{{ $friend->photo}}" alt="..." class="img-fluid rounded-circle"></div>
                                <div class="text">
                                    <h3 class="h5">{{ $friend->name}}</h3></a>
                                    <small><i class="fa fa-envelope"></i> {{ $friend->email}}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @else
                        <div class="col-sm-12">
                            <div class="alert alert-primary mb-0">@lang('users.no_friends')</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="articles card">
            
            <div class="card-header d-flex align-items-center">
                <h2 class="h3">@lang('users.trips')</h2>
                <!-- <div class="badge badge-rounded bg-green">4 New       </div> -->
            </div>
            <div class="card-body">
                <div class="row">
                    @if(count($plans))
                    <div class="col-sm-12" id="accordion" role="tablist">
                        @foreach($plans as $key=> $plan)
                        <div class="card bg-light mb-0">
                            <a data-toggle="collapse" href="#collapseOne{{$key+1}}" aria-expanded="false" aria-controls="collapseOne" class="collapsed">
                                <div class="card-header text-primary" role="tab" id="headingOne">
                                    <h5 class="mb-0">{{$plan->title}}</h5>
                                </div>
                            </a>

                            <div id="collapseOne{{$key+1}}" class="collapse" role="tabpanel" aria-labelledby="headingOne" data-parent="#accordion" style="">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <h3>@lang('users.where')</h3> @foreach($plan->cities as $key=> $city)
                                            <span>{{$city->city_name}}</span>@if($key < count($plan->cities)-1), @endif @endforeach

                                            <h3 class="mt-3">@lang('users.how')</h3> @foreach($plan->airports as $key=> $port)
                                            <span>{{ trim($port['name']) }}</span>@if($key
                                            < count($plan->airports)-1), @endif @endforeach

                                            <h3 class="mt-3">@lang('users.who')</h3> @foreach($plan->users as $key=> $user)
                                            <span>{{trim($user['name'])}}</span>@if($key
                                            < count($plan->users)-1), @endif @endforeach

                                        </div>
                                        <div class="col-lg-4">
                                            <h3>@lang('users.when')</h3> @foreach($plan->destinations as $key=> $dest)
                                            <p><b> {{$dest->name}} </b> {{ date('dS F Y', strtotime($dest->departure_date))}} - {{ date('dS F Y', strtotime($dest->arrival_date))}} <b>{{ $dest->duration }} Days </b> </p>
                                            @endforeach
                                        </div>
                                        <div class="col-lg-4">
                                            <h3>@lang('users.what')</h3> @foreach($plan->packages as $key=> $package)
                                            <span> {{trim($package->name)}}</span>@if($key
                                            < count($plan->packages)-1), @endif @endforeach
                                        </div>
                                        <div class="col-lg-4">

                                        </div>
                                        <div class="col-lg-4">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="col-sm-12">
                        <div class="alert alert-primary mb-0">@lang('users.no_trip')</div>
                    </div>
                    @endif
                </div>

            </div>
            
        </div>
    </div>
</section>
<style>
    .client .client-title span {
        margin: 0 auto;
    }
</style>
@endsection
