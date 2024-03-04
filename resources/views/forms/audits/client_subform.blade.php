@extends ('admin.client.client_app')
@section('page_title')
  {{ __('MY ASSIGNED AUDITS') }}
@endsection
@section('content')
  <style>
    .table {
      margin-bottom: 2rem;
    }
    .over_main_div .pager {
        position: absolute;
        bottom: -35px;
        right: 16px;
    }
    @media screen and (max-width: 580px) {
      .main_responsive_table {
        display: block;
        overflow: scroll;
      }
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
        </div>
      </div>
      <div class="main_table_redisign">
        
        <div class="over_main_div no_scroll">
          <table class="table table-striped text-center paginated main_responsive_table">
            <thead>
              <tr>
                <th scope="col">Sr NO.</th>
                <th scope="col">{{ __('Audit Form Name') }}</th>
                <th scope="col">{{ __('Group Name') }}</th>
                <th scope="col">{{ __('Asset Number') }}</th>
                <th scope="col">{{ __('Asset Name') }}</th>
                <th scope="col">{{ __('Show Form') }}</th>
                <th scope="col">{{ __('Fill Form') }}</th>
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
                      <td> <span class="fs-14"> @if(session('locale')=='fr') {{ $sub_form->title_fr ? $sub_form->group_name_fr : $sub_form->group_name }}  @else {{ $sub_form->group_name }} @endif </span> </td> 
                      <td> 
                        <span class="fs-14"> 
                          @if(empty($sub_form->other_number))
                            A-{{ $sub_form->client_id }}-{{ $sub_form->asset_number }}
                          @else
                            N-{{ $sub_form->client_id }}-{{ $sub_form->other_number }}
                          @endif
                        </span> 
                      </td> 
                      <td> 
                        <span class="fs-14"> 
                          @if(empty($sub_form->other_number))
                            {{ $sub_form->asset_name }} 
                          @else
                            {{ $sub_form->other_id }} 
                          @endif
                        </span> 
                      </td>
                      <td>  <a href="{{ url('audit/form/'.$sub_form->parent_form_id) }}" ><i class="far fa-eye"></i> {{ __('View Form') }}</a> </td>
                      <td class="text-center">
                        @if ($sub_form->form_link_id != '')
                          <a href="{{ url('audit/internal/'.$sub_form->form_link_id) }}" class="" target="_blank" >{{ __('Open')}}</a>
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
  </section>
@endsection
