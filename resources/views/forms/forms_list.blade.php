
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
              {{ __('Manage Forms') }}
            @endsection
            <div class="card-table">
              <table id="datatable" class="table fixed_header manage-assessments-table">
                <thead>
                  <tr>
                      <th style="vertical-align: middle " scope="col">{{ __('Form Name') }}</th>
                      <th style="vertical-align: middle" scope="col">{{ __('Show Form') }}</th>
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
                  <td class="table-sssf ">
                    <a href={{ url('Forms/ViewForm/'.$form_info->form_id) }}> 
                          <img src="{{url('assets-new/img/solar_eye-bold.png')}}"> {{ __('View Form') }}
                    </a>
                  </td>
                  <?php if (Auth::user()->role != 1): ?>
                  <td>
                    <!-- <a href="{{ route('subforms_list', ['id' => $form_info->form_id]) }}"><span>+ ADD</span></a>         -->
                    <a href="{{ route('subforms_list', ['id' => $form_info->form_id]) }}"><span class="table-sssf "><img src="{{url('assets-new/img/sub-forms.png')}}"> {{ __('Show Sub Forms') }} </span></a>  
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

  @if(auth()->user()->role == 1) 
    <div class="row">
      <div class="col-md-12">
        <div class="tile">
          <div class="table-responsive">
            @if(Session::has('message'))
              <p class="alert alert-info">{{ Session::get('message') }}</p>
            @endif
            @if(Session::has('alert'))
              <p class="alert alert-danger">{{ Session::get('alert') }}</p>
            @endif

            @if(Request::is('Forms/AdminFormsList/audit'))
              <h3 class="tile-title">Audit Forms <a href="{{ route('add_audit_form') }}" class="btn btn-sm btn-success pull-right cust_color" style="margin-right: 10px;"><i class="fa fa-plus" aria-hidden="true"></i>Add New Form</a></h3>
            @else
              <h3 class="tile-title">Assessment Forms <a href="{{ route('add_new_form') }}" class="btn btn-sm btn-success pull-right cust_color" style="margin-right: 10px;"><i class="fa fa-plus" aria-hidden="true"></i>Add New Form</a><a href="{{ route('forms_restore') }}" class="btn btn-sm btn-secondary pull-right" style="margin-right: 10px;"><i class="fa fa-plus" aria-hidden="true"></i>Restore Forms</a><a href="{{ url('/Forms/backup_list') }}" class="btn btn-sm btn-primary pull-right" style="margin-right: 10px;"><i class="fa fa-eye" aria-hidden="true"></i>View Backup Forms</a></h3>
            @endif
            
            <table class="table" id="forms-table">
              <thead class="back_blue">
                <tr>
                  @if(Request::is('Forms/AdminFormsList/audit'))
                    <th scope="col" col-span="2" >Audit Name English </th>
                    <th scope="col" col-span="2" >Audit Name French </th>
                    <th scope="col" col-span="2" >Question Group</th>
                  @else
                    <th scope="col" col-span="2" >Form Name English </th>
                    <th scope="col" col-span="2" >Form Name French </th>
                  @endif
                  <?php if (Auth::user()->role == 1): ?>
                  <th scope="col">Assign to Organization(s)</th>
                  <?php endif; ?>
                  <th scope="col">Show Form</th>
                  @if(Request::is('Forms/AdminFormsList'))
                    <th scope="col">Duplicate</th>
                    <th scope="col">Backup & Restore</th>
                    <th scope="col">Add Questions</th>
                  @endif
                  <?php if (Auth::user()->role != 1): ?>
                  <th scope="col">Sub Forms List</th>
                  <?php endif; ?>
                  <?php if (Auth::user()->role == 2 || Auth::user()->user_type == 1): ?>
                  <th scope="col">Number of Subforms</th>
                  <?php endif; ?>
                  <?php if (Auth::user()->role == 1): ?>
                  <th scope="col">Actions</th>
                  <?php endif; ?>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($forms_list as $form_info): ?>
                <tr>
                  <td>{{ $form_info->title }}</td>
                  <td><?php echo $form_info->title_fr?$form_info->title_fr:$form_info->title ?></td>
                  @if(Request::is('Forms/AdminFormsList/audit'))
                    <td>{{ $form_info->group_name }}</td>
                  @endif
                  @if(Request::is('Forms/AdminFormsList/audit'))
                    <?php if (Auth::user()->role == 1): ?>
                      @php
                      $check = DB::table('forms')
                      ->join('group_section', 'group_section.group_id', 'forms.group_id')
                      ->join('group_questions', 'group_questions.section_id', 'group_section.id')
                      ->where('forms.id', $form_info->form_id)
                      ->count();
                      @endphp
                    <td><a href="{{ url('Audit/Assignees/'.$form_info->form_id) }}" @if($check <= 0) style="pointer-events: none;cursor: default;color:grey;" @endif> <i class="fas fa-tasks"></i> Form Assignees</a></td></td>
                    <?php endif; ?>
                  @else
                    <?php if (Auth::user()->role == 1): ?>
                      @php
                      $check = DB::table('questions')->where('form_id', $form_info->form_id)->count();
                      @endphp
                    <td><a href="{{ url('Forms/FormAssignees/'.$form_info->form_id) }}" @if($check <= 0) style="pointer-events: none;cursor: default;color:grey;" @endif> <i class="fas fa-tasks"></i> Form Assignees</a></td></td>
                    <?php endif; ?>
                  @endif
                  @php
                     $check=DB::table('sub_forms')->where('parent_form_id', $form_info->form_id)->count();
                     $check_que=DB::table('questions')->where('form_id', $form_info->form_id)->count();
                     
                    @endphp
                  @if(Request::is('Forms/AdminFormsList'))
                    <td ><a  href={{ url('Forms/ViewForm/'.$form_info->form_id) }}> <i class="far fa-eye"></i> View Form</a></td></td>
                    <td><a class="btn btn-sm btn-primary" href={{ url('duplicate/'.$form_info->form_id) }}> Duplicate Form</a></td></td>
                    
                    <td><a class="btn btn-sm btn-info" id="backup_item" href="javascript:" onclick="backup('/Forms/backup/{{$form_info->form_id}}')" @if($check_que <= 0) style="pointer-events: none;cursor: default;color:white;background:grey;" @endif>Generate Backup</a></td></td>
                    
                    <td>
                    @if($check > 0 || $form_info->form_id < 14)
                      <a href="{{ url('Forms/'.$form_info->form_id.'/add/questions') }}" class="btn btn-sm btn-success disabled" style="margin-right: 10px;"><i class="fa fa-plus" aria-hidden="true"></i>Add Questions</a>
                    @else
                      <a href="{{ url('Forms/'.$form_info->form_id.'/add/questions') }}" class="btn btn-sm btn-success" style="margin-right: 10px;"><i class="fa fa-plus" aria-hidden="true"></i>Add Questions</a>
                    @endif
                    </td>
                  @else
                    <td><a href={{ url('audit/form/'.$form_info->form_id) }}>
                       <i class="far fa-eye"></i> View Form</a></td></td>
                  @endif
                  <?php if (Auth::user()->role != 1): ?>
                  <td><a href="{{ route('subforms_list', ['id' => $form_info->form_id]) }}"> <i class="fas fa-plus-circle"></i> Add / <i class="fas fa-list"></i> Show Sub Forms</a></td>
                  <?php endif; ?>
                  <?php if (Auth::user()->role == 2 || Auth::user()->user_type == 1): ?>
                  <td>{{ $form_info->subforms_count }}</td>
                  <?php endif; ?>
                  <?php if (Auth::user()->role == 1): ?>
                  @if($check > 0 || $form_info->form_id < 14)
                  <td class="text-center d-flex justify-content-around">
                    <a href="" style="pointer-events: none;color:grey;"> <i class="fas fa-pencil-alt"></i></a>
                    <a href="" style="pointer-events: none;color:grey;"><i class="fas fa-trash"></i></a>
                  </td>
                  @else
                  <td class="text-center d-flex justify-content-around">
                    <a href="{{ url('edit_form/'.$form_info->form_id) }}"> <i class="fas fa-pencil-alt"></i></a>
                    <a href="javascript:" id="delete_item" onclick="submitDelete('delete_form/{{$form_info->form_id}}')"> <i class="fas fa-trash"></i></a>
                  </td>
                  @endif
                  <?php endif; ?>
                  
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div> 
    <script>
      $(document).ready(function(){
          $('#forms-table').DataTable({
                  "order": [],
                  "scrollX": true,
			            "autoWidth": false
          });

          $(function () {
          $('[data-toggle="tooltip"]').tooltip()
        })
      })

      function submitDelete(url){
            swal({
                title:"Are you sure?",
                type: "info",
                showCancelButton: true,
                confirmButtonColor: "#05DD6B",
                confirmButtonText: "YES",
                cancelButtonText: "NO",
                closeOnConfirm: false
            }, function(val){
                if (val){
                    $('#delete_item').attr('href', url);
                    document.getElementById('delete_item').click();
                }
                swal.close();
            })
        }
        function backup(url){
            swal({
                title:"Are you sure you want to take Backup?",
                type: "info",
                showCancelButton: true,
                confirmButtonColor: "#05DD6B",
                confirmButtonText: "YES",
                cancelButtonText: "NO",
                closeOnConfirm: false
            }, function(val){
                if (val){
                    $('#backup_item').attr('href', url);
                    document.getElementById('backup_item').click();
                }
                swal.close();
            })
        }
    </script> 
  @endif

@endsection