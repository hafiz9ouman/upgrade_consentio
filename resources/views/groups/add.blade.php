@extends('admin.layouts.admin_app')
@section('content')

@section('page_title')
    {{ __('Add New  Group') }}
@endsection

<div class="app-title">
    <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{route('groups_list')}}">{{ __('Add New Form Groups') }}</a></li>
    </ul>
</div>
@if (Session::has('message'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>{{ Session::get('message') }}</strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
<form class="w-100" method="POST" action="{{ route('group_save') }}">
    <div class="row bg-white p-3">  
        <div class="col-12 p-3">
            <h3>{{ __('New Question Group') }}</h3>
        </div>
        {{ csrf_field() }}
        <div class="col-md-6">
            <div class="form-group">
                <label for="group_name">{{ __('Group Name En') }}</label>
                <input type="text" name="group_name" class="form-control" value="{{old('group_name')}}">
                @if ($errors->has('group_name'))
                    <span class="text-danger error">* {{ $errors->first('group_name') }}</span>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="group_name_fr">{{ __('Group Name Fr') }}</label>
                <input type="text" name="group_name_fr" class="form-control" value="{{old('group_name_fr')}}">
                @if ($errors->has('group_name_fr'))
                    <span class="text-danger error">* {{ $errors->first('group_name_fr') }}</span>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <button type="submit" class="btn btn-primary" style="max-width: 200px"> Save Group </button>
            </div>
        </div>
    </div>
</form>
@endsection