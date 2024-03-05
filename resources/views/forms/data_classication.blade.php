@extends('admin.client.client_app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
            @section('page_title')
                {{ __('DATA CLASSIFICATION') }}
            @endsection
            <div class="card">
                <div class="card-table">
                    <table class="table fixed_header manage-assessments-table" id="datatable">
                        <thead>
                            <tr>
                                <th style="vertical-align: middle;" scope="col" col-span="2">{{__('Data Classification')}} En</th>
                                <th style="vertical-align: middle;" scope="col" col-span="2">{{__('Data Classification')}} Fr</th>
                                <th style="vertical-align: middle;" scope="col">{{__('Action')}}</th>
                            </tr>
                        </thead>
                        <tbody class="btn-table">
                            <?php foreach ($data as $class): ?>
                            <tr>
                                <td>{{ $class->classification_name_en }}</td>
                                <td>{{ $class->classification_name_fr }}</td>

                                <td><a href="{{ url('front/edit-classification/' . $class->id) }}"> <img src="{{url('assets-new/img/action-edit.png')}}" alt=""> </a></td>


                            </tr>
                            <?php endforeach; ?>

                        </tbody>
                    </table>
                </div>
            </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        function update_asset_element(id) {
            var ase_id = $("#ade_id_" + id).val();
            $.ajax({
                method: "POST",
                url: "{{ url('update_asset_data_element') }}",
                data: {
                    ae_id: $("#ade_id_" + id).val(),
                    asset_name: $("#ade_name_" + id).val(),
                    sort: $("#order_sort_" + id).val(),
                    d_c_id: $("#dc_id_" + id).val(),
                    "_token": "{{ csrf_token() }}"
                },
                success: function(data) {
                    console.log(data);
                }
            })
        }
        $(document).ready(function() {
            $('#table_for_data_elements').DataTable({
                order: []
            });
        });
    </script>
@endsection
