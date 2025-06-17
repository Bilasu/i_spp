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
                        <h4><i class="bi bi-pencil-square me-2"></i> Jawapan Anda - {{ $category->name }}</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('student.quizcategory.read') }}">Back</a></li>
                            <li class="breadcrumb-item active">My Class</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <section class="content">
            <div class="container-fluid">

                <!-- Session Alerts -->
                @if (session('success'))
                    <div class="alert alert-success d-flex align-items-center">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    </div>
                @endif

                @if (session('errors'))
                    <div class="alert alert-danger d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('errors') }}
                    </div>
                @endif

                <!-- Calculate Progress -->
                @php
                    $answeredQuestions = $category->essayQuestions->filter(fn($q) => $q->answers->isNotEmpty());
                    $totalQuestions = $category->essayQuestions->count();
                    $answeredCount = $answeredQuestions->count();
                    $progress = $totalQuestions > 0 ? round(($answeredCount / $totalQuestions) * 100) : 0;
                @endphp

                <!-- Progress Bar -->
                <div class="mb-4">
                    <h5 class="mb-2">Kemajuan Jawapan: {{ $answeredCount }} / {{ $totalQuestions }}</h5>
                    <div class="progress">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progress }}%">
                            {{ $progress }}%
                        </div>
                    </div>
                </div>

                @if ($answeredQuestions->isEmpty())
                    <div class="alert alert-info d-flex align-items-center mt-4">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        Anda belum menjawab mana-mana soalan dalam kategori ini.
                    </div>
                @else
                    <!-- Kotak Besar -->
                    <div class="card shadow border-0 mb-5">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0"><i class="bi bi-journal-check me-2"></i> Senarai Jawapan Subjektif</h5>
                        </div>
                        <div class="card-body">
                            @foreach ($answeredQuestions as $index => $question)
                                @php
                                    $answer = $question->answers->first();
                                @endphp

                                <!-- Kotak Kecil Soalan -->
                                <div class="card mb-4 shadow-sm border">
                                    <div class="card-header bg-primary text-white">
                                        <strong>Soalan {{ $loop->iteration }}:</strong> {{ $question->question }}
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Jawapan Anda:</strong></p>
                                        <div class="p-3 bg-light border rounded mb-3">
                                            {{ $answer->answer ?? '-' }}
                                        </div>

                                        <p><strong>Markah:</strong>
                                            @if ($answer && $answer->mark !== null)
                                                <span class="badge bg-success">{{ $answer->mark }}/100</span>
                                            @else
                                                <span class="badge bg-secondary">Belum dinilai</span>
                                            @endif
                                        </p>

                                        <p><strong>Komen Cikgu:</strong></p>
                                        <div class="p-2 bg-white border rounded mb-2">
                                            {{ $answer->comment ?? 'Tiada komen' }}
                                        </div>

                                        <p class="text-muted mb-0">
                                            <i class="bi bi-clock-history me-1"></i>
                                            Dijawab pada:
                                            {{ \Carbon\Carbon::parse($answer->created_at)->translatedFormat('d M Y, h:i A') }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
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
