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
                        <h1>Category of Quiz List</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Category of Quiz List</li>
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

                            {{-- @if (Session::has('error'))
                                <div class="alert alert-danger">
                                    {{ Session::get('error') }}
                                </div>
                            @endif --}}

                            <div class="card-header">
                                <h3 class="card-title">Category of Quiz</h3>
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
                                        data-target="#addCategoryQuizModal">
                                        <i class="fas fa-plus"></i> Add Category of Quiz
                                    </button>

                                    <div class="col-sm-6">
                                        <div id="customButtons" class="btn-group float-sm-right"></div>
                                    </div>
                                </div>
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>

                                            <th>Name of Category Types of Quiz</th>
                                            <th>Type of Quiz</th>
                                            <th>Status</th>
                                            <th>Created At</th>

                                            {{-- <th>Role</th>
                                            <th>Status</th> --}}

                                            <th>Edit</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($quiz_categories as $item)
                                            <tr>

                                                <td>
                                                    {{ $item->name }}
                                                </td>

                                                <td>
                                                    <a href="{{ route('admin.quiz.read', $item->id) }}"
                                                        class="btn btn-outline-primary btn-sm rounded-pill">
                                                        📄 Kertas 1
                                                    </a>
                                                    <a href="{{ route('admin.subjective.read', $item->id) }}"
                                                        class="btn btn-outline-success btn-sm rounded-pill">
                                                        ✍️ Kertas 2
                                                    </a>
                                                    <a href="{{ route('admin.essay.read', $item->id) }}"
                                                        class="btn btn-outline-warning btn-sm rounded-pill">
                                                        📝 Kertas 3
                                                    </a>
                                                </td>
                                                <td>
                                                    {{ $item->status }}
                                                </td>

                                                <td>
                                                    {{ $item->created_at->format('d-m-Y') }}
                                                </td>


                                                <td><a href="" class="btn btn-primary" data-toggle="modal"
                                                        data-target="#editCategoryQuizModal{{ $item->id }}">Edit</a>
                                                </td>






                                            </tr>

                                            <!-- Edit Modal -->
                                            <div class="modal fade" id="editCategoryQuizModal{{ $item->id }}"
                                                tabindex="-1"
                                                aria-labelledby="editCategoryQuizModalLabel{{ $item->id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('admin.quizcategory.update', $item->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            {{-- @method('PUT') --}}

                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="editCategoryQuizModalLabel{{ $item->id }}">
                                                                    Edit Category Types of Quiz</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>

                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label>Name of Category Types of Quiz</label>
                                                                    <input type="text" name="name"
                                                                        value="{{ $item->name }}" class="form-control"
                                                                        required>
                                                                </div>

                                                            </div>

                                                            <div class="form-group col-md-6">
                                                                <label>Select Status</label>
                                                                <select name="status" class="form-control">
                                                                    <option value="" disabled selected>Select
                                                                        Status</option>
                                                                    <option value="active"
                                                                        {{ $item->status == 'active' ? 'selected' : '' }}>
                                                                        active</option>
                                                                    <option value="inactive"
                                                                        {{ $item->status == 'inactive' ? 'selected' : '' }}>
                                                                        inactive</option>
                                                                </select>
                                                                {{-- @error('status')
                                                        <p class="text-danger">{{ $message }}</p>
                                                    @enderror --}}
                                                            </div>

                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary">Save
                                                                    Changes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- <!-- Delete Modal -->
                                            <div class="modal fade" id="deleteCategoryQuizModal{{ $item->id }}"
                                                tabindex="-1"
                                                aria-labelledby="deleteCategoryQuizModalLabel{{ $item->id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('quizcategory.delete', $item->id) }}"
                                                            method="GET">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="deleteCategoryQuizModalLabel{{ $item->id }}">
                                                                    Confirm Delete
                                                                </h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>

                                                            <div class="modal-body">
                                                                <p class="text-center">Are you sure you want to delete this
                                                                    category type of note?</p>
                                                                <p class="text-center"><strong>{{ $item->name }}</strong>
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
                                            </div> --}}
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>

                                            <th>Name of Category Types of Quiz</th>
                                            <th>Type of Quiz</th>
                                            <th>Status</th>
                                            <th>Created At</th>

                                            {{-- <th>Role</th>
                                            <th>Status</th> --}}

                                            <th>Edit</th>

                                        </tr>
                                    </tfoot>
                                </table>





                            </div>

                        </div>

                    </div>

                </div>

            </div>



            <!-- Add Modal -->
            <div class="modal fade" id="addCategoryQuizModal" tabindex="-1" aria-labelledby="addCategoryQuizModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('admin.quizcategory.store') }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="addCategoryQuizModalLabel">Add New Category Types of Quiz</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Names of Category Types of Quiz</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>


                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Add Category Types of Quiz</button>
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
                    searchPlaceholder: "🔍 Search quiz category..."
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
