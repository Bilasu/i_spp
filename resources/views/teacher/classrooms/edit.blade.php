{{-- resources/views/classrooms/edit.blade.php --}}
@extends('teacher.layout')

@section('customCss')
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endsection

@section('content')
    <div class="content-wrapper" style="margin-left: 250px; padding: 20px;">
        <div class="container-fluid">
            <h1 class="mb-4">Edit Kelas</h1>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Ralat!</strong> Please check the following errors.
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('teacher.classrooms.update', $classroom->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Nama Kelas --}}
                {{-- <div class="mb-3">
                    <label for="class_name" class="form-label">Nama Kelas</label>
                    <input type="text" class="form-control" id="class_name" name="class_name"
                        value="{{ old('class_name', $classroom->class_name) }}" placeholder="Contoh: Tingkatan 4 Amanah"
                        required>
                </div> --}}

                {{-- Pilih Guru --}}
                {{-- <div class="mb-3">
                    <label class="form-label">Pilih Guru</label>

                    <input type="text" class="form-control mb-2" id="teacherSearch" placeholder="Cari nama guru...">

                    <div class="btn-group-toggle d-flex flex-wrap" data-toggle="buttons">
                        @foreach ($teachers as $teacher)
                            <label
                                class="btn btn-outline-primary m-1 teacher-btn {{ old('teacher_ic', $assignedTeachers[0] ?? '') == $teacher->ic ? 'active' : '' }}">
                                <input type="radio" name="teacher_ic" value="{{ $teacher->ic }}" autocomplete="off"
                                    {{ old('teacher_ic', $assignedTeachers[0] ?? '') == $teacher->ic ? 'checked' : '' }}>
                                {{ $teacher->name }} ({{ $teacher->ic }})
                            </label>
                        @endforeach
                    </div>
                    <small class="text-muted">Boleh pilih seorang sahaja.</small>
                </div> --}}

                {{-- Pilih Pelajar --}}
                <div class="mb-3">
                    <label class="form-label mt-4">Pilih Pelajar</label>

                    {{-- Search input --}}
                    <input type="text" id="studentSearch" class="form-control mb-3" placeholder="Cari nama pelajar...">

                    <div class="btn-group-toggle d-flex flex-wrap" data-toggle="buttons" id="studentList">
                        @foreach ($students as $student)
                            <label
                                class="btn btn-outline-success m-1 student-btn {{ in_array($student->ic, old('students', $assignedStudents ?? [])) ? 'active' : '' }}">
                                <input type="checkbox" name="students[]" value="{{ $student->ic }}" autocomplete="off"
                                    {{ in_array($student->ic, old('students', $assignedStudents ?? [])) ? 'checked' : '' }}>
                                {{ $student->name }} ({{ $student->ic }})
                            </label>
                        @endforeach
                    </div>
                    <small class="text-muted">Boleh pilih lebih daripada seorang pelajar.</small>
                </div>


                <button type="submit" class="btn btn-success">Kemaskini</button>
                <a href="{{ route('teacher.classrooms.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
@endsection

@section('customJs')
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>

    <script src="{{ asset('dist/js/adminlte.min2167.js?v=3.2.0') }}"></script>
    <script src="{{ asset('dist/js/demo.js') }}"></script>
    <script>
        document.getElementById('studentSearch').addEventListener('keyup', function() {
            const query = this.value.toLowerCase();
            const labels = document.querySelectorAll('#studentList .student-btn');

            labels.forEach(label => {
                const text = label.textContent.toLowerCase();
                label.style.display = text.includes(query) ? 'inline-block' : 'none';
            });
        });
    </script>
    <script>
        document.getElementById('teacherSearch').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const teacherLabels = document.querySelectorAll('#teacherList .teacher-btn');

            teacherLabels.forEach(label => {
                const text = label.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    label.style.display = '';
                } else {
                    label.style.display = 'none';
                }
            });
        });
    </script>
@endsection
