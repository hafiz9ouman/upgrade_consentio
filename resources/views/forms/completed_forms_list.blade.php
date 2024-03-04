@extends ('admin.client.client_app')
@section('content')


<style>
  .table {
    margin-bottom: 3rem;
  }
</style>

<section class="assets_list">
      <div class="main_custom_table">
        <div class="table_filter_section">
          <div class="select_tbl_filter">
            <div class="main_filter_tbl">
              <p>{{ __('Show') }}</p>
              <select>
                <option>10</option>
                <option>20</option>
                <option>30</option>
              </select>
              <p>{{ __('Entries') }}</p>
            </div>
            {{-- <div class="add_more_tbl">
              <button type="button" class="btn rounded_button">ADD MORE</button>
            </div> --}}
          </div>
        </div>
        <div class="main_table_redisign">
          @section('page_title')
          {{-- <div class="table_breadcrumb"> --}}
            {{-- <h3> --}}
              {{ __('COMPLETED FORMS') }}
            {{-- </h3> --}}
          @endsection
          <div class="over_main_div ">
            <table class="table table-striped text-center paginated ">
              <thead>
          <tr style = "text-transform:uppercase !important;">
          <th scope="col">{{ __('ASSESSMENT FORM') }}</th>
          <th scope="col">{{ __('USER EMAIL') }}</th>
          <th scope="col">{{ __('USER TYPE') }}</th>
          <th scope="col">{{ __('SUBFORM NAME') }}</th>
          <th scope="col">{{ __('FORM NAME') }}</th>
          <th scope="col" class="fs-12">{{ __('Total Organization Users of this subform') }}</th>
          <th scope="col" class="fs-12">{{ __('Completed Forms (By Organization Users)') }}</th>
          <th scope="col" class="fs-12">{{ __('Total External Users of this subform') }}</th>
          <th scope="col" class="fs-12">{{ __('Completed Forms (By External Users)') }}</th>
          <th scope="col">{{ __('Completed') }}</th>
          <th scope="col">{{ __('Completed On') }}</th>
          </tr>
        </thead>
              <tbody>
                 
                 
                <?php foreach ($completed_forms as $form_info): 
                
                // dd($form_info);
                ?>


                 
                {{-- @php
                dd($form_info);
                @endphp --}}
    <tr>
        <td>
            <?php
                $form_link = ''; 
                // dd($form_info);
                if ($form_info->user_type == 'Internal')
                    $form_link = url('Forms/CompanyUserForm/'.$form_info->form_link);
                if ($form_info->user_type == 'External')
                    $form_link = url('Forms/ExtUserForm/'.$form_info->form_link);
                    
            ?>
            <a class="btn rounded_button td_round_btn" href="<?php echo $form_link; ?>" target="_blank">{{ __('Open') }}</a>
        </td>
        <td><?php echo $form_info->email;  ?></td>
        <td>{!! __($form_info->user_type) !!}</td>
        
        <td>  @if(session('locale') == 'fr' && $form_info->subform_title_fr != null)
            <?php echo $form_info->subform_title_fr; ?>
            @else
            <?php echo $form_info->subform_title; ?>
            @endif

          </td>
        <td>
          @if(session('locale') == 'fr' && $form_info->form_title_fr != null)
          <?php echo $form_info->form_title_fr; ?>
          @else
          <?php echo $form_info->form_title; ?>
          @endif
        </td>
        <td>
            <?php 
                if (isset($form_info->total_internal_users_count ))
                {
                    
                    if($form_info->total_internal_users_count > 0 )
                    {
                      echo $form_info->total_internal_users_count;
                    }
                    else {
                      echo '-';    
                    }
                }
                else
                    echo '-';            
            ?>
        </td>
        <td>
            <?php
                if (isset($form_info->in_completed_forms ))
                {
                  if($form_info->in_completed_forms > 0)
                  {
                    echo $form_info->in_completed_forms;
                  }
                  else{
                    echo '-';   
                  }
                   
                }
                else
                echo '-';  
                             
            ?>            
        </td>
        <td>
         
            <?php
                if (isset($form_info->total_external_users_count ))
                {
                   if($form_info->total_external_users_count > 0 )
                   {
                    echo $form_info->total_external_users_count;
                   }
                   else {
                    echo '-';  
                   }
                   
                }
                else
                    echo '-';            
            ?>
        </td>
        <td>
            <?php
                if (isset($form_info->ex_completed_forms))
                {
                  if($form_info->ex_completed_forms > 0)
                  {
                    echo $form_info->ex_completed_forms;
                  }
                  else{
                    echo '-'; 
                  }
                    
                }
                else
                    echo '-';            
            ?>  
        </td>
        <td>
            <?php
                echo $form_info->is_locked;
            ?>
        </td>
        <td>
            <?php
                echo date('Y-m-d', strtotime($form_info->updated));
            ?>
        </td>        
    </tr>
    <?php endforeach; ?>

                 
              </tbody>
            </table>
            {{-- <div class="table_footer">
              <p>Showing 1 to 9 of 9 entries</p>
              <div class="table_custom_pagination">
                <p class="active_pagination">1</p>
                <p>2</p>
                <p>3</p>
              </div>
            </div> --}}
          </div>
        </div>
      </div>
    </section>

{{-- <script>
    $(document).ready(function(){
        $('#forms-table').DataTable({
                "order": [[ 0, "desc" ]]
        });
    })
</script> --}}
<script>
    $(document).ready(function(){
        var check_col_index = 9;
        var table = $('#forms-table').DataTable({
            "order": [[ 10, "desc" ]],
            "rowCallback": function(row, data) {
                if (data[check_col_index] == "0") {
                    $(row).hide();
                }
            }
        });
        
        table.column(check_col_index).visible(false);
    })
</script>
@endsection