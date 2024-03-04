@extends (($user_type == 'admin')?('admin.layouts.admin_app'):('admin.client.client_app'))
@section('content')
    @if (isset($data))
        <style type="text/css">
            .custom_Efdit_card {
                border-radius: 30px;
                margin-right: 19%;
                margin-left: 19%
            }

            @media screen and (max-width: 580px) {
                .custom_Efdit_card {
                    margin-right: 30px;
                    margin-left: 30px;
                }
            }
        </style>
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
                        <label>{{ __('Name') }}<span class="red">*</span></label>
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
                            class="form-control" required>
                    </div>
                    <div class="form-group">
                        <div class='input-field'>
                            <label for='country'>{{ __('Country') }}<span class="red">*</span></label>
                            <select id='country_selectz' class="form-control" required name='countryz'>
                                @if (isset($cont[0]->country_name))
                                    <option value="{{ $cont[0]->country_name }}">{{ $cont[0]->country_name }}</option>
                                @endif
                                @foreach ($countries as $country)
                                    <option>{{ $country->country_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>{{ __('City') }} </label>
                        <input type="text" id="citiz" name="cityzz" value="{{ $data->city }}"
                            class="form-control">
                    </div>
                    <div class="form-group">
                        <label>{{ __('State') }}/{{ __('Province') }} </label>
                        <input type="text" name="statez" value="{{ $data->state }}" class="form-control">
                    </div>
                    <div class="form-gourp">
                                <label for="">{{ __('Impact') }}</label>
                                <select name="impact" id="impact_name_up" class="form-control">
                                    @foreach ($impact as $imp)
                                        <option value="{{ $imp->id }}" {{ $imp->id == $data->impact_id ? "selected" : "" }}> {{ $imp->impact_name_en }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-gourp">
                                <label for="">{{ __('Data Classification') }}</label>
                                <select name="data_classification" id="classification_name_up"
                                    class="form-control">
                                    @foreach ($dt_classification->take(5) as $dc)
                                        <option value="{{ $dc->id }}"  {{ $dc->id == $data->data_classification_id ? "selected" : "" }}> {{ $dc->classification_name_en }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <div class='input-field'>
                                    <label for='country'>{{ __('Tier') }}</label>
                                    <select id='tier_sub_field_up' class="form-control" required name='tier_sub_filed'>
                                        <option value="crown jewels"> Crown Jewels</option>
                                        <option value="tier 1" {{ $data->tier == "tier 1" ? "selected" : "" }}> Tier 1</option>
                                        <option value="tier 2" {{ $data->tier == "tier 2" ? "selected" : "" }}> Tier 2</option>
                                        <option value="tier 3" {{ $data->tier == "tier 3" ? "selected" : "" }}> Tier 3</option>
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
                    <div class="form-gourp">
                        <label for="">{{ __('Internal or 3rd party') }}</label>
                        
                        <select id='internal_3rd_party' class="form-control" required name='internal_3rd_party'>
                            <option value="internal" {{ $data->internal_3rd_party === "internal" ? "selected" : "" }}>Internal</option>
                            <option value="3rd Party" {{ $data->internal_3rd_party === "3rd Party" ? "selected" : "" }}>3rd Party</option>
                        </select>
                    </div>
                    <div class="form-gourp">
                        <label for="">{{ __('Data Subject volume') }}</label>
                        <input type="text" id="data_subject_volume" name="data_subject_volume"
                            value="{{ $data->data_subject_volume }}" class="form-control">
                    </div>
                    <input type="hidden" id="latituedez" name="latz" class="form-control">
                    <input type="hidden" id="langutitudez" name="lngz" class="form-control">
                    <input type="hidden" id="tier_matrix_up" name="tier_matrix" class="form-control">
                    <div class="update_btn text-right">
                        <span id="tier_value"></span>
                        <input class="btn btn-primary mb-3" type="submit" name="submit" value="{{ __('Update') }}">
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
            <div class="main_custom_table">
                <div class="table_filter_section">
                    <div class="select_tbl_filter">
                        @if (Session::has('success'))
                            <div class="alert alert-success alert-dismissible fade show mx-5" role="alert">
                                <strong>{{ Session::get('success') }}</strong>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        <div class="main_filter_tbl">
                            <p>{{ __('Show') }}</p>
                            <select>
                                <option>10</option>
                                <option>20</option>
                                <option>30</option>
                            </select>
                            <p>{{ __('Entries') }}</p>
                        </div>
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
                </div>
                <div class="main_table_redisign">
                    <div class="table_breadcrumb">
                        <h3>{{ __('ASSETS') }}</h3>
                    </div>
                    <div class="over_main_div">
                        <table class="table table-striped text-center paginated" id="datatable">
                            <thead>
                                <tr>
                                    <th scope="col">NO.</th>
                                    <th scope="col">{{ __('ASSET #') }}</th>
                                    <th scope="col">{{ __('ASSET TYPE') }}</th>
                                    <th scope="col">{{ __('ASSET NAME') }}</th>
                                    <th scope="col">{{ __('HOSTING TYPE') }}</th>
                                    <th scope="col">{{ __('HOSTING PROVIDER') }}</th>
                                    <th scope="col">{{ __('COUNTRY') }}</th>
                                    <th scope="col">{{ __('CITY') }}</th>
                                    <th scope="col">{{ __('DATA CLASSIFICATION') }}</th>
                                    <th scope="col">{{ __('IMPACT') }}</th>
                                    <th scope="col">{{ __('TIER') }}</th>

                                    <th scope="col">{{ __('ACTIONS') }}</th>
                                </tr>
                            </thead>


                            <tbody>
                                @foreach ($asset_list as $asset)
                                <tr>
                                    <td scope="row" class="fix_width_td">{{ $loop->iteration }}</td>
                                    <td class='spocNames'>A-{{$asset->client_id}}-{{$asset->asset_number}}</td>
                                    <td class='spocNames'>{{ $asset->asset_type }}</td>
                                    <td class='spocNames'>{{ $asset->name }}</td>
                                    <td class='spocNames'>{{ $asset->hosting_type }}</td>
                                    <td class='spocNames'>{{ $asset->hosting_provider }}</td>
                                    <td class='spocNames'>{{ $asset->country }}</td>
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
                                            {{ $asset->tier }}
                                        @else
                                            {{ $asset->tier }}
                                        @endif
                                    </td>

                                    <td>
                                        <div class="action_icons">
                                            <a href="{{ url('asset_edit/' . $asset->id) }}"><i
                                                    class='bx bx-edit'></i></a>
                                            <a href="javascript:void(0)" data-id="{{ $asset->id }}"
                                                class=" removePartner"><i class='bx bxs-trash'></i></a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="table_footer">
                            <p>{{ __('Showing 1 to 9 of 9 entries') }}</p>
                            <div class="table_custom_pagination">
                                <p class="active_pagination">1</p>
                                <p>2</p>
                                <p>3</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="modal" id="myModal">
            <div class="modal-dialog">
                <div class="modal-content" style="border-radius: 30px;">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('Add Asset') }}</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form action="{{ route('asset_add') }}" onsubmit="return get_location_assets();" method="POST" enctype="multipart/form-data" id="add_asset_loc">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label for="sel1">{{ __('Asset type') }}<span class="red">*</span></label>
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
                                <label>{{ __('Name') }}<span class="red">*</span></label>
                                <input type="text" id="name1" name="name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <div class='input-field'>
                                    <label>{{ __('Hosting Type') }}<span class="red">*</span></label>
                                    <select class="form-control" required id="hosting_type1" name='hosting_type'>
                                        <option value="Cloud">{{ __('Cloud') }}</option>
                                        <option value="On-Premise">{{ __('On-Premise') }}</option>
                                        <option value="Not Sure">{{ __('Not Sure') }}</option>
                                        <option value="Hybrid">{{ __('Hybrid') }}</option>

                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>{{ __('Hosting Provider') }} </label>
                                <input type="text" id="hosting_provider1" name="hosting_provider"
                                    class="form-control" required>
                            </div>
                            <div class="form-group">
                                <div class='input-field'>
                                    <label for='country'>{{ __('Country') }}<span class="red">*</span></label>
                                    <select id='country_select' class="form-control" required name='country'>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->country_name }}">{{ $country->country_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>{{ __('City') }} </label>
                                <input type="text" id="city1" name="city" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>{{ __('State') }}/{{ __('Province') }} </label>
                                <input type="text" id="state1" name="state" class="form-control">
                            </div>
                            <div class="form-gourp">
                                <label for="">{{ __('Impact') }}</label>
                                <select name="impact" id="impact_name" class="form-control for_change_triger">
                                    @foreach ($impact as $imp)
                                        <option value="{{ $imp->id }}"> {{ $imp->impact_name_en }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-gourp">
                                <label for="">{{ __('Data Classification') }}</label>
                                <select name="data_classification" id="classification_name"
                                    class="form-control for_change_triger">
                                    @foreach ($dt_classification as $dc)
                                        <option value="{{ $dc->id }}"> {{ $dc->classification_name_en }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <div class='input-field'>
                                    <label for='country'>{{ __('Tier') }}</label>
                                    <select id='tier_sub_field' class="form-control" required name='tier_sub_filed'>
                                        <option value="crown jewels"> Crown Jewels</option>
                                        <option value="tier 1"> Tier 1</option>
                                        <option value="tier 2"> Tier 2</option>
                                        <option value="tier 3"> Tier 3</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-gourp">
                                <label for="">{{ __('IT Owner') }}</label>
                                <input type="text" id="it_owner" name="it_owner" class="form-control">
                            </div>
                            <div class="form-gourp">
                                <label for="">{{ __('Business Owner') }}</label>
                                <input type="text" id="business_owner" name="business_owner" class="form-control">
                            </div>

                            <div class="form-gourp pt-2">
                                <label for="">{{ __('Business Unit') }}</label>
                                <input type="text" id="business_unit" name="business_unit" class="form-control">
                            </div>
                            <div class="form-gourp">
                                <label for="">{{ __('Internal or 3rd party') }}</label>
                                <!-- <input type="text" id="internal_3rd_party" name="internal_3rd_party" class="form-control"> -->
                                <select id='internal_3rd_party' class="form-control" required name='internal_3rd_party'>
                                        <option value="internal"> Internal</option>
                                        <option value="3rd Party"> 3rd Party</option>
                                </select>
                            </div>
                            <div class="form-gourp">
                                <label for="">{{ __('Data Subject volume') }}</label>
                                <input type="text" id="data_subject_volume" name="data_subject_volume"
                                    class="form-control">
                            </div>
                            <input type="hidden" id="latituede" name="lat" class="form-control">
                            <input type="hidden" id="langutitude" name="lng" class="form-control">
                            <input type="hidden" id="tier_matrix" name="tier_matrix" class="form-control">
                            <div class="pt-4 d-flex justify-content-end">
                                <button type="button" class="btn btn-danger mr-2"
                                    data-dismiss="modal">{{ __('Close') }}</button>
                                <input class="btn btn-primary" type="submit" name="submit"
                                    value="{{ __('Add') }}">
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
        $("#update_asset_locz").on("change", function() {

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