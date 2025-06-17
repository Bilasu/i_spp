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
                        <h1>Senarai Soalan Essay</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('teacher.quizcategory.read') }}">Back</a></li>
                            <li class="breadcrumb-item active">Senarai Soalan Essay</li>
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
                                <h3 class="card-title">Soalan Essay yang Tersedia</h3>

                            </div>

                            <div class="card-body">
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addQuestionModal">
                                    <i class="fas fa-plus"></i> Tambah Soalan
                                </button>
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Soalan</th>
                                            <th>Review Jawapan</th>
                                            <th>Edit</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($questions as $question)
                                            <tr>
                                                <td>{{ $question->question }}</td>
                                                <td>
                                                    {{-- @php
                                                        $teacher_ic = Auth::guard('teacher')->user()->ic;
                                                        $category_ic = $question->category->user_ic;
                                                    @endphp

                                                    {{-- Debugging output to check the values --}}
                                                    {{-- <p>teacher IC: {{ $teacher_ic }}</p>
                                                    <p>Category IC: {{ $category_ic }}</p> --}}

                                                    {{-- @if (Auth::guard('teacher')->check() && $category_ic === $teacher_ic) --}}
                                                    <a href="{{ route('teacher.quiz.essayshow', [$question->category->id, $question->id]) }}"
                                                        class="btn btn-primary btn-sm">
                                                        Review Answer
                                                    </a>
                                                    {{-- @else
                                                        <a href="{{ route('teacher.quiz.subshow', $question->category->id) }}"
                                                            class="btn btn-secondary btn-sm">
                                                            Lihat Jawapan
                                                        </a>
                                                    @endif --}}
                                                </td>



                                                <td>
                                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                                        data-target="#editQuestionModal{{ $question->id }}">
                                                        Edit
                                                    </button>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger" data-toggle="modal"
                                                        data-target="#deleteQuestionModal{{ $question->id }}">
                                                        Delete
                                                    </button>
                                                </td>
                                            </tr>

                                            <!-- Modal Edit Soalan -->
                                            <div class="modal fade" id="editQuestionModal{{ $question->id }}" tabindex="-1"
                                                aria-labelledby="editQuestionModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('teacher.essay.update', $question->id) }}"
                                                            method="POST">
                                                            @csrf

                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editQuestionModalLabel">Edit
                                                                    Soalan</h5>

                                                                <input type="hidden" name="quiz_category_id"
                                                                    value="{{ $question->quiz_category_id }}">
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="question">Soalan</label>
                                                                    <input type="text" name="question" id="question"
                                                                        class="form-control"
                                                                        value="{{ $question->question }}">
                                                                </div>
                                                                {{-- <div class="form-group">
                                                                    <label for="quiz_category_id">Kategori</label>
                                                                    <select name="quiz_category_id" id="quiz_category_id"
                                                                        class="form-control">
                                                                        @foreach ($categories as $category)
                                                                            <option value="{{ $category->id }}"
                                                                                {{ $category->id == $question->quiz_category_id ? 'selected' : '' }}>
                                                                                {{ $category->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div> --}}
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary">Update
                                                                    Soalan</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Modal Padam Soalan -->
                                            <div class="modal fade" id="deleteQuestionModal{{ $question->id }}"
                                                tabindex="-1" aria-labelledby="deleteQuestionModalLabel"
                                                aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('teacher.essay.delete', $question->id) }}"
                                                            method="GET">
                                                            @csrf
                                                            {{-- @method('DELETE') --}}
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="deleteQuestionModalLabel">Padam
                                                                    Soalan</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Adakah anda pasti mahu memadam soalan berikut?</p>
                                                                <p><strong>{{ $question->question }}</strong></p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-danger">Ya,
                                                                    Padam</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Tambah Soalan -->
            <div class="modal fade" id="addQuestionModal" tabindex="-1" aria-labelledby="addQuestionModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('teacher.essay.store') }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="addQuestionModalLabel">Tambah Soalan Baru</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="question">Soalan</label>
                                    <input type="hidden" name="quiz_category_id" value="{{ $quiz_category_id }}">
                                    <input type="text" name="question" id="question" class="form-control"
                                        placeholder="Masukkan soalan di sini" required>
                                </div>
                                {{-- <div class="form-group">
                                    <label for="quiz_category_id">Kategori</label>
                                    <select name="quiz_category_id" id="quiz_category_id" class="form-control" required>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div> --}}
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Tambah Soalan</button>
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
    <script src="plugins/pdfmake/pdfmake.min.js"></script>
    <script src="plugins/pdfmake/vfs_fonts.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

    <script src="dist/js/teacherlte.min2167.js?v=3.2.0"></script>

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
                    searchPlaceholder: "🔍 Cari soalan kuiz..."
                }
            });

            table.buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            $('#example1_filter input')
                .addClass('form-control form-control-sm rounded-pill ml-2')
                .css({
                    width: '200px',
                    display: 'inline-block'
                });
        });
    </script>
@endsection
