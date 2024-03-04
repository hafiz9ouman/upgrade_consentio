@extends('admin.client.client_app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
            @section('page_title')
                {{ __('DATA CLASSIFICATION') }}
            @endsection
                <div class="over_main_div">
                    <table class="table table-striped text-center" id="table_for_data_elements" >
                        <thead>
                            <tr>
                                <th scope="col" col-span="2">Data Classification Name English </th>
                                <th scope="col" col-span="2">Data Classification Name French </th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="btn-table">
                            <?php foreach ($data as $class): ?>
                            <tr>
                                <td>{{ $class->classification_name_en }}</td>
                                <td>{{ $class->classification_name_fr }}</td>

                                <td><a href="{{ url('front/edit-classification/' . $class->id) }}"> <i
                                            class="fas fa-pencil-alt"></i> Edit</a></td>


                            </tr>
                            <?php endforeach; ?>

                        </tbody>
                    </table>
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
            $('#table_for_data_elements').DataTable();
        });
    </script>
@endsection
