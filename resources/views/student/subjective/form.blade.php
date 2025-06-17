@extends('student.layout')

@section('customCss')
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@endsection

@section('content')
    <div class="content-wrapper">
        <!-- Header -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2 align-items-center">
                    <div class="col-sm-6">
                        <h4><i class="bi bi-pencil-square me-2"></i>Jawab Soalan Subjektif</h4>
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

        <!-- Main Content -->
        <section class="content">
            <div class="container-fluid">

                {{-- Session Alerts --}}
                @if (session('success'))
                    <div class="alert alert-success d-flex align-items-center" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('success') }}
                    </div>
                @elseif(session('error'))
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Kategori --}}
                <div class="mb-4">
                    <p><strong>Kategori:</strong> {{ $category->name }}</p>
                </div>

                {{-- Unanswered Questions --}}
                @php
                    $unansweredQuestions = $category->subjectiveQuestions->filter(function ($q) use ($answers) {
                        return !isset($answers[$q->id]);
                    });
                @endphp

                @if ($unansweredQuestions->isEmpty())
                    <div class="alert alert-info d-flex align-items-center">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        Anda telah menjawab semua soalan dalam kategori ini.
                    </div>
                @else
                    @foreach ($unansweredQuestions as $index => $question)
                        <form method="POST" action="{{ route('student.subjective.submit', $question->id) }}"
                            class="mb-4">
                            @csrf
                            <input type="hidden" name="category_id" value="{{ $category->id }}">

                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-primary text-white">
                                    <strong>Soalan {{ $loop->iteration }}:</strong> {{ $question->question }}
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <textarea name="answer" class="form-control" rows="4" required placeholder="Tulis jawapan anda di sini..."></textarea>
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-success">
                                            <i class="bi bi-send-check-fill me-1"></i> Hantar Jawapan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @endforeach
                @endif

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
                    searchPlaceholder: "🔍 Search notetypes..."
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
