@extends (($user_type == 'admin')?('admin.layouts.admin_app'):('admin.client.client_app'))
@section('content')
<style>
    #map_wrapper {
      height: 400px;
      margin-bottom: 2rem;
  }

  #map_canvas {
      width: 100%;
      height: 100%;
  }
    .mapouter {
      margin-bottom: 2rem;
  }

  .mapouter,
  .gmap_canvas {
      width: auto !important;
      height: 100% !important;
  }

  .mapouter iframe {
      width: 100% !important;

  }

  .mapouter {
      position: relative;
      text-align: right;
      height: 500px;
  }
  .gmap_canvas {
      overflow: hidden;
      background: none !important;
      height: 500px;
      width: 600px;
      border-radius: 20px;
      /*-webkit-filter: grayscale(100%) !important;*/
      /* -moz-filter: grayscale(100%)!important;
                -ms-filter: grayscale(100%) !important;
                -o-filter: grayscale(100%) !important;*/
      /*filter: grayscale(100%) !important;*/
  }
  @media screen and (max-width:640px) {
    .response{
        display: inline-grid !important;
    }
    .buton{
        margin: 1px !important;
    }
  }
</style>
    @if (isset($data))
        
        @section('page_title')
            {{ __('EDIT ASSET') }}
        @endsection
        <div class="card custom_Efdit_card" style="">
            <div class="card-body">
                <form action="{{ route('update_asset') }}" onsubmit="return get_location_assetsz();" method="POST"
                    enctype="multipart/form-data" id="update_asset_locz">

                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="sel1">{{ __('Asset type') }}<span class="red">*</span></label>
                        <select class="form-control" name="asset_typez" required id="sel1">
                            
                            <option value="Server" {{ $data->asset_type === "Server" ? "selected" : "" }}>{{ __('Server') }}</option>
                            <option value="Application" {{ $data->asset_type === "Application" ? "selected" : "" }}>{{ __('Application') }}</option>
                            <option value="Database" {{ $data->asset_type === "Database" ? "selected" : "" }}>{{ __('Database') }}</option>
                            <option value="Physical Storage" {{ $data->asset_type === "Physical Storage" ? "selected" : "" }}>{{ __('Physical Storage') }}</option>
                            <option value="Website" {{ $data->asset_type === "Website" ? "selected" : "" }}>{{ __('Website') }}</option>
                            <option value="Other" {{ $data->asset_type === "Other" ? "selected" : "" }}>{{ __('Other') }}</option>
                        </select>
                    </div>
                    <input type="hidden" name="id" value="{{ $data->id }}" id="as_id_up">
                    <div class="form-group">
                        <label>{{ __('Assets Name') }}<span class="red">*</span></label>
                        <input type="text" name="namez" value="{{ $data->name }}" class="form-control" required
                            disabled>
                    </div>

                    <div class="form-group">
                        <div class='input-field'>
                            <label>{{ __('Hosting Type') }}<span class="red">*</span></label>

                            <select class="form-control" required name='hosting_typez'>
                                
                                <option value="Cloud" {{ $data->hosting_type === "Cloud" ? "selected" : "" }}>{{ __('Cloud') }}</option>
                                <option value="On-Premise" {{ $data->hosting_type === "On-Premise" ? "selected" : "" }}>{{ __('On-Premise') }}</option>
                                <option value="Not Sure" {{ $data->hosting_type === "Not Sure" ? "selected" : "" }}>{{ __('Not Sure') }}</option>
                                <option value="Hybrid" {{ $data->hosting_type === "Hybrid" ? "selected" : "" }}>{{ __('Hybrid') }}</option>

                            </select>
                        </div>
                    </div>



                    <div class="form-group">
                        <label>{{ __('Hosting Provider') }} </label>
                        <input type="text" name="hosting_providerz" value="{{ $data->hosting_provider }}"
                            class="form-control">
                    </div>
                    <div class="form-group">
                        <div class='input-field'>
                            <label for='country'>{{ __('Hosting Country') }}<span class="red">*</span></label>
                            <select id='country_selectz' class="form-control" required name='countryz'>
                                @if (isset($cont[0]->country_name))
                                    <option value="{{ $cont[0]->country_name }}">{{ __($cont[0]->country_name) }}</option>
                                @endif
                                @foreach ($countries as $country)
                                    <option>{{ __($country->country_name) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>{{ __('Hosting City') }} </label>
                        <input type="text" id="citiz" name="cityzz" value="{{ $data->city }}"
                            class="form-control">
                    </div>
                    <div class="form-group">
                        <label>{{ __('State') }}/{{ __('Province') }} </label>
                        <input type="text" name="statez" value="{{ $data->state }}" class="form-control">
                    </div>
                    <div class="form-gourp">
                                <label for="">{{ __('Impact') }}<span class="red">*</span></label>
                                <select name="impact" id="impact_name_up" class="form-control for_change">
                                    @foreach ($impact as $imp)
                                        <option value="{{ $imp->id }}" {{ $imp->id == $data->impact_id ? "selected" : "" }}> @if(session('locale') == 'fr') {{ $imp->impact_name_fr }} @else {{ $imp->impact_name_en }} @endif</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-gourp">
                                <label for="">{{ __('Data Classification') }}<span class="red">*</span></label>
                                <select name="data_classification" id="classification_name_up"
                                    class="form-control for_change">
                                    @foreach ($dt_classification->take(5) as $dc)
                                        <option value="{{ $dc->id }}"  {{ $dc->id == $data->data_classification_id ? "selected" : "" }}> @if(session('locale') == 'fr') {{ $dc->classification_name_fr }} @else {{ $dc->classification_name_en }} @endif</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <div class='input-field'>
                                    <label for='country'>{{ __('Category (Asset Tier)') }}<span class="red">*</span></label>
                                    <select id='tier_sub_field_up' class="form-control" required name='tier_sub_filed'>
                                        <option value="Crown Jewels" {{ $data->tier == "Crown Jewels" ? "selected" : "" }}> {{__('Crown Jewels')}}</option>
                                        <option value="tier 1" {{ $data->tier == "tier 1" ? "selected" : "" }}> {{__('Tier 1')}}</option>
                                        <option value="tier 2" {{ $data->tier == "tier 2" ? "selected" : "" }}> {{__('Tier 2')}}</option>
                                        <option value="tier 3" {{ $data->tier == "tier 3" ? "selected" : "" }}> {{__('Tier 3')}}</option>
                                    </select>
                                </div>
                            </div>
                    

                    <div class="form-group">
                        <label for="">{{ __('Business Unit') }}</label>
                        <input type="text" id="business_unit" name="business_unit" class="form-control" value="{{ $data->business_unit }}">
                    </div>

                    <div class="form-gourp">
                        <label for="">{{ __('IT Owner') }}</label>
                        <input type="text" id="it_owner" name="it_owner" value="{{ $data->it_owner }}"
                            class="form-control">
                    </div>
                    <div class="form-gourp">
                        <label for="">{{ __('Business Owner') }}</label>
                        <input type="text" id="business_owner" name="Business_owner" value="{{ $data->business_owner }}"
                            class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">{{ __('Internal or 3rd party') }}</label>
                        
                        <select id='internal_3rd_party' class="form-control" required name='internal_3rd_party'>
                            <option value="internal" {{ $data->internal_3rd_party === "internal" ? "selected" : "" }}>{{__('Internal')}}</option>
                            <option value="3rd Party Provider" {{ $data->internal_3rd_party === "3rd Party Provider" ? "selected" : "" }}>{{__('3rd Party Provider')}}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">{{ __('Volume of Sensitive Data') }}</label>
                        <select class="form-control" required id="data_subject_volume" name='data_subject_volume'>
                            <option value="0-100" {{ $data->data_subject_volume === "0-100" ? "selected" : "" }}>{{ __('0-100') }}</option>
                            <option value="100-500" {{ $data->data_subject_volume === "100-500" ? "selected" : "" }}>{{ __('100-500') }}</option>
                            <option value="500-1,000" {{ $data->data_subject_volume === "500-1,000" ? "selected" : "" }}>{{ __('500-1,000') }}</option>
                            <option value="1,000-10,000" {{ $data->data_subject_volume === "1,000-10,000" ? "selected" : "" }}>{{ __('1,000-10,000') }}</option>
                            <option value="10,000-100,000" {{ $data->data_subject_volume === "10,000-100,000" ? "selected" : "" }}>{{ __('10,000-100,000') }}</option>
                            <option value="100,000-500,000" {{ $data->data_subject_volume === "100,000-500,000" ? "selected" : "" }}>{{ __('100,000-500,000') }}</option>
                            <option value="500,000-1M" {{ $data->data_subject_volume === "500,000-1M" ? "selected" : "" }}>{{ __('500,000-1M') }}</option>
                            <option value="1M - 5M" {{ $data->data_subject_volume === "1M - 5M" ? "selected" : "" }}>{{ __('1M - 5M') }}</option>
                            <option value="5M - 10M" {{ $data->data_subject_volume === "5M - 10M" ? "selected" : "" }}>{{ __('5M - 10M') }}</option>
                            <option value="10M - 50M" {{ $data->data_subject_volume === "10M - 50M" ? "selected" : "" }}>{{ __('10M - 50M') }}</option>
                            <option value="50M - 100M" {{ $data->data_subject_volume === "50M - 100M" ? "selected" : "" }}>{{ __('50M - 100M') }}</option>
                            <option value="100M+" {{ $data->data_subject_volume === "100M+" ? "selected" : "" }}>{{ __('100M+') }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">{{ __('Supplier') }}</label>
                        <input type="text" id="supplier" name="supplier" class="form-control" value="{{ $data->supplier }}">
                    </div>
                    <div class="form-group">
                        <label for="">{{ __('Data Retention') }}</label>
                        <select class="form-control" id="data_retention" name='data_retention'>
                            <option value="0-30 days" {{ $data->data_retention === "0-30 days" ? "selected" : "" }}>{{ __('0-30 days') }}</option>
                            <option value="30-90 days" {{ $data->data_retention === "30-90 days" ? "selected" : "" }}>{{ __('30-90 days') }}</option>
                            <option value="3-6 months" {{ $data->data_retention === "3-6 months" ? "selected" : "" }}>{{ __('3-6 months') }}</option>
                            <option value="6-12 months" {{ $data->data_retention === "6-12 months" ? "selected" : "" }}>{{ __('6-12 months') }}</option>
                            <option value="1-3 years" {{ $data->data_retention === "1-3 years" ? "selected" : "" }}>{{ __('1-3 years') }}</option>
                            <option value="3-5 years" {{ $data->data_retention === "3-5 years" ? "selected" : "" }}>{{ __('3-5 years') }}</option>
                            <option value="5-7 years" {{ $data->data_retention === "5-7 years" ? "selected" : "" }}>{{ __('5-7 years') }}</option>
                            <option value="7-10 years" {{ $data->data_retention === "7-10 years" ? "selected" : "" }}>{{ __('7-10 years') }}</option>
                            <option value="10-12 years" {{ $data->data_retention === "10-12 years" ? "selected" : "" }}>{{ __('10-12 years') }}</option>
                            <option value="12-15 years" {{ $data->data_retention === "12-15 years" ? "selected" : "" }}>{{ __('12-15 years') }}</option>
                            <option value="15-20 years" {{ $data->data_retention === "15-20 years" ? "selected" : "" }}>{{ __('15-20 years') }}</option>
                            <option value="Over 20 years" {{ $data->data_retention === "Over 20 years" ? "selected" : "" }}>{{ __('Over 20 years') }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">{{ __('Number of Users') }}</label>
                        <input type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '')" id="no_users" name="no_users" class="form-control" value="{{ $data->no_of_user }}">
                    </div>
                    <div class="form-group">
                        <label for="">{{ __('List of Data Type') }}</label>
                        <input type="text" id="data_type" name="data_type" class="form-control" value="{{ $data->list_data_type }}">
                    </div>
                    <div class="form-group">
                        <label for="">{{ __('Notes') }}</label>
                        <input type="text" id="notes" name="notes" class="form-control" value="{{ $data->notes }}">
                    </div>
                    <div class="form-group">
                        <label for="">{{ __('Description') }}</label>
                        <textarea id="description" name="description" class="form-control">{{ $data->description }}</textarea>
                    </div>

                    <input type="hidden" id="latituedez" name="latz" class="form-control">
                    <input type="hidden" id="langutitudez" name="lngz" class="form-control">
                    <input type="hidden" id="tier_matrix_up" name="tier_matrix" class="form-control">
                    <div class="update_btn text-right">
                        <span id="tier_value"></span>
                        <a href="{{ url('assets') }}" class="buton" style="background:grey;border-color:grey;">{{__('Cancel')}}</a>
                        <input class="buton" type="submit" name="submit" value="{{ __('Update') }}">
                    </div>
                </form>
            </div>
        </div>
    @else
        <?php if ($user_type == 'admin'): ?>
        <div class="app-title">
            <ul class="app-breadcrumb breadcrumb">
                <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i>
                </li>
                <li class="breadcrumb-item"><a href="{{ route('asset_list') }}">{{ __('Assets') }}</a>
                </li>
            </ul>
        </div>
        <?php endif;?>
        @section('page_title')
            {{ __('ASSETS LIST') }}
        @endsection
        <section class="assets_list">
                @if (Session::has('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>{{ Session::get('success') }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
            <div class="row mb-2 align-items-center">
                        
                        <div class="col">
                            <button type="button" data-toggle="modal" data-target="#myModal"
                                class="buton mx-1" style="border: 1px solid #71BA4F; background: #71BA4F;">{{ __('ADD ASSET') }}</button>
                        </div>

                        <div class="col text-right mt-2 response">
                            <a class="buton mx-1" href="{{ route('export-asset', Auth::user()->client_id) }}">{{ __('EXPORT') }}</a>
                            <a class="buton mx-1" href="{{ url('import-asset') }}">{{ __('IMPORT_ASSETS') }}</a>
                        </div>
            </div>
            <div class="row">
                <div class="col-12">
                <!-- <div class="table_filter_section">
                    <div class="select_tbl_filter">
                        @if (Session::has('success'))
                            <div class="alert alert-success alert-dismissible fade show mx-5" role="alert">
                                <strong>{{ Session::get('success') }}</strong>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        <div class="add_more_tbl">
                            <button type="button" data-toggle="modal" data-target="#myModal"
                                class="btn rounded_button">{{ __('ADD MORE') }}</button>
                        </div>

                        <div class="add_more_tbl">
                            <a href="{{ route('export-asset', Auth::user()->client_id) }}"><button type="button"
                                    class="btn rounded_button">{{ __('EXPORT') }}</button></a>&nbsp;&nbsp;
                            <a href="{{ url('import-asset') }}"><button type="button"
                                    class="btn rounded_button">{{ __('IMPORT_ASSETS') }}</button></a>
                        </div>



                    </div>
                </div> -->
                <div class="card">
                    <div class="table_breadcrumb">
                        <h3>{{ __('ASSETS') }}</h3>
                    </div>
                    <div class="card-table">
                        <table class="table table-striped text-center" id="datatable">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('ASSET #') }}</th>
                                    <th scope="col">{{ __('ASSET TYPE') }}</th>
                                    <th scope="col">{{ __('ASSET NAME') }}</th>
                                    <th scope="col">{{ __('HOSTING TYPE') }}</th>
                                    <th scope="col">{{ __('HOSTING PROVIDER') }}</th>
                                    <th scope="col">{{ __('HOSTING COUNTRY') }}</th>
                                    <th scope="col">{{ __('CITY') }}</th>
                                    <th scope="col">{{ __('DATA CLASSIFICATION') }}</th>
                                    <th scope="col">{{ __('IMPACT') }}</th>
                                    <th scope="col">{{ __('TIER') }}</th>
                                    <th scope="col">{{ __('IT OWNER') }}</th>
                                    <th scope="col">{{ __('BUSINESS OWNER') }}</th>
                                    <th scope="col">{{ __('BUSINESS UNIT') }}</th>
                                    <th scope="col">{{ __('INTERNAL 3RD PARTY') }}</th>
                                    <th scope="col">{{ __('VOLUME OF SENSITIVE DATA') }}</th>

                                    <th scope="col">{{ __('ACTIONS') }}</th>
                                </tr>
                            </thead>


                            <tbody>
                                @foreach ($asset_list as $asset)
                                <tr>
                                    <td class='spocNames'>A-{{$asset->client_id}}-{{$asset->asset_number}}</td>
                                    <td class='spocNames'>{{ __($asset->asset_type) }}</td>
                                    <td class='spocNames'>{{ $asset->name }}</td>
                                    <td class='spocNames'>{{ __($asset->hosting_type) }}</td>
                                    <td class='spocNames'>{{ $asset->hosting_provider }}</td>
                                    <td class='spocNames'>{{ __($asset->country) }}</td>
                                    <td class='spocNames'>{{ $asset->city }}</td>
                                    
                                    <td class='spocNames'>
                                        @if(session('locale') == 'fr')
                                            {{ $asset->classification_name_fr }}
                                        @else
                                            {{ $asset->classification_name_en }}
                                        @endif
                                    </td>

                                    <td class='spocNames'>
                                        @if(session('locale') == 'fr')
                                            {{ $asset->impact_name_fr }}
                                        @else
                                            {{ $asset->impact_name_en }}
                                        @endif
                                    </td>

                                    <td class='spocNames'>
                                        @if(session('locale') == 'fr')
                                            {{ __($asset->tier) }}
                                        @else
                                            {{ __($asset->tier) }}
                                        @endif
                                    </td>

                                    <td class='spocNames'>{{ $asset->it_owner }}</td>
                                    <td class='spocNames'>{{ $asset->business_owner }}</td>
                                    <td class='spocNames'>{{ $asset->business_unit }}</td>
                                    <td class='spocNames'>{{ __($asset->internal_3rd_party) }}</td>
                                    <td class='spocNames'>{{ $asset->data_subject_volume }}</td>

                                    <td>
                                        <div class="action_icons">
                                            <a href="{{ url('asset_edit/' . $asset->id) }}"><img class="action-edit-right" src="{{url('assets-new/img/action-edit.png')}}"></a>
                                            <a href="javascript:void(0)" data-id="{{ $asset->id }}"
                                                class=" removePartner"><img class="action-edit-right" src="{{url('assets-new/img/action-delete.png')}}"></a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!-- <div class="table_footer">
                            <p>{{ __('Showing 1 to 9 of 9 entries') }}</p>
                            <div class="table_custom_pagination">
                                <p class="active_pagination">1</p>
                                <p>2</p>
                                <p>3</p>
                            </div>
                        </div> -->
                    </div>
                </div>
                </div>
            </div>
        </section>
        <section>
            <div class="row">
                <!-- <div class="col-sm-4 col-xs-12">
                            <div class="map-image card card-full-content">
                                <img src="assets/img/map.png">
                                <div class="map_canvas"></div>
                            </div>
                        </div> -->
                        <!-- New Map  -->
                        <div class="col-3 d-flex flex-column align-items-center justify-content-center">
                            <img src="{{url('assets-new/location.png')}}" alt="" style="width:80px;">
                            <h3 class="text-center">{{ __('Asset Location Map') }}</h3>
                        </div>
                        <div class="col-9">
                            <div class="mapouter">
                                <div class="gmap_canvas">
                                    <div id='map_canvas' style="position:relative; width:100%; height:500px;">
                                    </div>
                                </div>
                            </div>
                        </div>
            </div>
        </section>

        <div class="modal" id="myModal" style="padding: 5% 0% 5% 0%;">
            <div class="modal-dialog modal-xl">
                <div class="modal-content" style="border-radius: 30px;">

                    <!-- Modal Header -->
                    <div class="modal-header text-center bg-primary">
                        <h4 class="modal-title w-100 text-light">{{ __('Add Asset') }}</h4>
                        <button type="button" class="close text-light" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form action="{{ route('asset_add') }}" onsubmit="return get_location_assets();" method="POST" enctype="multipart/form-data" id="add_asset_loc">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sel1">{{ __('Asset Type') }}<span class="red">*</span></label>
                                        <select class="form-control" required id="sel1" name="asset_type">
                                            <option>{{ __('Server') }}</option>
                                            <option>{{ __('Application') }}</option>
                                            <option>{{ __('Database') }}</option>
                                            <option>{{ __('Physical Storage') }}</option>
                                            <option>{{ __('Website') }}</option>
                                            <option>{{ __('Other') }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                            <label>{{ __('Hosting Type') }}<span class="red">*</span></label>
                                            <select class="form-control" required id="hosting_type1" name='hosting_type'>
                                                <option value="Cloud">{{ __('Cloud') }}</option>
                                                <option value="On-Premise">{{ __('On-Premise') }}</option>
                                                <option value="Not Sure">{{ __('Not Sure') }}</option>
                                                <option value="Hybrid">{{ __('Hybrid') }}</option>

                                            </select>
                                    </div>
                                    <div class="form-group">
                                            <label for='country'>{{ __('Hosting Country') }}<span class="red">*</span></label>
                                            <select id='country_select' class="form-control" required name='country'>
                                                @foreach ($countries as $country)
                                                    <option value="{{ $country->country_name }}">{{ __($country->country_name) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('State') }}/{{ __('Province') }} </label>
                                        <input type="text" id="state1" name="state" class="form-control">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="">{{ __('Data Classification') }}<span class="red">*</span></label>
                                        <select name="data_classification" id="classification_name"
                                            class="form-control for_change_triger">
                                            @foreach ($dt_classification as $dc)
                                                <option value="{{ $dc->id }}"> @if(session('locale') == 'fr') {{ $dc->classification_name_fr }} @else {{ $dc->classification_name_en }} @endif</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="">{{ __('IT Owner') }}</label>
                                        <input type="text" id="it_owner" name="it_owner" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="">{{ __('Business Unit') }}</label>
                                        <input type="text" id="business_unit" name="business_unit" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="">{{ __('Volume of Sensitive Data') }}</label>
                                        <select class="form-control" required id="data_subject_volume" name='data_subject_volume'>
                                            <option value="0-100">{{ __('0-100') }}</option>
                                            <option value="100-500">{{ __('100-500') }}</option>
                                            <option value="500-1,000">{{ __('500-1,000') }}</option>
                                            <option value="1,000-10,000">{{ __('1,000-10,000') }}</option>
                                            <option value="10,000-100,000">{{ __('10,000-100,000') }}</option>
                                            <option value="100,000-500,000">{{ __('100,000-500,000') }}</option>
                                            <option value="500,000-1M">{{ __('500,000-1M') }}</option>
                                            <option value="1M - 5M">{{ __('1M - 5M') }}</option>
                                            <option value="5M - 10M">{{ __('5M - 10M') }}</option>
                                            <option value="10M - 50M">{{ __('10M - 50M') }}</option>
                                            <option value="50M - 100M">{{ __('50M - 100M') }}</option>
                                            <option value="100M+">{{ __('100M+') }}</option>

                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="">{{ __('Supplier') }}</label>
                                        <input type="text" id="supplier" name="supplier" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="">{{ __('Data Retention') }}</label>
                                        <select class="form-control" required id="data_retention" name='data_retention'>
                                            <option value="0-30 days">{{ __('0-30 days') }}</option>
                                            <option value="30-90 days">{{ __('30-90 days') }}</option>
                                            <option value="3-6 months">{{ __('3-6 months') }}</option>
                                            <option value="6-12 months">{{ __('6-12 months') }}</option>
                                            <option value="1-3 years">{{ __('1-3 years') }}</option>
                                            <option value="3-5 years">{{ __('3-5 years') }}</option>
                                            <option value="5-7 years">{{ __('5-7 years') }}</option>
                                            <option value="7-10 years">{{ __('7-10 years') }}</option>
                                            <option value="10-12 years">{{ __('10-12 years') }}</option>
                                            <option value="12-15 years">{{ __('12-15 years') }}</option>
                                            <option value="15-20 years">{{ __('15-20 years') }}</option>
                                            <option value="Over 20 years">{{ __('Over 20 years') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Assets Name') }}<span class="red">*</span></label>
                                        <input type="text" id="name1" name="name" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('Hosting Provider') }} </label>
                                        <input type="text" id="hosting_provider1" name="hosting_provider"
                                            class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('Hosting City') }} </label>
                                        <input type="text" id="city1" name="city" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="">{{ __('Impact') }}<span class="red">*</span></label>
                                        <select name="impact" id="impact_name" class="form-control for_change_triger">
                                            @foreach ($impact as $imp)
                                                <option value="{{ $imp->id }}"> @if(session('locale') == 'fr') {{ $imp->impact_name_fr }} @else {{ $imp->impact_name_en }} @endif</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <div class='input-field'>
                                            <label for='country'>{{ __('Category (Asset Tier)') }}<span class="red">*</span></label>
                                            <select id='tier_sub_field' class="form-control" required name='tier_sub_filed'>
                                                <option value="Crown Jewels"> {{__('Crown Jewels')}}</option>
                                                <option value="tier 1"> {{__('Tier 1')}}</option>
                                                <option value="tier 2"> {{__('Tier 2')}}</option>
                                                <option value="tier 3"> {{__('Tier 3')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="">{{ __('Business Owner') }}</label>
                                        <input type="text" id="business_owner" name="business_owner" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="">{{ __('Internal or 3rd Party') }}</label>
                                        <!-- <input type="text" id="internal_3rd_party" name="internal_3rd_party" class="form-control"> -->
                                        <select id='internal_3rd_party' class="form-control" required name='internal_3rd_party'>
                                                <option value="internal"> {{__('Internal')}}</option>
                                                <option value="3rd Party Provider"> {{__('3rd Party Provider')}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="">{{ __('Number of Users') }}</label>
                                        <input type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '')" id="no_users" name="no_users" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="">{{ __('List of Data Type in Application') }}</label>
                                        <input type="text" id="data_type" name="data_type" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="">{{ __('Notes') }}</label>
                                        <input type="text" id="notes" name="notes" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                        <label for="description">{{ __('Description') }}</label>
                                        <textarea id="description" name="description" class="form-control"></textarea>
                            </div>
                            
                            <input type="hidden" id="latituede" name="lat" class="form-control">
                            <input type="hidden" id="langutitude" name="lng" class="form-control">
                            <input type="hidden" id="tier_matrix" name="tier_matrix" class="form-control">
                            <div class="pt-4 d-flex justify-content-center">
                                <input class="buton mr-2 px-5"  type="submit" name="submit"
                                    value="{{ __('Add') }}">
                                <button type="button" class="btn btn-secondary px-5" style="width:140px;border-radius:35px;"
                                    data-dismiss="modal">{{ __('Close') }}</button>
                                
                            </div>
                        </form>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button> <input class="btn btn-primary" type="submit" name="submit" value="Add"> -->
                    </div>

                </div>
            </div>
        </div>


        <div id="edit_modal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <form action="{{ route('asset_update') }}" method="POST" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">{{ __('Assets Edit') }}</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>{{ __('Asset Name') }}</label>
                                <input type="text" class="form-control" name="name" id="get_name">
                            </div>
                        </div>
                        <input type="hidden" id="first_name" name="first_name">
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default"
                                data-dismiss="modal">{{ __('Close') }}</button>
                            <input type="submit" class="btn btn-primary" value="Update">
                        </div>
                    </div>
                </form>

            </div>
        </div>

        <script type="text/javascript" src="//www.gstatic.com/charts/loader.js"></script>
<script src="https://knockoutjs.com/downloads/knockout-2.2.1.js"></script>
<script src="http://maps.google.com/maps/api/js?sensor=false&.js"></script>
<script src="https://rawgit.com/kangax/fabric.js/master/dist/fabric.js"></script>
<script src="https://knockoutjs.com/downloads/jquery.tmpl.min.js"></script>





<input type="hidden" id="lat_value" value="<?php echo htmlentities(json_encode($lat_value)); ?>">
<input type="hidden" id="lat_detail" value="<?php echo htmlentities(json_encode($lat_detail)); ?>">

<script>
var lat_value = [];
var lat_detail = [];
jQuery(function($) {

    lat_value = JSON.parse(document.getElementById("lat_value").value);
    lat_detail = JSON.parse(document.getElementById("lat_detail").value);
    console.log({
        "lat_value": lat_value
    });


    // Asynchronously Load the map API 
    var script = document.createElement('script');
    script.src =
        "//maps.googleapis.com/maps/api/js?key=AIzaSyDaCml5EZAy3vVRySTNP7_GophMR8Niqmg&callback=initialize&libraries=&v=beta&map_ids=66b6b123dade7a4d";
    document.body.appendChild(script);
});

function initialize() {


    //above lines were put for var map, for api key


    var bounds = new google.maps.LatLngBounds();
    var mapOptions = {
        mapTypeId: 'roadmap'

    };



    map = new google.maps.Map(document.getElementById("map_canvas"), {
        mapId: "66b6b123dade7a4d",

    });








    var markers = lat_value;
    var html = '';
    var windowArray = [];

    var ct = "";

    var windowArray = [];

    for (var r = 0; r < lat_detail.length; r++) {
        ct = "";

        if (lat_detail[r][1] != null) {
            ct += '<p class="info_content"><strong>City :</strong>  ' + lat_detail[r][1] + '</p>';
        }


        if (lat_detail[r][2] != null) {
            ct += '<p class="info_content"><strong>State :</strong> ' + lat_detail[r][2] + '</p></div>';
        }




        // var html = [''. $string];

        var html = ['<div class="info_content"><p> <strong>Country :</strong> ' + lat_detail[r][0] + '</p>' +
            '<p><strong>Asset Name :</strong> ' + lat_detail[r][3] + '</p>' +
            '<p class="info_content"><strong>Hosting provider  :</strong> ' + lat_detail[r][4] + '</p>' +
            '<p class="info_content"><strong>Asset type :</strong> ' + lat_detail[r][5] + '</p>' + ct
        ];
        // html+=string;

        windowArray.push(html);

    }

    console.log(windowArray);



    for (var r = 0; r < markers.length; r++) {

        bounds = new google.maps.LatLngBounds();



        var position = new google.maps.LatLng(markers[r][1], markers[r][2]);
        bounds.extend(position);
        marker = new google.maps.Marker({
            position: position,
            map: map,
            title: markers[r][0]
        });




        var infoWindow = new google.maps.InfoWindow(),
            marker, r;

        google.maps.event.addListener(marker, 'click', (function(marker, r) {
            return function() {
                infoWindow.setContent(windowArray[r][0]);
                infoWindow.open(map, marker);
            }
        })(marker, r));

        console.log(bounds);

        map.fitBounds(bounds);

    }



    var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
        this.setZoom(1.7);
        // this.setTilt('africa');
        google.maps.event.removeListener(boundsListener);
    });


}
</script>


        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>

        <script type="text/javascript">
            //// Add Asset Matrix
            $(".for_change_triger").on("change", function() {
                $.ajax({
                    url: "{{ url('assets') }}",
                    method: "post",
                    data: {
                        imp: $("#impact_name").val(),
                        classification_id: $("#classification_name").val(),
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        $("#tier_sub_field").val(data[0].tier_value);
                        $("#tier_matrix").val(data[0].tier_value);
                    }
                });
            });
        </script>

        <script type="text/javascript">
            function update_asset(value) {
                document.getElementById('get_name').value = value;
                document.getElementById('first_name').value = value;
            }

            $("body").on("click", ".removePartner", function() {
                var task_id = $(this).attr("data-id");
                var form_data = {
                    id: task_id
                };
                swal({
                        title: "{!! __('Delete Asset') !!}",
                        text: "{!! __('This operation can not be reversed') !!}",
                        type: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#F79426',
                        cancelButtonColor: '#d33',
                        confirmButtonText: "{!! __('Yes') !!}",
                        showLoaderOnConfirm: true
                    },
                    function() {
                        $.ajax({
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: '<?php echo url('delete_asset'); ?>',
                            data: form_data,
                            success: function(msg) {
                                swal("@lang('users.success_delete')", '', 'success')
                                setTimeout(function() {
                                    location.reload();
                                }, 2500);
                            }
                        });
                    });

            });

            $(document).ready(() => {

                $('#assets-table').DataTable();

                <?php if (Auth::user()->role == '1'): ?>

                <?php endif;?>


            })
        </script>
        <script type="text/javascript">
            function get_location_assets() {

                var lng;
                get_location(lng);

                var hosting_provider = document.getElementById('hosting_provider1').value;

                return false;

            }

            function get_location(lng) {
                window.locationData = [];
                var country_select = document.getElementById('country_select').value;
                var city1 = document.getElementById('city1').value;
                //alert("ok")
                $.ajax({
                    url: "https://maps.googleapis.com/maps/api/geocode/json?address=" + country_select + "+" + city1 +
                        "&key=AIzaSyDaCml5EZAy3vVRySTNP7_GophMR8Niqmg",
                    method: "GET",
                    success: function(response) {
                        window.locationData = response;
                        var lat = locationData.results[0].geometry.location.lat;
                        var lng = locationData.results[0].geometry.location.lng;
                        document.getElementById("latituede").value = lat;
                        document.getElementById("langutitude").value = lng;

                        document.getElementById("add_asset_loc").removeAttribute("onsubmit");

                        $.ajax({
                            url: document.getElementById("add_asset_loc").getAttribute("action"),
                            method: "POST",
                            data: {
                                "asset_type": document.getElementById("add_asset_loc").asset_type.value,
                                "name": document.getElementById("add_asset_loc").name.value,
                                "hosting_type": document.getElementById("add_asset_loc").hosting_type.value,
                                "hosting_provider": document.getElementById("add_asset_loc").hosting_provider.value,
                                "country": document.getElementById("add_asset_loc").country.value,
                                "business_unit": document.getElementById("add_asset_loc").business_unit.value,
                                "city": document.getElementById("add_asset_loc").city.value,
                                "state": document.getElementById("add_asset_loc").state.value,
                                "impact": document.getElementById("add_asset_loc").impact.value,
                                "data_classification": document.getElementById("add_asset_loc").data_classification.value,
                                "tier_sub_filed": document.getElementById("add_asset_loc").tier_sub_filed.value,
                                "it_owner": document.getElementById("add_asset_loc").it_owner.value,
                                "business_owner": document.getElementById("add_asset_loc").business_owner.value,
                                "internal_3rd_party": document.getElementById("add_asset_loc").internal_3rd_party.value,
                                "data_subject_volume": document.getElementById("add_asset_loc").data_subject_volume.value,
                                "supplier": document.getElementById("add_asset_loc").supplier.value,
                                "data_retention": document.getElementById("add_asset_loc").data_retention.value,
                                "no_users": document.getElementById("add_asset_loc").no_users.value,
                                "data_type": document.getElementById("add_asset_loc").data_type.value,
                                "notes": document.getElementById("add_asset_loc").notes.value,
                                "description": document.getElementById("add_asset_loc").description.value,

                                "lat": document.getElementById("add_asset_loc").lat.value,
                                "lng": document.getElementById("add_asset_loc").lng.value,

                                "_token": document.getElementById("add_asset_loc")._token.value
                            },
                            success: function(msg) {
                                console.log(msg);
                                if (msg.status == 'success') {
                                    swal("{!! __('New Asset Added Successfully!') !!}", 'success')
                                } else {
                                    swal("{!! __('Asset already exists') !!}", 'error')
                                }
                                setTimeout(function() {
                                    window.location.replace("assets");
                                }, 2500);
                            }

                        });

                        return (lng);

                    }
                })
            }
        </script>
    @endif
    <script type="text/javascript">
        //// Edit Asset Matrix
        $(".for_change").on("change", function() {

            var as_id = $("#as_id_up").val();
            $.ajax({
                url: "{{ url('asset_edit/as_id') }}",
                method: "post",
                data: {
                    imp: $("#impact_name_up").val(),
                    dc_val: $("#classification_name_up").val(),
                    "_token": "{{ csrf_token() }}"
                },
                success: function(data) {
                    $("#tier_matrix_up").val(data[0].tier_value);
                    $("#tier_sub_field_up").val(data[0].tier_value);
                    //  console.log(data);

                }
            });
        });
        

        function get_location_assetsz() {
            var lng;
            get_locationz(lng);

            return false;

        }

        function get_locationz(lng) {
            // alert('well');
            window.locationData = [];
            var country_selectz = document.getElementById('country_selectz').value;
            var cityz = document.getElementById('citiz').value;

            $.ajax({
                url: "https://maps.googleapis.com/maps/api/geocode/json?address=" + country_selectz + "+" + cityz +
                    "&key=AIzaSyDaCml5EZAy3vVRySTNP7_GophMR8Niqmg",
                method: "GET",
                success: function(response) {
                    window.locationData = response;
                    var lat = locationData.results[0].geometry.location.lat;
                    var lng = locationData.results[0].geometry.location.lng;
                    document.getElementById("latituedez").value = lat;
                    document.getElementById("langutitudez").value = lng;

                    document.getElementById("update_asset_locz").removeAttribute("onsubmit");
                    console.log("Asset Ajax Call");

                    $.ajax({

                        url: document.getElementById("update_asset_locz").getAttribute("action"),
                        method: "POST",
                        data: {
                            "id": document.getElementById("update_asset_locz").id.value,
                            "asset_type": document.getElementById("update_asset_locz").asset_typez.value,
                            "name": document.getElementById("update_asset_locz").namez.value,
                            "hosting_type": document.getElementById("update_asset_locz").hosting_typez.value,
                            "hosting_provider": document.getElementById("update_asset_locz").hosting_providerz.value,
                            "country": document.getElementById("update_asset_locz").countryz.value,
                            "city": document.getElementById("update_asset_locz").cityzz.value,
                            "state": document.getElementById("update_asset_locz").statez.value,
                            "impact": document.getElementById("update_asset_locz").impact.value,
                            "data_classification": document.getElementById("update_asset_locz").data_classification.value,
                            "tier_sub_filed": document.getElementById("update_asset_locz").tier_sub_filed.value,
                            "business_unit": document.getElementById("update_asset_locz").business_unit.value,
                            "it_owner": document.getElementById("update_asset_locz").it_owner.value,
                            "business_owner": document.getElementById("update_asset_locz").business_owner.value,
                            "internal_3rd_party": document.getElementById("update_asset_locz").internal_3rd_party.value,
                            "data_subject_volume": document.getElementById("update_asset_locz").data_subject_volume.value,
                            "supplier": document.getElementById("update_asset_locz").supplier.value,
                            "data_retention": document.getElementById("update_asset_locz").data_retention.value,
                            "no_users": document.getElementById("update_asset_locz").no_users.value,
                            "data_type": document.getElementById("update_asset_locz").data_type.value,
                            "notes": document.getElementById("update_asset_locz").notes.value,
                            "description": document.getElementById("update_asset_locz").description.value,

                            "lat": document.getElementById("update_asset_locz").latz.value,
                            "lng": document.getElementById("update_asset_locz").lngz.value,
                            "_token": document.getElementById("update_asset_locz")._token.value
                        },
                        
                        success: function(msg) {
                            swal("{!! __('Update Successfully!') !!}")
                            setTimeout(function() {
                                window.location.replace("/assets");
                            }, 2500);
                        }
                    });

                }
            })
        }
    </script>

    <script>
        $(document).ready(function() {
            $('#datatable').DataTable();
        });
    </script>

@endsection