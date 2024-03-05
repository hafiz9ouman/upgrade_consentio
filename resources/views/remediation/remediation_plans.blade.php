@extends ('admin.client.client_app')
@section('content')
@section('page_title')
    {{ __('Remediation List') }}
@endsection
    <style>
        .back_blue {
            background-color: #0f75bd !important;
            color:#fff;
        }
    </style>
    <section class="section dashboard">
        <div class="row bg-white">
            <div class="col-12 overflow-auto p-3">
                <div class="card">
                <div class="p-3">
                                <a class="button float-right" href="{{ route('report.remediation') }}">
                                <img src="{{url('assets-new/img/grr.png')}}" alt=""> {{ __("Global Remediation Report") }}</a>
                        </div>
                    <div class="card-table">
                        
                        <table class="table fixed_header manage-assessments-table" id="datatable" style="min-width:700px">
                            <thead class="">
                                <tr>
                                    <th style="vertical-align: middle;"> # </th>
                                    <th style="vertical-align: middle;"> {{__('Audit Form Name')}} </th>
                                    <th style="vertical-align: middle;"> {{__('Group Name')}} </th>
                                    <th style="vertical-align: middle;"> {{__('Asset Number')}} </th>
                                    <th style="vertical-align: middle;"> {{__('Asset Name')}} </th>
                                    <th style="vertical-align: middle;"> {{__('Action')}} </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($remediation_plans as $plan)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td> @if(session('locale')=='fr') {{ $plan->form_title_fr }}  @else {{ $plan->form_title }} @endif </td>
                                        <td> @if(session('locale')=='fr') {{ $plan->group_name_fr }}  @else {{ $plan->group_name }} @endif </td>
                                        <td>
                                            @if($plan->type == "others")
                                                N-
                                            @else
                                                A-
                                            @endif
                                            {{$plan->client_id}}-{{$plan->asset_number}}</td>
                                        <td>{{ $plan->name }}</td>
                                        <td><a href="{{ route('single_remediation', $plan->sub_form_id) }}" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i></a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    </div>
                    </div>
        </div>
    </section>
@endsection
