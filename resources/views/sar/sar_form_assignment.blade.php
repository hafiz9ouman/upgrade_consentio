
@extends (($user_type == 'admin')?('admin.layouts.admin_app'):('admin.client.client_app'))
@section('content')
  @if ($user_type == 'admin')
    <div class="app-title">
      <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i>
        </li>
        @if(Request::is('Forms/AdminFormsList/audit'))
          <li class="breadcrumb-item"><a href="{{url('Forms/AdminFormsList/audit')}}">{{ __('Manage Audit Forms') }}</a>
        @else
          <li class="breadcrumb-item"><a href="{{route('admin_forms_list')}}">{{ __('Manage Assessment Forms') }}</a>
        @endif
        </li>
      </ul>
    </div>
  @endif

  @if(auth()->user()->role != 1)
    <section class="section dashboard">
      <div class="row">
        <div class="col-12">
          <div class="card">
            @section('page_title')
              {{ __('SAR FORM ASSIGNEES') }}
            @endsection
            <div class="card-table">
              <table id="datatable" class="table fixed_header manage-assessments-table">
                <thead>
                  <tr>
                      <th style="vertical-align: middle;" scope="col">{{ __('FORM NAME') }}</th>
                      <th style="vertical-align: middle;" scope="col">{{ __('Show Form') }}</th>
                      <?php if (Auth::user()->role != 1): ?>
                      <th style="vertical-align: middle;" scope="col">{{ __('Sub Forms List') }}</th>
                      <?php endif; ?>
                      <?php if (Auth::user()->role == 2 || Auth::user()->user_type == 1): ?>
                      <th style="vertical-align: middle;" scope="col">{{ __('Number Of Subforms') }}</th>
                      <?php endif; ?>
                  </tr>
                </thead>
                <tbody>
                @foreach($forms_list as $form_info)
                <tr>
                  <td>{{ $form_info->title }}</td>
                  <td>
                    <a href={{ url('Forms/ViewForm/'.$form_info->form_id) }}> 
                          
                    
                    <span class="table-sssf">
                    <img src="{{url('assets-new/img/solar_eye-bold.png')}}"> {{ __('View Form') }}
                    </span>  
                  </a>
                  </td>
                  <?php if (Auth::user()->role != 1): ?>
                  <td>      
                    <a href="{{ route('sar_subforms_list', ['id' => $form_info->form_id]) }}"><span class="table-sssf"><img src="{{url('assets-new/img/sub-forms.png')}}"> {{ __('Show Sub Forms') }} </span></a>  
                  </td>
                  <?php endif; ?>
                  <?php if (Auth::user()->role == 2 || Auth::user()->user_type == 1): ?>
                  <td>{{ $form_info->subforms_count }}</td>
                  <?php endif; ?>
                </tr>
                @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
  @endif

@endsection