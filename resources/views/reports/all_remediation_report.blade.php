@extends('admin.client.client_app')
@section('page_title')
{{ __('Global Remediation Report') }}
@endsection
@section('content')
<style>
    body{
        color:black;
    }
</style>

<div class="container-fluid" style="background-color: white;">
    <object data="{{ url('/dash/global') }}" style="width: 100%; height:110vh;border:none;"></object>
</div>
@endsection