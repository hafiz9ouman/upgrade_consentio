@extends ('admin.client.client_app')
@section('content')


<section class="assets_list">
      <div class="row">
        <div class="col-12">
          <div class="card">
          @section('page_title')
          {{-- <div class="table_breadcrumb"> --}}
            {{-- <h3> --}}
              {{ __('COMPLETED SAR FORMS') }}
            {{-- </h3> --}}
          @endsection
          <div class="card-table">
            <table id="datatable" class="table fixed_header manage-assessments-table">
              <thead>
                <tr style = "text-transform:uppercase !important;">
                  <th style="vertical-align: middle;" scope="col">{{ __('FORM LINK') }}</th>
                  <th style="vertical-align: middle;" scope="col">{{ __('USER EMAIL') }}</th>
                  <th style="vertical-align: middle;" scope="col">{{ __('USER TYPE') }}</th>
                  <th style="vertical-align: middle;" scope="col">{{ __('SUBFORM NAME') }}</th>
                  <th style="vertical-align: middle;" scope="col">{{ __('FORM NAME') }}</th>
                  <!-- <th style="vertical-align: middle;" scope="col" class="fs-12">{{ __('Total Organization Users of this subform') }}</th>
                  <th style="vertical-align: middle;" scope="col" class="fs-12">{{ __('Completed Forms (By Organization Users)') }}</th>
                  <th style="vertical-align: middle;" scope="col" class="fs-12">{{ __('Total External Users of this subform') }}</th>
                  <th style="vertical-align: middle;" scope="col" class="fs-12">{{ __('Completed Forms (By External Users)') }}</th> -->
                  <!-- <th style="vertical-align: middle;" scope="col">{{ __('Completed') }}</th> -->
                  <th style="vertical-align: middle;" scope="col">{{ __('Completed On') }}</th>
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
            <a class="btn btn-primary td_round_btn" href="<?php echo $form_link; ?>" target="_blank">{{ __('Open') }}</a>
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
        <!-- <td>
            @php
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
            @endphp
        </td> -->
        <!-- <td>
            @php
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
                             
            @endphp          
        </td> -->
        <!-- <td>
         
            @php
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
            @endphp
        </td> -->
        <!-- <td>
            @php
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
            @endphp  
        </td> -->
        <!-- <td>
            @php
                echo $form_info->is_locked;
            @endphp
        </td> -->
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
      </div>
    </section>


<script>
    $(document).ready(function() {
      $('#datatable').DataTable({
        "order": []
      });
    });
</script>
@endsection