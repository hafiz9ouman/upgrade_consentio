@extends(($user_type=='1')?('admin.layouts.admin_app'):('admin.client.client_app'))
@section( 'content' )
<style>
  #sub-form-area {
      width:100%;
  }
  .sub-form {
    margin-top:20px;
  }
  .row.sub-form{
    margin-left: 0;
  }
  #sub-form-area .sub-form{
    display: flex;
    flex-direction: row;
        align-items: baseline;
  }
  #sub-form-area .sub-form input,#sub-form-area .sub-form select{
    max-width: 80%;
    
    margin-right: 2%;
  }
  #sub-form-area .sub-form button{
    width: 15%;
  }
  .form-control:focus{
    outline: 0;
    box-shadow: none;
  }
  .btn-primary.focus, .btn-primary:focus,.btn-primary:not(:disabled):not(.disabled).active:focus, .btn-primary:not(:disabled):not(.disabled):active:focus, .show>.btn-primary.dropdown-toggle:focus{
    box-shadow: none;
    outline: 0;
  }
  @media screen and (max-width: 998px){
    #sub-form-area {
      width:100%;
    }
    #sub-form-area .sub-form button{
    width: auto;
    }
  }
  #act-msg {
  display:none; 
  }
  .fs-14 {
      font-size:14px;
  }
  .fs-12 {
      font-size:12px;
  }
  .zdd {
     width: 100% !important; 
  }
  .adding_circle {
    padding: 0px 5px;
    border-radius: 30px;
    font-size: 12px;
    background: #4e73df;
    color: #fff;
  }
</style>
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


<div class="session" style="margin-bottom: 20px">
@if(Session::has('error'))
      <div class="alert alert-danger">
      <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    {{ Session::get('error') }}
    </div>
    @endif
    @if(Session::has('success'))
      <div class="alert alert-success">
      <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    {{ Session::get('success') }}
  </div>
    @endif
     @if(Session::has('alert'))
      <div class="alert alert-danger">
      <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    {{ Session::get('alert') }}
    </div>
    @endif 
</div>
<div class="modal" id="edit-modal">
    <div class="modal-dialog">
      <div class="modal-content">
      
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">{{ __('Edit Subform') }}</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        
        <!-- Modal body -->
        <div class="modal-body">
          <label for="usr">{{ __('Name') }} :</label>
            <input type="text" name="sb-name" id="sb-name" sb-id="" class="form-control zdd">
        </div>
        
        <!-- Modal footer -->
        <div class="modal-footer">
        <div style="text-align:center;">
          <button id="edit-form" type="button" class="btn btn-primary">{{ __('Edit') }}</button>
          </div>
        </div>
        
      </div>
    </div>
  </div>
  {{-- <h3 class="tile-title" style="margin-left: 25px;">Sub Forms <button class="btn btn-primary" id="add" style="float:right; margin-right: 17px;">Add Sub Form</button></h3> --}}
<div class="" style="margin-left:30px; ">
  <?php if ($user_type=='client'): ?>  
<span id='act-msg'><i>{{ __('Please wait while your action is processed') }}</i></span>
<?php endif; ?>
<form method="POST" action="{{ route('gen_subform') }}">
  {{ csrf_field() }}
  <input type="hidden" name="form-id" value="">
  <br>
  <!-- <button type="submit" class="btn btn-primary">Submit</button> -->
</form>
<?php
      $options_list  = '<option value="all">All</option>';

  
      foreach ($client_users as $name)
      {
          $options_list .= '<option value="'.$name.'">'.$name.'</option>';
      }
?>
<div id="sub-form-area"></div>
<?php if ($user_type == 'admin'): ?>
<div class="row">
<div class="col-md-12">
<div class="tile">
<h3  class="tile-title">{{ __('SubForms List') }}</h3>
<div class="table-responsive small-table-width">
<?php endif; ?>

<section class="assets_list">
            <div class="main_custom_table">
                <div class="table_filter_section">
                    <div class="select_tbl_filter">
                        <div class="main_filter_tbl">
                            <p>{{ __('Show')}}</p>
                            <select>
                                <option>10</option>
                                <option>20</option>
                                <option>30</option>
                            </select>
                            <p>{{ __('Entries')}}</p>
                        </div>
                        <div class="add_more_tbl">
                            <a id="add" type="button" class="btn rounded_button"><i class='bx bx-plus mr-1' ></i> {{ __('ADD SUB FORM') }}</a>
                        </div>
                    </div>
                </div>
                <div class="main_table_redisign">
                    {{-- <div class="table_breadcrumb">
                        <h3>INCIDENT REGISTER</h3>
                    </div> --}}
                    @section('page_title')
                    {{ __('SUB FORMS') }}
                    @endsection
                    <div class="over_main_div">
                    <table class="table table-striped text-center">
                               <?php if ($user_type == 'admin'): ?>
    <thead class="back_blue">
    <?php else: ?>
    <thead>
        <?php  endif; ?>
          @if (!empty($sub_forms))
          <tr>
            <th colspan="{{( ( (Auth::user()->role == 2) || (Auth::user()->role == 3) ) || (Auth::user()->user_type == 1) )?(6):(3)}}" style="text-align:center">

              {{-- {{$form_info->title}} Sub Forms --}}
                @if(session('locale')=='fr')
  {{$form_info->title_fr}}
  {{ __('Sub Forms') }}
  @else
  {{$form_info->title}}
  {{ __('Sub Forms') }}
  @endif

            </th>  
          </tr>
          @endif
          <tr>
            <th scope="col">{{ __('Form Name') }}</th>
      @if (Auth::user()->role == 2 || Auth::user()->user_type == 1 || (Auth::user()->role == 3)): 
            <th scope="col">{{ __('External Users') }}</th>     
            <th scope="col">{{ __('Internal Users') }}</th>     
            <th scope="col">{{ __('Sub Form Users') }}</th>
            <th scope="col" style="width:100px">{{ __('Actions') }}</th>
      @endif
            <!--<th scope="col">External Users Forms</th>-->
          </tr>
        </thead>
               <tbody>
    @if (!empty($sub_forms))
      @for ($i = 0; $i < count($sub_forms); $i++)          
          <tr>
            <td><span class="fs-14">

              {{-- {{ $sub_forms[$i]->title }} --}}
              @if(session('locale')=='fr'){{ $sub_forms[$i]->title_fr?$sub_forms[$i]->title_fr:$sub_forms[$i]->title }} @else {{ $sub_forms[$i]->title }} @endif


            </span></td> 
            @php 
        $ex_link_title = '<i class="fas fa-link"></i> Open / <i class="fas fa-arrow-right"></i> Send';
        $in_link_title = 'Send / Show Forms';
      @endphp
      @if (Auth::user()->role == 2 || Auth::user()->user_type == 1 || Auth::user()->role == 3):
      <td>
          <?php
              $count = 0;
              if (isset($sub_forms[$i]->external_users_count))
                  $count =  $sub_forms[$i]->external_users_count;
              //echo $count;
              
              if ($count>=0):
          ?>
              <a class="fs-14" href="{{url('/Forms/OrgSubFormsList/'.$sub_forms[$i]->id.'/?ext_user_only=1')}}"> <span class="adding_circle" style="vertical-align: middle;">{{$count}}</span> {{ __('Assign To') }}</a>
          <?php
              else:
          ?>
              <span class="fs-14">0</span> {{ __('Assign To') }}
          <?php endif; ?>
      </td>     
      
      <td><a class="fs-14" href="{{url('Forms/SubFormAssignees/'.$sub_forms[$i]->id)}}">  <span class="adding_circle" style="vertical-align: middle;"><?php echo (isset($sub_forms[$i]->internal_users_count) && !empty($sub_forms[$i]->internal_users_count))?($sub_forms[$i]->internal_users_count):(0); ?></span> {{ __('Assign To') }}</a></td>
      
      <td><a class="fs-14" href="{{url('/Forms/OrgSubFormsList/'.$sub_forms[$i]->id)}}">  <span style="color: #3fd474;">{{ __('SHOW') }}</span>  </a>
          <span style="color: black;">|</span>
          <a class="fs-14" href="{{url('/Forms/OrgSubFormsList/'.$sub_forms[$i]->id)}}">   <span style="color: #5778ba;">{{ __('SEND FORM') }}</span></a>
      </td>
      <td>

        
                  <div class="action_icons">
                   {{--  <a data-toggle="modal" data-target="#edit-modal" class="fs-14 edit-sb" sb-id="{{$sub_forms[$i]->id}}" sb-name="{{ $sub_forms[$i]->title }}" href="#"> <i class="fas fa-pencil-alt"></i></a>

                     <a class="fs-14 delete-sb" sb-id="{{$sub_forms[$i]->id}}" href="#"><i class="fas fa-minus-circle"></i></a>
 --}}

                          <a data-toggle="modal" data-target="#edit-modal" class="edit-sb" sb-id="{{$sub_forms[$i]->id}}" sb-name="{{ $sub_forms[$i]->title }}"><i class="bx bx-edit"></i></a>
                          <a sb-id="{{$sub_forms[$i]->id}}" class=" delete-sb"><i class="bx bxs-trash"></i></a>
                        </div>
       </td>                 
      @endif
          </tr>
      @endfor
    @endif          
        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </section>


<div class="modal fade" id="practice_modal" tabindex="-1" role="dialog" aria-labelledby="my-modal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">{{ __('Root Cause') }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
      </div>
    </div>
  </div>
</div>
<script>

$(document).ready(function() {
    
    $('#subforms').DataTable();
    
    $('body').on('DOMNodeInserted', 'div', function () {
        console.log('event trig')
        $('div.sub-form input.form-control').each(function(){
            var val = $(this).val();
            $(this).css({'width': val.length+'%'});
        
        });
    });
    
    $('body').on('keyup', 'input.form-control', function () {
            var val = $(this).val();
            //$(this).css({'width': val.length+'%'});
    });    

    
    var assign_list = '<select class="form form-control" disabled>'+'<?php echo $options_list; ?>'+'</select>';
    var counter = {{ $i }};

    $('#add').click(function(){
        counter++;
        console.log("test");
        $('#sub-form-area').append( '<div class="row" style="">'+
                                      '<div style="width:31%">'+
                                        '<label>{{ __("Label in English") }}</label>'+
                                        '<input type="text" class="form-control" id="subform-title-'+counter+'" value="<?php echo $form_info->title; ?>">'+
                                      '</div>'+
                                      '<br>'+
                                      '<div style="width:30%">'+
                                        '<label>{{ __("Label in French") }}</label>'+
                                        '<input style="width:100%" type="text" class="form-control" id="subform-title-fr-'+counter+'" value="<?php echo str_replace("'", '', $form_info->title_fr); ?>">'+
                                      '</div>'+
                                      '<br>'+
                                        '<button style="margin-top:10px;margin-left:2px; margin-bottom:30px;" class="btn rounded_button btn-primary create-subform" id="subform-'+counter+'">{!! __('Create') !!}</button>'+
                                    '</div>');
    });


    var subform_info = {};

    subform_info['_token'] = '{{ csrf_token() }}';

    $(document).on('click', '.create-subform', function (){
        subform_info['users']         = $(this).prev().prev().val();
        subform_info['subform_title'] = $('#subform-title-'+counter).val();
        subform_info['subform_title_fr'] = $('#subform-title-fr-'+counter).val();
        subform_info['form_id']       = {{ $form_info->id }};
        subform_info['client_id']     = {{ Auth::id() }};
    
    $('#add').prop('disabled', true);
    $('.create-subform').prop('disabled', true);
    $('#act-msg').show();
    
        $.ajax({
          url: '{{ route('gen_subform') }}',
          method: 'POST',
          data: subform_info,
          success: function (response) {
        console.log(response);
              console.log(response.msg);
              //alert(response.msg);
        $('#add').prop('disabled',false);
        $('.create-subform').prop('disabled', false); 
        $('#act-msg').hide();
        
        if (response.status == 'success') {
        location.reload();
        }else{
              swal(response.msg , 'error');          
        }
          }

        });
    });
    
    $('.edit-sb').click(function(e){
        e.preventDefault();
        
        $('.modal-body #sb-name').val($(this).attr('sb-name'));
        $('.modal-body #sb-name').attr('sb-id', $(this).attr('sb-id'));
        
        
        var name = $('#sb-name').val();
          
    });
    
    $('#edit-form').click(function(){
 
        var sb_id    = $('#sb-name').attr('sb-id');        
        var sb_name  = $('#sb-name').val();
        
        console.log('sb_id '+sb_id);
        console.log('sb_name '+sb_name);
        
        $.ajax({
            url:'<?php echo url('Forms/EditSubform');  ?>',
            data: {
                'sb-id':sb_id,
                'name':sb_name
            },
            success:function (response) {
                if (response.status == 'success') {
                    
                    swal({
                      title:              "{!! __('Sub-form Updated') !!}",
                      text:               "{!! __('The sub-form information was successfully updated') !!}",
                      type:               "success",
                      showCancelButton:    false,
                      confirmButtonClass: "btn-success",
                      confirmButtonText:  "OK",
                      closeOnConfirm:      true
                    },
                    function(){
                      //swal("Deleted!", "Your imaginary file has been deleted.", "success");
                      location.reload();
                    });                     
                }
                else {
                    var error_msg = __('Something went wrong while updating sub-form');
                    if (response.status) {
                        error_msg = response.msg;
                    }
                    swal('Error', error_msg, 'error');
                }
            }
        });         
        
        
    });
    
    $('.delete-sb').click(function(e){
        e.preventDefault();
        
        //swal('Delete Confirmation')
        
        var sb_id = $(this).attr('sb-id');
        
        swal({
          title:              "{!! __('Are you sure') !!}",
          text:               "{!! __('All associated information including users filled data will be deleted!') !!}",
          type:               "warning",
          showCancelButton:    true,
          confirmButtonClass: "btn-danger",
          cancelButtonClass:  "btn-primary",
          cancelButtonText:   "{!! __('No') !!}",
          confirmButtonText:  "{!! __('Yes, delete it!') !!}",
          closeOnConfirm:     true
        },
        function(){
            $.ajax({
                url:'<?php echo url('Forms/DeleteSubform');  ?>',
                data: {
                    'sb-id':sb_id
                },
                success:function (response) {
                    console.log(response);
                    if (response.status == 'success') {
                        swal({
                          title: "{!! __('Sub-form removed') !!}",
                          text: response.msg,
                          type: "success",
                          showCancelButton: false,
                          confirmButtonClass: "btn-primary",
                          confirmButtonText: "OK",
                          closeOnConfirm: true
                        },
                        function(){
                            location.reload();
                        });                     
                    }
                    else {
                        swal('Error', "{!! __('Something went wrong while deleting form') !!}", 'error');
                    }
                }
            });          
    
        });         
        
        
        

    });
});
</script>

@endsection