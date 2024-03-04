@extends('admin.client.client_app')
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.3.2/css/buttons.dataTables.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js"></script>
    @if(Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show mx-5" role="alert">
            <strong>{{ Session::get('success') }}</strong> 
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif 
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
            @section('page_title')
                {{ __('DATA ELEMENTS') }}
            @endsection
                <table class="table-striped table-bordered data-table" id="table_for_data_elements">
                    <thead class="text-center text-capitalize">
                        <button data-toggle="modal" data-target="#exampleModal" class="btn btn-primary mx-1">Add New</button>
                        <a href="{{ route('export-elements-data', Auth::user()->client_id) }}"class="btn btn-primary mx-1">Export</a>
                        <a href="{{url('elements-data')}}" class="btn btn-primary import mx-1">Import</a>
                        <br><br>
                        <tr>
                            <th>#</th> 
                            <th>data elements</th>
                            <th>data element group</th>
                            <th>data classification</th>
                            <th>action</th>
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
                                <a href="{{url('edit-data-element/'.$element->id) }}" class="btn btn-primary text-light" > Edit</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add New Data Elements Model -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#table_for_data_elements').DataTable();
        });
    </script>
@endsection