
@extends (($user_type == 'admin')?('admin.layouts.admin_app'):('admin.client.client_app'))
@section('page_title')
    {{ __('Manage Audits') }}
@endsection
@section('content')
    <style>
    @media screen and (max-width: 580px) {
        .add_responsive {
            overflow: scroll;
            display: block;
        }
    }
    </style>

@if(auth()->user()->role != 1)
    <section class="assets_list">
        <div class="main_custom_table">
            <div class="table_filter_section">
                <div class="select_tbl_filter">
                </div>
            </div>
            <div class="main_table_redisign">
                <div class="over_main_div no_scroll">
                    <table class="table table-striped text-center add_responsive">
                        <thead>
                            <tr>
                                <th scope="col">{{ __('Audit Form Name') }}</th>
                                <th scope="col">{{ __('Group Name') }}</th>
                                <th scope="col">{{ __('Show Form') }}</th>
                                <?php if (Auth::user()->role != 1): ?>
                                <th scope="col">{{ __('Sub Forms List') }}</th>
                                <?php endif;?>
                                <?php if (Auth::user()->role == 2 || Auth::user()->user_type == 1): ?>
                                <th scope="col">{{ __('Number Of Subforms') }}</th>
                                <?php endif;?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($forms_list as $form_info): ?>
                            <tr>

                                <td>{{ $form_info->title }}</td>
                                <td>{{ $form_info->group_name }}</td>
                                <td>
                                    <div class="vie_form">
                                        <a href="{{ route('view_audit_form', $form_info->form_id) }}">
                                            <p><i class='bx bx-show-alt'></i>{{ __('View Form') }}</p>
                                        </a>
                                    </div>
                                </td>
                                <?php if (Auth::user()->role != 1): ?>
                                <td>
                                    <div class="add_plus_form">
                                        <div class="add_forms">
                                            <a href="{{ route('report.asset', ['id' => $form_info->group_id]) }}">
                                            <i class='bx bxs-report'></i> {{ __("Report") }}</a>
                                        </div>
                                        <div class="show_sub_forms">
                                            <a href="{{ route('audit.sub-form', ['id' => $form_info->form_id]) }}">
                                            <i class='bx bx-list-ul'></i> {{ __('Show Sub Forms') }}</a>
                                        </div>
                                    </div>
                                </td>
                                <?php endif;?>
                                <?php if (Auth::user()->role == 2 || Auth::user()->user_type == 1): ?>
                                <td>{{ $form_info->subforms_count }}</td>
                                <?php endif;?>

                            <tr>
                                <?php endforeach;?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endif



@if(auth()->user()->role == 1)
    <div class="row" style="margin-left:10px;">
        <div class="col-md-12">
            <div class="tile">

                <div class="table-responsive cust-table-width">


                    @if(Session::has('message'))
                    <p class="alert alert-info">{{ Session::get('message') }}</p>
                    @endif
                    <h3 class="tile-title">Assessment Forms
                        <a href="{{ route('add_new_form') }}" class="btn btn-sm btn-success pull-right cust_color"
                            style="margin-right: 10px;"><i class="fa fa-plus" aria-hidden="true"></i>Add New Form</a>
                    </h3>

                    <table class="table" id="forms-table">
                        <thead class="back_blue">
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