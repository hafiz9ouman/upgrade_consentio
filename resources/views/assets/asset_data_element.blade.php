@extends('admin.client.client_app')
@section('content')
    @if(Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show mx-5" role="alert">
            <strong>{{ Session::get('success') }}</strong> 
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif 
    <section>
        <div class="row mb-3">
            <div class="col">
                <button data-toggle="modal" data-target="#exampleModal" class="buton mx-1" style="border: 1px solid #71BA4F; background: #71BA4F;">Add New</button>
            </div>
            <div class="col text-right">
                <a href="{{ route('export-elements-data', Auth::user()->client_id) }}"class="buton mx-1">Export</a>
                <a href="{{url('elements-data')}}" class="buton import mx-1">Import</a>
            </div>
            
        </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-table">
            @section('page_title')
                {{ __('DATA ELEMENTS') }}
            @endsection
                <table class="table fixed_header manage-assessments-table" id="datatable">
                    <thead class="text-center text-capitalize">
                        <tr>
                            <th style="vertical-align: middle;">#</th> 
                            <th style="vertical-align: middle;">data elements</th>
                            <th style="vertical-align: middle;">data element group</th>
                            <th style="vertical-align: middle;">data classification</th>
                            <th style="vertical-align: middle;">action</th>
                        </tr>
                    </thead>
                    <tbody class="btn-table">
                        @foreach($elements as $element )
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $element->name }}</td>
                            <td>{{ $element->section }}</td>
                            @if(session('locale') == 'fr')
                                <td>{{ $element->classification_name_fr }}</td>
                            @else
                                <td>{{ $element->classification_name_en }}</td>
                            @endif
                            <td>
                                <a href="{{url('edit-data-element/'.$element->id) }}" ><img src="{{url('assets-new/img/action-edit.png')}}" alt=""></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            </div>
        </div>
    </div>
    </section>

    <!-- Add New Data Elements Model -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" style="padding: 5% 0% 5% 0%;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">New Element</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form action="{{url('save_assets_data_elements')}}" method="post">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="#">Element Name</label>
                            <input type="text" name="name" class="form-control" placeholder="New Element" required>
                        </div>
                        <div class="form-group">
                            <label for="#">Data Element Group</label>
                            <select name="element_group" id="" class="form-control">
                                @foreach($section as $val)
                                    <option value="{{$val->id}}">{{$val->section_name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="#">Data Element Classification</label>
                            @if (session('locale') == 'fr')
                                <select name="element_classification" id="" class="form-control">
                                    @foreach($data_classifications as $val)
                                        <option value="{{$val->id}}">{{$val->classification_name_fr}}</option>
                                    @endforeach
                                </select>
                            @else
                                <select name="element_classification" id="" class="form-control">
                                    @foreach($data_classifications as $val)
                                        <option value="{{$val->id}}">{{$val->classification_name_en}}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="buton bg-dark" data-dismiss="modal">Close</button>
                    <button type="submit" class="buton">Submit</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#table_for_data_elements').DataTable({
                order:[]
            });
        });
    </script>
@endsection