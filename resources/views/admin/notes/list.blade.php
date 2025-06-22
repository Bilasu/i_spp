@extends('admin.layout')
@section('customCss')
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="lugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@section('content')
    <div class="content-wrapper">

        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Notes List</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Notes List</li>
                        </ol>
                    </div>
                </div>


            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">

                        <div class="card">
                            @if (Session::has('success'))
                                <div class="alert alert-success">
                                    {{ Session::get('success') }}
                                </div>
                            @endif


                            @if (session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif
                            <div class="card-header">
                                <h3 class="card-title">Notes List</h3>
                                {{-- <form action="">
                                    <div class="col-md-4">
                                        <select name="class_id" id="" class="form-control">
                                            <option value="" disabled selected> Select Class</option>
                                            @foreach ($classes as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach>
                                        </select>
                                    </div>
                                </form> --}}

                                {{-- <div class="form-group col-md-4">
                                    <label>Select Class</label>
                                    <select name="class_id" class="form-control">
                                        <option value="" disabled selected>Select Class</option>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}"
                                                {{ $class->id == request('class_id') ? 'selected' : '' }}>
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>


                                </div> --}}


                            </div>



                            <div class="card-body">

                                <div class="row mb-2">
                                    <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                        data-target="#addNotesModal">
                                        <i class="fas fa-plus"></i> Add Notes
                                    </button>

                                    <div class="col-sm-6">
                                        <div id="customButtons" class="btn-group float-sm-right"></div>
                                    </div>
                                </div>
                                <table id="example1" class="table table-bordered table-striped"
                                    style="table-layout: auto; width: 70%;">
                                    <thead>
                                        <tr>

                                            <th>Name</th>
                                            <th>Description</th>
                                            {{-- <th>File</th> --}}
                                            <th>Note Type</th>
                                            <th>Created At</th>
                                            {{-- <th>Download</th> --}}
                                            <th>Edit</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($notes as $item)
                                            <tr>
                                                <!-- File Download Link -->
                                                <td style="width: auto; white-space: normal; word-wrap: break-word;">
                                                    <a href="{{ asset('storage/uploads/' . $item->file) }}" target="_blank"
                                                        download title="Download {{ $item->file }}">
                                                        {{ $item->file }}
                                                    </a>
                                                </td>

                                                <!-- Description -->
                                                <td>{{ $item->desc }}</td>

                                                <!-- Note Type -->
                                                <td>{{ $item->Notetypes->name }}</td>

                                                <!-- Created At -->
                                                <td>{{ $item->created_at }}</td>

                                                <!-- Edit Button -->
                                                <td>
                                                    <a href="" class="btn btn-primary" data-toggle="modal"
                                                        data-target="#editNotesModal{{ $item->id }}">Edit</a>
                                                </td>

                                                <!-- Delete Button -->
                                                <td>
                                                    <a href="" class="btn btn-danger" data-toggle="modal"
                                                        data-target="#deleteNotesModal{{ $item->id }}">Delete</a>
                                                </td>
                                            </tr>


                                            <!-- View Modal -->
                                            {{-- <!-- View Modal -->
                                            <div class="modal fade" id="viewNotesModal{{ $item->id }}" tabindex="-1"
                                                aria-labelledby="viewNotesModalLabel{{ $item->id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-xl">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="viewNotesModalLabel{{ $item->id }}">
                                                                View Note - {{ $item->name }}
                                                            </h5>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>

                                                        <div class="modal-body">
                                                            @php
                                                                $ext = pathinfo($item->file, PATHINFO_EXTENSION);
                                                                $fileUrl = asset('uploads/' . $item->file);
                                                            @endphp

                                                            <div class="embed-responsive embed-responsive-16by9">
                                                                @if ($ext === 'pdf')
                                                                    <iframe class="embed-responsive-item"
                                                                        src="{{ $fileUrl }}" allowfullscreen></iframe>
                                                                @elseif (in_array($ext, ['xls', 'xlsx', 'ppt', 'pptx']))
                                                                    <iframe class="embed-responsive-item"
                                                                        src="https://view.officeapps.live.com/op/embed.aspx?src={{ urlencode($fileUrl) }}"
                                                                        allowfullscreen>
                                                                    </iframe>
                                                                @else
                                                                    <p class="text-danger">File format not supported for
                                                                        preview.</p>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <a href="{{ route('admin.notes.view', $item->file) }}"
                                                                target="_blank" class="btn btn-primary">
                                                                Open in New Tab
                                                            </a>
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> --}}


                                            <!-- Edit Notes Modal -->
                                            <div class="modal fade" id="editNotesModal{{ $item->id }}" tabindex="-1"
                                                aria-labelledby="editNotesModalLabel{{ $item->id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('admin.notes.update', $item->id) }}"
                                                            method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            {{-- @method('PUT') --}}

                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="editNotesModalLabel{{ $item->id }}">Edit Note
                                                                </h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>

                                                            <div class="modal-body">
                                                                {{-- <div class="form-group">
                                                                    <label>Name</label>
                                                                    <input type="text" name="name"
                                                                        class="form-control" value="{{ $item->name }}"
                                                                        required>
                                                                </div> --}}

                                                                <div class="form-group">
                                                                    <label>Select Note Type</label>
                                                                    <select name="notetypes_id" class="form-control"
                                                                        required>
                                                                        <option disabled>Select Note Type</option>
                                                                        @foreach ($notetypes as $notetype)
                                                                            <option value="{{ $notetype->id }}"
                                                                                {{ $item->notetypes_id == $notetype->id ? 'selected' : '' }}>
                                                                                {{ $notetype->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div class="form-group">
                                                                    <label>Notes Description</label>
                                                                    <input type="text" name="desc"
                                                                        class="form-control" value="{{ $item->desc }}"
                                                                        required>
                                                                </div>

                                                                <div class="form-group">
                                                                    <label for="formFileMultiple">Upload New File
                                                                        (Optional)</label>
                                                                    <input class="form-control" name="file"
                                                                        type="file" id="formFileMultiple"
                                                                        accept=".pdf, .xls, .xlsx">
                                                                    <small class="text-muted">Current File:
                                                                        {{ $item->file }}</small>
                                                                </div>
                                                            </div>

                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary">Update
                                                                    Note</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>


                                            <!-- Delete Modal -->
                                            <div class="modal fade" id="deleteNotesModal{{ $item->id }}" tabindex="-1"
                                                aria-labelledby="deleteNotesModalLabel{{ $item->id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('admin.notes.delete', $item->id) }}"
                                                            method="GET">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="deleteNotesModalLabel{{ $item->id }}">
                                                                    Confirm Delete
                                                                </h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>

                                                            <div class="modal-body">
                                                                <p class="text-center">Are you sure you want to delete this
                                                                    note type?</p>
                                                                <p class="text-center">
                                                                    <strong>{{ $item->file }}</strong>
                                                                </p>
                                                            </div>

                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-danger">Yes,
                                                                    Delete</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>

                                            <th>Name</th>
                                            <th>Description</th>
                                            {{-- <th>File</th> --}}
                                            <th>Note Type</th>
                                            {{-- <th>Download</th> --}}
                                            <th>View</th>
                                            <th>Edit</th>
                                            <th>Delete</th>
                                        </tr>
                                    </tfoot>
                                </table>





                            </div>

                        </div>

                    </div>

                </div>

            </div>



            <!-- Add Modal -->
            <div class="modal fade" id="addNotesModal" tabindex="-1" aria-labelledby="addNotesModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('admin.notes.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="addNotesModalLabel">Add New Notes</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                {{-- <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div> --}}

                                <div class="form-group">
                                    <label>Select Note Type</label>
                                    <select name="notetypes_id"
                                        class="form-control @error('notetypes_id') is-invalid @enderror" required>
                                        <option value="" disabled {{ old('notetypes_id') ? '' : 'selected' }}>Select
                                            Note Type</option>
                                        @foreach ($notetypes as $notetype)
                                            <option value="{{ $notetype->id }}"
                                                {{ old('notetypes_id') == $notetype->id ? 'selected' : '' }}>
                                                {{ $notetype->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('notetypes_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                                <div class="form-group">
                                    <label>Notes Description</label>
                                    <input type="text" name="desc" class="form-control"
                                        placeholder="Enter Notes Description" required>
                                </div>

                                <div class="form-group">
                                    <label for="formFileMultiple" class="form-label">Upload File</label>
                                    <input class="form-control" name="file" type="file" id="formFileMultiple"
                                        accept=".pdf, .xls, .xlsx, .ppt, .pptx" required>
                                    <small class="text-danger">Please rename your file before uploading.</small>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Add Notes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>



        </section>

    </div>
@endsection
@section('customJs')
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="plugins/jszip/jszip.min.js"></script>
    <script src="Plugins/pdfmake/pdfmake.min.js"></script>
    <script src="plugins/pdfmake/vfs_fonts.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

    <script src="dist/js/adminlte.min2167.js?v=3.2.0"></script>

    <script src="dist/js/demo.js"></script>

    <script>
        $(function() {
            let table = $("#example1").DataTable({
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                buttons: ["copy", "csv", "excel", "pdf", "print", "colvis"],


                dom: '<"d-flex justify-content-between align-items-center mb-3"<"dt-buttons"><"dt-search"f>>rtip',

                language: {
                    search: '',
                    searchPlaceholder: "🔍 Search notes..."
                }
            });



            table.buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

            // Add custom styling to the search box
            $('#example1_filter input')
                .addClass('form-control form-control-sm rounded-pill ml-2')
                .css({
                    width: '200px',
                    display: 'inline-block'
                });


            // Move buttons to custom container
            table.buttons().container().appendTo('#datatable-buttons');
        });
    </script>

@endsection
