@extends('student.layout')

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
                        <h1>Assignment Tittle : {{ $assignment->title }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('student.classrooms.index') }}">Back</a></li>
                            <li class="breadcrumb-item active">My Class</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                @if (session('success'))
                    <div class="alert alert-success">
                        {!! session('success') !!}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Assignment Details -->
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <!-- Card to display the assignment details and submission form -->
                            <div class="card p-4 shadow-sm">

                                <div style="display: flex; gap: 20px; flex-wrap: wrap;">

                                    {{-- Kotak Due Date --}}
                                    <div
                                        style=" font-size: 30px; flex: 1; min-width: 250px; background-color: rgba(255, 255, 255, 0.6); padding: 15px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
                                        <p><strong>Due Date:</strong><br>
                                            @if ($assignment->due_date)
                                                <span
                                                    style="color: #1e88e5;">{{ $assignment->due_date->format('d M Y H:i') }}</span>
                                            @else
                                                <em>Not set yet</em>
                                            @endif
                                        </p>
                                    </div>

                                    {{-- Kotak Assignment File --}}
                                    @if ($assignment->file_path)
                                        <div
                                            style="font-size: 20px; flex: 1; min-width: 250px; background-color: rgba(255, 255, 255, 0.6); padding: 15px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
                                            <p><strong>Assignment File:</strong><br>
                                            <div
                                                style="background-color: rgba(200, 200, 200, 0.3); padding: 15px; border-radius: 10px; display: inline-block; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: 0.3s;">
                                                <a href="{{ asset('storage/uploads/' . $assignment->file_path) }}"
                                                    target="_blank" download
                                                    style="text-decoration: none; color: #1e88e5; font-weight: bold; display: flex; align-items: center; gap: 10px;">
                                                    <i class="fas fa-download"></i> Download Assignment File
                                                </a>
                                            </div>
                                            </p>
                                        </div>
                                    @endif


                                </div>


                                <hr>

                                <div
                                    style="background-color: rgba(255, 255, 255, 0.8); padding: 20px; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">

                                    <h3 style="font-family: 'Montserrat', sans-serif; font-weight: 600; color: #333;">
                                        Submission Form</h3>

                                    <form
                                        action="{{ $studentSubmission ? route('student.submission.update', $assignment->id) : route('student.submission.submit', $assignment->id) }}"
                                        method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @if ($studentSubmission)
                                            {{-- @method('PUT') --}}
                                        @endif

                                        <!-- Comment Section -->
                                        <div class="mb-3">
                                            <label for="comment" class="form-label">Comment (optional):</label>
                                            <textarea name="comment" id="comment" class="form-control" rows="3">{{ old('comment', $studentSubmission->comment ?? '') }}</textarea>
                                            @error('comment')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- File Upload Section -->
                                        <div class="mb-3">
                                            <label for="file_path" class="form-label">Upload File :</label>
                                            <input type="file" name="file" id="file_path" class="form-control"
                                                {{ $studentSubmission ? '' : 'required' }}>
                                            @error('file')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Display Submitted File -->
                                        @if ($studentSubmission && $studentSubmission->file_path)
                                            <p><strong>Your Submitted File:</strong>
                                            <div
                                                style="background-color: rgba(200, 200, 200, 0.3); padding: 15px; border-radius: 10px; display: inline-block; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: 0.3s;">
                                                <a href="{{ asset('storage/uploads/' . $studentSubmission->file_path) }}"
                                                    target="_blank" download
                                                    style="text-decoration: none; color: #1e88e5; font-weight: bold; display: flex; align-items: center; gap: 10px;">
                                                    <i class="fas fa-download"></i> Download Submitted File
                                                </a>
                                            </div>
                                            </p>
                                        @endif


                                        <!-- Submit/Update Button -->
                                        <button type="submit" class="btn btn-primary">
                                            {{ $studentSubmission ? 'Update Submission' : 'Submit Assignment' }}
                                        </button>
                                    </form>

                                </div>

                            </div>
                        </div>
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
    <script src="plugins/pdfmake/pdfmake.min.js"></script>
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
                dom: '<"d-flex justify-content-between align-items-center mb-2"Bf>rtip',
                language: {
                    search: '',
                    searchPlaceholder: "🔍 Search assignment..."
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
@endsection
