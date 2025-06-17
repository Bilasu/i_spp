@extends('teacher.layout')
@section('customCss')
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        <style>.table-blue-header {
            background-color: #1e88e5;
            color: white;
        }
    </style>
    </style>
@endsection
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Assignemnt for class: {{ $classroom->class_name }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('teacher.classrooms.index') }}">Back</a></li>
                            <li class="breadcrumb-item active">Class Assignment</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
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
                <!-- Add New Assignment Button -->
                <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#createAssignmentModal">+ Add
                    Assignment</button>

                @if ($assignments->isEmpty())
                    <p>No assignment found.</p>
                @else
                    <div class="table-responsive mb-4">
                        <div class="card">
                            <div class="card-body">
                                <table style="background-color: #1e88e5; color: white;" class="table table-bordered">
                                    <thead class="table-blue-header">
                                        <tr>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>Due Date</th>
                                            <th>File</th>
                                            <th>Submitted Students</th>
                                            <th>Edit</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($assignments as $assignment)
                                            <tr>
                                                <td>{{ $assignment->title }}</td>
                                                <td>{{ $assignment->description }}</td>
                                                <td>{{ \Carbon\Carbon::parse($assignment->due_date)->format('d/m/Y') }}
                                                </td>
                                                <td>
                                                    @if ($assignment->file_path)
                                                        <a href="{{ asset('uploads/' . $assignment->file_path) }}"
                                                            download>
                                                            {{ $assignment->file_path }}
                                                        </a>
                                                    @else
                                                        None
                                                    @endif
                                                </td>
                                                @php
                                                    $totalStudents = $students->count();
                                                    $submittedCount = $assignment->submissions->count();
                                                    $notSubmittedCount = $totalStudents - $submittedCount;
                                                @endphp
                                                <td>
                                                    <a
                                                        href="{{ route('teacher.assignment.submissions', ['assignment' => $assignment->id]) }}">
                                                        <strong>{{ $submittedCount }} / {{ $totalStudents }}</strong><br>
                                                        <span style="font-size: 0.875rem; color: #333;">
                                                            Total submitted: {{ $submittedCount }}<br>
                                                            Not submitted: {{ $notSubmittedCount }}
                                                        </span>
                                                    </a>
                                                </td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm" data-toggle="modal"
                                                        data-target="#editAssignmentModal{{ $assignment->id }}">Edit</button>

                                                </td>
                                                <td>
                                                    <button class="btn btn-danger btn-sm" data-toggle="modal"
                                                        data-target="#deleteAssignmentModal{{ $assignment->id }}">Delete</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot style="background-color: #1e88e5; color: white;" class="table table-bordered">
                                        <tr>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>Due Date</th>
                                            <th>File</th>
                                            <th>Submitted Students</th>
                                            <th>Edit</th>
                                            <th>Delete</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </section>
    </div>

    <!-- Create Assignment Modal -->
    <div class="modal fade" id="createAssignmentModal" tabindex="-1" aria-labelledby="createAssignmentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('teacher.assignment.store', ['classroom' => $classroom->id]) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createAssignmentModalLabel">Create New Assignment</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" name="title" id="title" class="form-control" required
                                value="{{ old('title') }}">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="due_date" class="form-label">Due Date</label>
                            <input type="date" name="due_date" class="form-control"
                                min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" value="{{ old('due_date') }}">
                        </div>
                        <div class="mb-3">
                            <label for="file" class="form-label">Attachment (PDF/DOC/PPT/Excel - max 20MB)</label>
                            <input type="file" name="file" id="file" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
                        <button type="submit" class="btn btn-primary">Save Assignment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Assignment Modal -->
    @foreach ($assignments as $assignment)
        <div class="modal fade" id="editAssignmentModal{{ $assignment->id }}" tabindex="-1"
            aria-labelledby="editAssignmentModalLabel{{ $assignment->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form action="{{ route('teacher.assignment.update', ['id' => $assignment->id]) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="classroom_id" value="{{ $classroom->id }}">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editAssignmentModalLabel{{ $assignment->id }}">Update Assignment
                            </h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" name="title" id="title" class="form-control"
                                    value="{{ old('title', $assignment->title) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control">{{ old('description', $assignment->description) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="due_date" class="form-label">Due Date</label>
                                <input type="date" name="due_date" id="due_date" class="form-control"
                                    min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}"
                                    value="{{ old('due_date', $assignment->due_date ? \Carbon\Carbon::parse($assignment->due_date)->format('Y-m-d') : '') }}">
                            </div>

                            <div class="mb-3">
                                <label for="file" class="form-label">New Attachment (if you want to change)</label>
                                <input type="file" name="file" id="file" class="form-control">
                                @if ($assignment->file_path)
                                    <p class="mt-2">Existing file:
                                        <a href="{{ route('teacher.assignment.download', $assignment->file_path) }}"
                                            target="_blank">
                                            {{ basename($assignment->file_path) }}
                                        </a>
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Delete Assignment Modal -->
    @foreach ($assignments as $assignment)
        <div class="modal fade" id="deleteAssignmentModal{{ $assignment->id }}" tabindex="-1"
            aria-labelledby="deleteAssignmentModalLabel{{ $assignment->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteAssignmentModalLabel{{ $assignment->id }}">Delete Assignment
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this assignment?</p>
                        <p><strong>Assignment: {{ $assignment->title }}</strong></p>
                    </div>
                    <div class="modal-footer">
                        <form action="{{ route('teacher.assignment.delete', $assignment->id) }}" method="GET"
                            class="d-inline">
                            @csrf
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

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
                // responsive: true,
                // lengthChange: false,
                // autoWidth: false,
                // buttons: ["copy", "csv", "excel", "pdf", "print", "colvis"],


                dom: '<"d-flex justify-content-between align-items-center mb-2"Bf>rtip',
                language: {
                    search: '',
                    searchPlaceholder: "🔍 Search class..."
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
