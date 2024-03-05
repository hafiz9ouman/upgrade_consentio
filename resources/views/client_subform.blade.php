@extends ('admin.client.client_app')
@section('content')

<section class="section dashboard">
      <div class="row">
        <div class="col-12">
          <div class="card">
          @section('page_title')
          {{-- <div class="table_breadcrumb"> --}}
            {{-- <h3> --}}
              {{ __('MY ASSIGNED FORMS') }}
            {{-- </h3> --}}
          @endsection
          <div class="card-table">
            <table id="datatable" class="table fixed_header manage-assessments-table">
              <thead>
                <tr>
                  <th style="vertical-align: middle;" scope="col">Sr NO.</th>
                  <th style="vertical-align: middle;" scope="col">{{ __('Form Name') }}</th>
                  <!-- <th style="vertical-align: middle;" scope="col">{{ __('Show Form') }}</th> -->
                  <th style="vertical-align: middle;" scope="col">{{ __('Fill Form') }}</th>
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
                    <!-- <td><a href={{ url('Forms/ViewForm/'.$sub_forms[$i]->parent_form_id) }} > <img src="{{url('assets-new/img/solar_eye-bold.png')}}"></i> {{ __('View Form') }}</a></td></td> -->
                    <td class="text-center">
                        @if ($sub_forms[$i]->form_link_id != '')
                        <a href="{{ url('Forms/CompanyUserForm/'.$sub_forms[$i]->form_link_id) }}" class="btn btn-primary td_round_btn" target="_blank" >{{ __('Open')}}</a>
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