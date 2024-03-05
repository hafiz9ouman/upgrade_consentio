@extends ('admin.client.client_app')
@section('page_title')
  {{ __('MY ASSIGNED AUDITS') }}
@endsection
@section('content')
  <section class="section dashboard">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-table">
            <table class="table fixed_header manage-assessments-table" id="datatable">
              <thead>
                <tr>
                  <th style="vertical-align: middle;" scope="col">Sr NO.</th>
                  <th style="vertical-align: middle;" scope="col">{{ __('Audit Form Name') }}</th>
                  <th style="vertical-align: middle;" scope="col">{{ __('Group Name') }}</th>
                  <th style="vertical-align: middle;" scope="col">{{ __('Asset Number') }}</th>
                  <th style="vertical-align: middle;" scope="col">{{ __('Asset Name') }}</th>
                  <th style="vertical-align: middle;" scope="col">{{ __('Show Form') }}</th>
                  <th style="vertical-align: middle;" scope="col">{{ __('Fill Form') }}</th>
                </tr>
              </thead>
              <tbody>
                  @if(true == false)
                  <tr>
                      <td colspan="4"> No data Found </td>
                  @else
                    @foreach ($sub_forms as $sub_form)
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td> @if(session('locale') == 'fr') {{$sub_form->sub_form_title_fr}} @else {{$sub_form->sub_form_title}}@endif</td>
                        <td> <span style="margin-right: 0px; !important" class="fs-14"> @if(session('locale')=='fr') {{ $sub_form->title_fr ? $sub_form->group_name_fr : $sub_form->group_name }}  @else {{ $sub_form->group_name }} @endif </span> </td> 
                        <td> 
                          <span style="margin-right: 0px; !important" class="fs-14"> 
                            @if(empty($sub_form->other_number))
                              A-{{ $sub_form->client_id }}-{{ $sub_form->asset_number }}
                            @else
                              N-{{ $sub_form->client_id }}-{{ $sub_form->other_number }}
                            @endif
                          </span> 
                        </td> 
                        <td> 
                          <span style="margin-right: 0px; !important" class="fs-14"> 
                            @if(empty($sub_form->other_number))
                              {{ $sub_form->asset_name }} 
                            @else
                              {{ $sub_form->other_id }} 
                            @endif
                          </span> 
                        </td>
                        <td>  <a href="{{ url('audit/form/'.$sub_form->parent_form_id) }}" ><img src="{{url('assets/img/solar_eye-bold.png')}}" alt=""></i> {{ __('View Form') }}</a> </td>
                        <td class="text-center">
                          @if ($sub_form->form_link_id != '')
                            <a href="{{ url('audit/internal/'.$sub_form->form_link_id) }}" class="btn btn-primary td_round_btn" target="_blank" >{{ __('Open')}}</a>
                          @endif
                        </td>
                      </tr>
                    @endforeach
                  @endif
                </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
  <script>
   $(document).ready(function() {
       $('#datatable').DataTable({
         // Disable auto-sort by name
         "order": []
       });
   });
</script>
@endsection
