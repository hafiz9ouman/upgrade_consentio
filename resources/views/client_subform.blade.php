@extends ('admin.client.client_app')
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
            {{-- <div class="add_more_tbl">
              <button type="button" class="btn rounded_button">ADD MORE</button>
            </div> --}}
          </div>
        </div>
        <div class="main_table_redisign">
          @section('page_title')
          {{-- <div class="table_breadcrumb"> --}}
            {{-- <h3> --}}
              {{ __('MY ASSIGNED FORMS') }}
            {{-- </h3> --}}
          @endsection
          <div class="over_main_div no_scroll">
            <table class="table table-striped text-center paginated main_responsive_table">
              <thead>
          <tr>
      <th scope="col">Sr NO.</th>
            <th scope="col">{{ __('Form Name') }}</th>
      <th scope="col">{{ __('Show Form') }}</th>
      <th scope="col">{{ __('Fill Form') }}</th>
          </tr>
        </thead>
              <tbody>
                @if (!empty($sub_forms))
      @for ($i = 0; $i < count($sub_forms); $i++)          
          <tr>
            <td>{{ $i + 1 }}</td>   
            <td>
              @if($sub_forms[$i]->title_fr != null && session('locale')=='fr')
              {{ $sub_forms[$i]->title_fr }}
              @elseif($sub_forms[$i]->title_fr == null && session('locale')=='fr')
              {{ $sub_forms[$i]->title }}
              @elseif (session('locale')=='en')
              {{ $sub_forms[$i]->title }}
              @endif</td>
      <td><a href={{ url('Forms/ViewForm/'.$sub_forms[$i]->parent_form_id) }} > <i class="far fa-eye"></i> {{ __('View Form') }}</a></td></td>
      <td class="text-center">
          @if ($sub_forms[$i]->form_link_id != '')
          <a href="{{ url('Forms/CompanyUserForm/'.$sub_forms[$i]->form_link_id) }}" class="" target="_blank" >{{ __('Open')}}</a>
                @endif
            </td>
          </tr>
      @endfor
    @endif     
                 


                 
              </tbody>
            </table>
           {{--  <div class="table_footer">
              <p>{{ __('Showing') }} 1 to 9 of 9 entries</p>
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

@endsection