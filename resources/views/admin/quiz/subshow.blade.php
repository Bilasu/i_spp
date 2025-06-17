@extends('admin.layout')

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
                        <h1>Senarai Soalan Subjektif</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.quizcategory.read') }}">Back</a></li>
                            <li class="breadcrumb-item active">Senarai Soalan Subjektif</li>
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

                            {{-- Paparan Soalan --}}
                            <div class="card shadow-sm mb-4 border-0">
                                <div class="card-body">
                                    <h5 class="mb-3 text-primary fw-semibold">
                                        <i class="bi bi-question-circle-fill me-2"></i>Soalan:
                                    </h5>
                                    <p class="fs-5 fst-italic">{{ $question->question }}</p>
                                </div>
                            </div>

                            @if ($isOwner)
                                @forelse($question->answers as $answer)
                                    <div class="card mb-4 shadow-sm border-start border-primary border-4">
                                        <div class="card-body">
                                            <p><strong class="text-dark">👤 Pelajar:</strong> {{ $answer->student->name }}
                                            </p>
                                            <p><strong class="text-dark">✍️ Jawapan:</strong> {{ $answer->answer }}</p>

                                            @if ($answer->mark && $answer->comment)
                                                <hr>
                                                <p><strong class="text-success">✅ Markah:</strong> {{ $answer->mark }}</p>
                                                <p><strong class="text-secondary">💬 Komen:</strong> {{ $answer->comment }}
                                                </p>
                                            @else
                                                <form action="{{ route('admin.answer.mark', $answer->id) }}" method="POST"
                                                    class="mt-3">
                                                    @csrf
                                                    <div class="mb-2">
                                                        <label class="form-label">Markah:</label>
                                                        <input type="number" class="form-control" name="marks"
                                                            value="{{ $answer->mark ?? '' }}" max="100" min="0"
                                                            required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Komen:</label>
                                                        <input type="text" class="form-control" name="comments"
                                                            value="{{ $answer->comment ?? '' }}" required>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">💾 Simpan Markah &
                                                        Komen</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="alert alert-info shadow-sm">
                                        Tiada jawapan untuk soalan ini.
                                    </div>
                                @endforelse
                            @else
                                @forelse($question->answers as $answer)
                                    <div class="card mb-4 shadow-sm border-start border-secondary border-4">
                                        <div class="card-body">
                                            <p><strong>👤 Pelajar:</strong> {{ $answer->student->name }}</p>
                                            <p><strong>✍️ Jawapan:</strong> {{ $answer->answer }}</p>
                                            <p><strong>✅ Markah:</strong> {{ $answer->mark }}</p>
                                            <p><strong>💬 Komen:</strong> {{ $answer->comment }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="alert alert-info shadow-sm">
                                        Tiada jawapan untuk soalan ini.
                                    </div>
                                @endforelse
                            @endif






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
