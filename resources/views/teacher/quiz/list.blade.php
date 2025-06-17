@extends('teacher.layout')
@section('customCss')
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@endsection
@section('content')
    <div class="content-wrapper">

        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>List of Question</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('teacher.quizcategory.read') }}">Back</a></li>
                            <li class="breadcrumb-item active">Question List</li>
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
                                <h3 class="card-title">List of Question</h3>
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
                                        data-target="#addQuizModal">
                                        <i class="fas fa-plus"></i> Add Quiz
                                    </button>

                                    <!-- Review Results Button -->
                                    <a href="{{ route('teacher.quiz.results', ['quiz_id' => $category->id]) }}"
                                        class="btn btn-success ml-2">
                                        <i class="fas fa-eye"></i> Review Results
                                    </a>
                                    <div class="col-sm-6">
                                        <div id="customButtons" class="btn-group float-sm-right"></div>
                                    </div>
                                </div>
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>

                                            <th>Question</th>
                                            <th>Answer A</th>
                                            <th>Answer B</th>
                                            <th>Answer C</th>
                                            <th>Answer D</th>
                                            <th>True Answer</th>
                                            <th>Category</th>
                                            <th>Edit</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($questions as $item)
                                            <tr>

                                                <td>{{ $item->question }}</td>
                                                <td>{{ $item->a }}</td>
                                                <td>{{ $item->b }}</td>
                                                <td>{{ $item->c }}</td>
                                                <td>{{ $item->d }}</td>
                                                <td>{{ $item->ans }}</td>
                                                <td>{{ $item->category->name }}</td>



                                                <td><a href="" class="btn btn-primary" data-toggle="modal"
                                                        data-target="#editQuizModal{{ $item->id }}">Edit</a>
                                                </td>

                                                <td><a href="" class="btn btn-danger" data-toggle="modal"
                                                        data-target="#deleteQuizModal{{ $item->id }}">Delete</a>
                                                </td>




                                            </tr>





                                            <!-- Edit Notes Modal -->
                                            <div class="modal fade" id="editQuizModal{{ $item->id }}" tabindex="-1"
                                                aria-labelledby="editQuizModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('teacher.quiz.update', $item->id) }}"
                                                            method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            {{-- @method('PUT') --}}
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editQuizModalLabel">Edit
                                                                    Question</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>

                                                            <div class="modal-body">
                                                                <div class="row mb-2">
                                                                    <h5>Question</h5>
                                                                </div>

                                                                <div class="row mb-3">
                                                                    <input name="question" class="form-control"
                                                                        value="{{ $item->question }}"
                                                                        placeholder="Enter question text here">
                                                                </div>

                                                                <div class="row mb-2">
                                                                    <div class="col-md-6">
                                                                        <label> A: </label>
                                                                        <input name="opa" class="form-control"
                                                                            value="{{ $item->a }}"
                                                                            placeholder="Option A">
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label> B: </label>
                                                                        <input name="opb" class="form-control"
                                                                            value="{{ $item->b }}"
                                                                            placeholder="Option B">
                                                                    </div>
                                                                </div>

                                                                <div class="row mb-2">
                                                                    <div class="col-md-6">
                                                                        <label> C: </label>
                                                                        <input name="opc" class="form-control"
                                                                            value="{{ $item->c }}"
                                                                            placeholder="Option C">
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label> D: </label>
                                                                        <input name="opd" class="form-control"
                                                                            value="{{ $item->d }}"
                                                                            placeholder="Option D">
                                                                    </div>
                                                                </div>

                                                                <div class="row mb-2">
                                                                    <div class="col-md-6">
                                                                        <label>Answer:</label>
                                                                        <select name="ans" class="form-control">
                                                                            <option value="">-- Select Answer --
                                                                            </option>
                                                                            <option value="a"
                                                                                {{ $item->ans == 'a' ? 'selected' : '' }}>
                                                                                A</option>
                                                                            <option value="b"
                                                                                {{ $item->ans == 'b' ? 'selected' : '' }}>
                                                                                B</option>
                                                                            <option value="c"
                                                                                {{ $item->ans == 'c' ? 'selected' : '' }}>
                                                                                C</option>
                                                                            <option value="d"
                                                                                {{ $item->ans == 'd' ? 'selected' : '' }}>
                                                                                D</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary">Update
                                                                    Question</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>



                                            <!-- Delete Modal -->
                                            <div class="modal fade" id="deleteQuizModal{{ $item->id }}"
                                                tabindex="-1" aria-labelledby="deleteQUizModalLabel{{ $item->id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('teacher.quiz.delete', $item->id) }}"
                                                            method="GET">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="deleteQuizModalLabel{{ $item->id }}">
                                                                    Confirm Delete
                                                                </h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>

                                                            <div class="modal-body">
                                                                <p class="text-center">Are you sure you want to delete this
                                                                    question?</p>
                                                                <p class="text-center">
                                                                    <strong>{{ $item->question }}</strong>
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

                                            <th>Question</th>
                                            <th>Answer A</th>
                                            <th>Answer B</th>
                                            <th>Answer C</th>
                                            <th>Answer D</th>
                                            <th>True Answer</th>
                                            <th>Category</th>
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
            <div class="modal fade" id="addQuizModal" tabindex="-1" aria-labelledby="addQuizModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('teacher.quiz.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="quiz_category_id" value="{{ $category->id }}">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addQuizModalLabel">Add New Question</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <div class="row mb-2">
                                    <h5>Question</h5>
                                </div>

                                <div class="row mb-3">
                                    <input name="question" class="form-control" placeholder="Enter question text here"
                                        required>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <label> A: </label>
                                        <input name="opa" class="form-control" placeholder="Option A" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label> B: </label>
                                        <input name="opb" class="form-control" placeholder="Option B" required>
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <label> C: </label>
                                        <input name="opc" class="form-control" placeholder="Option C" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label> D: </label>
                                        <input name="opd" class="form-control" placeholder="Option D" required>
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <label>Answer:</label>
                                        <select name="ans" class="form-control" required>
                                            <option value="">-- Select Answer --</option>
                                            <option value="a">A</option>
                                            <option value="b">B</option>
                                            <option value="c">C</option>
                                            <option value="d">D</option>
                                        </select>
                                    </div>
                                </div>
                            </div>


                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Add Question</button>
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
                    searchPlaceholder: "🔍 Search quiz question..."
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
