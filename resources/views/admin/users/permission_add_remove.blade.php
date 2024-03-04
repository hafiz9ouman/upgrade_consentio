@extends('admin.layouts.admin_app')
@section('content')

  <style>
    .switch {
      position: relative;
      display: inline-block;
      width: 60px;
      height: 34px;
    }

    .switch input { 
      opacity: 0;
      width: 0;
      height: 0;
    }

    #size{
        margin-left: 58px;
        width: 168px;
        padding: 3px;
    }

    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #222d32;
      -webkit-transition: .4s;
      transition: .4s;
    }
    input:checked + .slider {
        background-color: #1cc88a !important;
    }
    .slider:before {
      position: absolute;
      content: "";
      height: 26px;
      width: 26px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      -webkit-transition: .4s;
      transition: .4s;
    }

    input:checked + .slider {
      background-color: #2196F3;
    }

    input:focus + .slider {
      box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
      -webkit-transform: translateX(26px);
      -ms-transform: translateX(26px);
      transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
      border-radius: 34px;
    }

    .slider.round:before {
      border-radius: 50%;
    }
    .max-spec {
        font-size:14px;
    }
    .card-body-form {
        width: 100%;
    }
    .form-group {
        display: block; 
        flex-direction: column; 
    }
    .form-btn a , .form-btn button {
      font-size: 18px;

    }
    .add_color {
          background: #0f75bd;
        font-size: 19px;
        color: #fff;
    }
  </style>

  <div class="app-title">
    <ul class="app-breadcrumb breadcrumb">
      <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i>
      </li>
      <li class="breadcrumb-item"><a href="{{url('/admin')}}">Organization Admins </a>
      </li>
      <li class="breadcrumb-item"><a href="{{url('/users/add')}}">Add Organization Administrator</a>
      </li>		
    </ul>
  </div>

  @if (session('alert'))
      <div class="alert alert-danger">
          {{ session('alert') }}
      </div>
  @endif
  @if ($errors->any())
      <div class="alert alert-danger">
          <ul>
              @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
  @endif


  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-header d-flex align-items-center">
          <h3 class="h4">Add/Remove Permissions </h3>
        </div>
        <div class="card-body" id="org-form">
          <div class="card-body-form">
            <form class="form-horizontal" method="POST" action="{{ url('users/permissions/store') }}" enctype="multipart/form-data">
              {{ csrf_field() }}
              <table class="table">  
                <tbody>
                  <tr>
                    <th class="add_color">Modules</th>
                    <td class="add_color">Permission</td>
                  @foreach($permissions as $total_permissions)
                    <tr>
                      <th>{{ $total_permissions->module_title }}</th>
                      <td>
                        <label class="switch">
                          <input type="checkbox" name="permiss[]" value="{{$total_permissions->module}}"  @if(in_array($total_permissions->module, $granted_permissions) == true) checked="true" @endif>
                          <span class="slider round"></span>
                        </label>
                      </td>
                    </tr>
                  @endforeach
                  </tr>
                </tbody>
              </table>
              <input id="file" type="hidden" class="form-control" name="id" value="{{$id}}">
              <div class="form-group row form-btn">
                <div class="col-sm-12 text-right">
                  <a href="{{url('admin')}}" class="btn btn-sm btn-secondary">@lang('general.cancel')</a>
                  <button type="submit" class="btn btn-sm btn-primary">@lang('general.save') </button>
                </div>
              </div>	
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection