@extends('teacher.layout')
@section('customCss')
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css">
@endsection

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Class List</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Classroom List</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
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

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif


                <div class="row">
                    @forelse ($classrooms as $classroom)
                        <div class="col-md-4 mb-4">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-folder-open text-warning"></i> {{ $classroom->class_name }}
                                    </h5>
                                    <p class="card-text mb-1">
                                        <strong>Teacher:</strong>
                                        {{ $classroom->teachers->first()->name ?? '-' }}
                                    </p>
                                    <p class="card-text">
                                        <strong>Total Student:</strong>
                                        {{ $classroom->students->count() }} stundent
                                    </p>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="{{ route('teacher.assignment.index', $classroom->id) }}"
                                            class="btn btn-sm btn-primary">Assignment</a>

                                        <!-- Edit Button to Toggle Collapse -->
                                        <button class="btn btn-sm btn-info" data-toggle="modal"
                                            data-target="#viewClassModal{{ $classroom->id }}">
                                            View
                                        </button>
                                    </div>


                                    <!-- View Class Modal -->
                                    <div class="modal fade" id="viewClassModal{{ $classroom->id }}" tabindex="-1"
                                        role="dialog" aria-labelledby="viewClassModalLabel{{ $classroom->id }}"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-info text-white">
                                                    <h5 class="modal-title" id="viewClassModalLabel{{ $classroom->id }}">
                                                        Class Details</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <h5><strong>Class Name:</strong> {{ $classroom->name }}</h5>
                                                    <hr>
                                                    <h6>List of Students:</h6>
                                                    <ul>
                                                        @forelse($classroom->students as $student)
                                                            <li>{{ $student->name }} ({{ $student->ic }})</li>
                                                        @empty
                                                            <li>No student in this class</li>
                                                        @endforelse
                                                    </ul>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            @empty
                                <div class="alert alert-info mt-3 d-flex align-items-center" role="alert">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <div>
                                        No class found
                                    </div>
                                </div>
                    @endforelse
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
                dom: '<"d-flex justify-content-between align-items-center mb-2"Bf>rtip',
                language: {
                    search: '',
                    searchPlaceholder: "🔍 Search class..."
                }
            });

            table.buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            $('#example1_filter input')
                .addClass('form-control form-control-sm rounded-pill ml-2')
                .css({
                    width: '200px',
                    display: 'inline-block'
                });

            table.buttons().container().appendTo('#datatable-buttons');
        });
    </script>
    <script>
        // Function to filter students based on search input
        function filterStudents(searchId, listId) {
            const searchTerm = document.getElementById(searchId).value.toLowerCase();
            const studentItems = document.querySelectorAll(#$ {
                listId
            }.student - btn);

            studentItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        }
    </script>
@endsection
