
@extends (($user_type == 'admin')?('admin.layouts.admin_app'):('admin.client.client_app'))
@section('page_title')
    {{ __('Manage Audits') }}
@endsection
@section('content')

@if(auth()->user()->role != 1)
    <section class="section dashboard">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-table">
                        <table id="datatable" class="table fixed_header manage-assessments-table">
                            <thead>
                                <tr>
                                    <th style="vertical-align: middle;" scope="col">{{ __('Audit Form Name') }}</th>
                                    <th style="vertical-align: middle;" scope="col">{{ __('Group Name') }}</th>
                                    <th style="vertical-align: middle;" scope="col">{{ __('Show Form') }}</th>
                                    <?php if (Auth::user()->role != 1): ?>
                                    <th style="vertical-align: middle;" scope="col">{{ __('Sub Forms List') }}</th>
                                    <?php endif;?>
                                    <?php if (Auth::user()->role == 2 || Auth::user()->user_type == 1): ?>
                                    <th style="vertical-align: middle;" scope="col">{{ __('Reports') }}</th>
                                    <?php endif;?>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($forms_list as $form_info)
                                    <tr>
                                        <td>{{ $form_info->title }}</td>
                                        <td>{{ $form_info->group_name }}</td>
                                        <td>
                                            <a href="{{ route('view_audit_form', $form_info->form_id) }}">
                                            <span class="table-sssf">   
                                            <img src="{{url('assets-new/img/solar_eye-bold.png')}}">{{ __('View Form') }}
                                            </span>     
                                        </a>
                                        </td>
                                        <?php if (Auth::user()->role != 1): ?>
                                        <td>
                                            
                                            <a href="{{ route('audit.sub-form', ['id' => $form_info->form_id]) }}">
                                            <span class="table-sssf">    
                                            <img src="{{url('assets-new/img/sub-forms.png')}}"> {{ __('Show Sub Forms') }} ({{ $form_info->subforms_count }}) 
                                            </span>    
                                        </a>
                                        </td>
                                        <?php endif;?>
                                        <?php if (Auth::user()->role == 2 || Auth::user()->user_type == 1): ?>
                                        <td>
                                            <div class="add_plus_form">
                                                <div class="add_forms">
                                                    <a href="{{ route('report.asset', ['id' => $form_info->group_id]) }}">
                                                    <i class='bx bxs-report'></i> {{ __("Audit") }}</a>
                                                </div>
                                                <div class="add_forms">
                                                    <a href="{{ route('report.onerem', ['id' => $form_info->group_id]) }}">
                                                    <i class='bx bxs-report'></i> {{ __("Remediation") }}</a>
                                                </div>
                                            </div>
                                        </td>
                                        <?php endif;?>
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



@if(auth()->user()->role == 1)
    <div class="row">
        <div class="col-12">
            <div class="card">

                <div class="card-table">


                    @if(Session::has('message'))
                    <p class="alert alert-info">{{ Session::get('message') }}</p>
                    @endif
                    <h3 class="tile-title">Assessment Forms
                        <a href="{{ route('add_new_form') }}" class="btn btn-sm btn-success pull-right cust_color"
                            style="margin-right: 10px;"><i class="fa fa-plus" aria-hidden="true"></i>Add New Form</a>
                    </h3>

                    <table  class="table fixed_header manage-assessments-table" id="forms-table">
                        <thead>
                            <tr>
                                <th scope="col" col-span="2">Form Name English </th>
                                <th scope="col" col-span="2">Form Name French </th>

                                <?php if (Auth::user()->role == 1): ?>
                                <th scope="col">Assign to Organization(s)</th>
                                <?php endif;?>
                                <th scope="col">Show Form</th>
                                <th scope="col">Add Questions</th>
                                <?php if (Auth::user()->role != 1): ?>
                                <th scope="col">Sub Forms List</th>
                                <?php endif;?>
                                <?php if (Auth::user()->role == 2 || Auth::user()->user_type == 1): ?>
                                <th scope="col">Number of Subforms</th>
                                <?php endif;?>
                                <?php if (Auth::user()->role == 1): ?>
                                <th scope="col">Actions</th>
                                <?php endif;?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($forms_list as $form_info): ?>
                            <tr>
                                <td>{{ $form_info->title }}</td>
                                <td><?php echo $form_info->title_fr ? $form_info->title_fr : $form_info->title ?></td>
                                <?php if (Auth::user()->role == 1): ?>
                                <td><a href="{{ url('Forms/FormAssignees/'.$form_info->form_id) }}"> <i
                                            class="fas fa-tasks"></i> Form Assignees</a></td>
                                </td>
                                <?php endif;?>

                                <td><a href={{ url('Forms/ViewForm/'.$form_info->form_id) }}> <i class="far fa-eye"></i>
                                        View Form</a></td>
                                </td>
                                <td>
                                    {{-- @if($form_info->form_id > 14) --}}
                                    <a href="{{ url('Forms/'.$form_info->form_id.'/add/questions') }}"
                                        class="btn btn-sm btn-success pull-right " style="margin-right: 10px;"><i
                                            class="fa fa-plus" aria-hidden="true"></i>Add Questions</a>
                                    {{-- @else --}}
                                    {{-- <strong class="btn btn-warning btn-sm"  data-toggle="tooltip" data-placement="top" title="Unable to Edit Previous Forms"><i class="fa fa-ban" aria-hidden="true "></i>Can't Edit</strong> --}}
                                    {{-- @endif --}}
                                </td>
                                <?php if (Auth::user()->role != 1): ?>
                                <td><a href="{{ route('subforms_list', ['id' => $form_info->form_id]) }}"> <i
                                            class="fas fa-plus-circle"></i> Add / <i class="fas fa-list"></i> Show Sub
                                        Forms</a></td>
                                <?php endif;?>
                                <?php if (Auth::user()->role == 2 || Auth::user()->user_type == 1): ?>
                                <td>{{ $form_info->subforms_count }}</td>
                                <?php endif;?>
                                <?php if (Auth::user()->role == 1): ?>
                                <td><a href="{{ url('edit_form/'.$form_info->form_id) }}"> <i class="fas fa-pencil-alt"></i>
                                        Edit Name</a></td>
                                <?php endif;?>

                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        $('#forms-table').DataTable({
            "order": [
                [0, "desc"]
            ]
        });
        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        })
    })
    </script>
@endif

@endsection