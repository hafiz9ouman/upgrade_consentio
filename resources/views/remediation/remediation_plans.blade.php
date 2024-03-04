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
    <section class="assets_list">
        <div class="row bg-white">
            <div class="col-12 overflow-auto p-3">
                <div class="p-3">
                        <a class="btn btn-primary" href="{{ route('report.remediation') }}">
                        <i class='bx bxs-report'></i> {{ __("Global Remediation Report") }}</a>
                </div>
                <table class="table" id="remediation_plans" style="min-width:700px">
                    <thead class="back_blue">
                        <tr>
                            <th> # </th>
                            <th> Audit </th>
                            <th> Question Group </th>
                            <th> ITEM # </th>
                            <th> AUDIT ITEM </th>
                            <th> Action </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($remediation_plans as $plan)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td> @if(true == false) {{ $plan->form_title_fr }}  @else {{ $plan->form_title }} @endif </td>
                                <td> @if(true == false) {{ $plan->group_name_fr }}  @else {{ $plan->group_name }} @endif </td>
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
    </section>
@endsection
@push('scripts')
    <script>
        $(function(){
            $('#remediation_plans').DataTable();
        });
    </script>
@endpush