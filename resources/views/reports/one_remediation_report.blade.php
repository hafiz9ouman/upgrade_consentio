@extends('admin.client.client_app')
@section('page_title')
{{ __('Remediation Report') }}
@endsection
@section('content')
<style>
    body{
        color:black;
    }
    #main {
        margin-top: 22px;
    }
</style>

<div class="container-fluid" style="background-color: white;">
    <object data="{{ url('/dash/remediation/' . $group[0]->id) }}" style="width: 100%; height:110vh;border:none;"></object>
</div>
@endsection