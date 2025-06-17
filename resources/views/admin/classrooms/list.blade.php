@extends('admin.layout')
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
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
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
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3">
                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                        data-target="#addClassroomModal">
                                        <i class="fas fa-plus"></i> Add Classroom
                                    </button>
                                </div>

                                <table id="classroomTable" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Class Name</th>
                                            <th>Teacher</th>
                                            <th>Student</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($classrooms as $classroom)
                                            <tr>
                                                <td>{{ $classroom->class_name }}</td>
                                                <td>
                                                    <ul>
                                                        @foreach ($classroom->teachers as $teacher)
                                                            <li>{{ $teacher->name }} ({{ $teacher->ic }})</li>
                                                        @endforeach
                                                    </ul>
                                                </td>
                                                <td>
                                                    <ul>
                                                        @foreach ($classroom->students as $student)
                                                            <li>{{ $student->name }} ({{ $student->ic }})</li>
                                                        @endforeach
                                                    </ul>
                                                </td>
                                                <td>{{ $classroom->status }}</td>
                                                <td>
                                                    <button class="btn btn-primary" data-toggle="modal"
                                                        data-target="#editClassModal{{ $classroom->id }}">
                                                        Edit
                                                    </button>
                                                </td>
                                            </tr>

                                            <!-- Edit Modal -->
                                            <div class="modal fade" id="editClassModal{{ $classroom->id }}" tabindex="-1"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <form
                                                            action="{{ route('admin.classrooms.update', $classroom->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Edit Class</h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <!-- Class Name -->
                                                                <div class="mb-3">
                                                                    <label>Class Name</label>
                                                                    <input type="text" name="class_name"
                                                                        class="form-control"
                                                                        value="{{ old('class_name', $classroom->class_name) }}"
                                                                        required>
                                                                    @error('class_name')
                                                                        <div class="alert alert-danger">{{ $message }}
                                                                        </div>
                                                                    @enderror
                                                                </div>

                                                                <!-- Status -->
                                                                <div class="mb-3">
                                                                    <label>Status</label>
                                                                    <select name="status"
                                                                        class="form-control status-select"
                                                                        id="statusSelect{{ $classroom->id }}">
                                                                        <option value="active"
                                                                            {{ $classroom->status == 'active' ? 'selected' : '' }}>
                                                                            Active</option>
                                                                        <option value="inactive"
                                                                            {{ $classroom->status == 'inactive' ? 'selected' : '' }}>
                                                                            Inactive</option>
                                                                    </select>
                                                                </div>

                                                                <!-- Teachers Search -->
                                                                <div class="mb-3">
                                                                    <label>Teachers</label>
                                                                    <input type="text"
                                                                        id="teacherSearch{{ $classroom->id }}"
                                                                        class="form-control mb-3"
                                                                        placeholder="Search Teacher..."
                                                                        onkeyup="filterTeachers('teacherSearch{{ $classroom->id }}', 'teacherList{{ $classroom->id }}')">

                                                                    <div class="border p-2"
                                                                        id="teacherList{{ $classroom->id }}"
                                                                        style="max-height: 200px; overflow-y: auto; overflow-x: hidden;">
                                                                        @foreach ($teachers as $teacher)
                                                                            <div class="form-check teacher-item">
                                                                                <input type="radio" name="teacher_ic"
                                                                                    value="{{ $teacher->ic }}"
                                                                                    id="teacher{{ $classroom->id }}{{ $teacher->ic }}"
                                                                                    class="form-check-input teacher-checkbox{{ $classroom->id }}"
                                                                                    {{ $classroom->teachers->pluck('ic')->contains($teacher->ic) ? 'checked' : '' }}>
                                                                                <label class="form-check-label"
                                                                                    for="teacher{{ $classroom->id }}{{ $teacher->ic }}">
                                                                                    {{ $teacher->name }}
                                                                                    ({{ $teacher->ic }})
                                                                                </label>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>

                                                                <!-- Students Search -->
                                                                <div class="mb-3">
                                                                    <label>Students</label>
                                                                    <input type="text"
                                                                        id="studentSearch{{ $classroom->id }}"
                                                                        class="form-control mb-3"
                                                                        placeholder="Search Student..."
                                                                        onkeyup="filterStudents('studentSearch{{ $classroom->id }}', 'studentList{{ $classroom->id }}')">

                                                                    <div class="border p-2"
                                                                        id="studentList{{ $classroom->id }}"
                                                                        style="max-height: 200px; overflow-y: auto;">

                                                                        @foreach ($classroom->students as $assignedStudent)
                                                                            <div class="form-check student-item">
                                                                                <input type="checkbox" name="student_ics[]"
                                                                                    value="{{ $assignedStudent->ic }}"
                                                                                    class="form-check-input student-checkbox{{ $classroom->id }}"
                                                                                    id="student{{ $classroom->id }}{{ $assignedStudent->ic }}"
                                                                                    checked>
                                                                                <label class="form-check-label"
                                                                                    for="student{{ $classroom->id }}{{ $assignedStudent->ic }}">
                                                                                    {{ $assignedStudent->name }}
                                                                                    ({{ $assignedStudent->ic }})
                                                                                </label>
                                                                            </div>
                                                                        @endforeach

                                                                        @foreach ($studentsWithoutClass as $student)
                                                                            <div class="form-check student-item">
                                                                                <input type="checkbox"
                                                                                    name="student_ics[]"
                                                                                    value="{{ $student->ic }}"
                                                                                    class="form-check-input student-checkbox{{ $classroom->id }}"
                                                                                    id="student{{ $classroom->id }}{{ $student->ic }}">
                                                                                <label class="form-check-label"
                                                                                    for="student{{ $classroom->id }}{{ $student->ic }}">
                                                                                    {{ $student->name }}
                                                                                    ({{ $student->ic }})
                                                                                </label>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                    <small class="text-muted">Students can only be assigned
                                                                        to one class.</small>
                                                                </div>

                                                            </div>

                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary">Update
                                                                    Class</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>



                                        @empty
                                            <tr>
                                                <td colspan="4">No classrooms found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Class Name</th>
                                            <th>Teacher</th>
                                            <th>Students</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add Classroom Modal -->
                <div class="modal fade" id="addClassroomModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form action="{{ route('admin.classrooms.store') }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label>Class Name</label>
                                        <input type="text" class="form-control" name="class_name"
                                            placeholder="4 Alamanda" required>
                                        @error('class_name')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>


                                    <!-- Teacher Selection -->
                                    <div class="mb-3">
                                        <label>Pilih Guru</label>
                                        <input type="text" class="form-control mb-2" id="addTeacherSearch"
                                            placeholder="Cari guru..."
                                            onkeyup="filterTeachers('addTeacherSearch', 'addTeacherList')">
                                        <div class="btn-group-toggle d-flex flex-wrap" data-toggle="buttons"
                                            id="addTeacherList">
                                            @foreach ($teachers as $teacher)
                                                <label
                                                    class="btn btn-outline-primary m-1 teacher-btn teacher-item {{ old('teacher_ic') == $teacher->ic ? 'active' : '' }}">
                                                    <input type="radio" name="teacher_ic" value="{{ $teacher->ic }}"
                                                        {{ old('teacher_ic') == $teacher->ic ? 'checked' : '' }}
                                                        class="teacher-checkbox">
                                                    <span>{{ $teacher->name }} ({{ $teacher->ic }})</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        <small class="text-muted">Only one teacher can be selected.</small>
                                    </div>

                                    <!-- Student Selection -->
                                    <div class="mb-3">
                                        <label>Pilih Pelajar</label>
                                        <input type="text" class="form-control mb-2" id="addStudentSearch"
                                            placeholder="Search students..."
                                            onkeyup="filterStudents('addStudentSearch', 'addStudentList')">
                                        <div class="btn-group-toggle d-flex flex-wrap" data-toggle="buttons"
                                            id="addStudentList">
                                            @foreach ($studentsWithoutClass as $student)
                                                <label class="btn btn-outline-success m-1 student-btn student-item">
                                                    <input type="checkbox" name="students[]" value="{{ $student->ic }}"
                                                        class="student-checkbox">
                                                    <span>{{ $student->name }} ({{ $student->ic }})</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>


                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-success">Save</button>
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Back</button>
                                    </div>
                            </form>
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
    <script src="Plugins/pdfmake/pdfmake.min.js"></script>
    <script src="plugins/pdfmake/vfs_fonts.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>


    <script src="dist/js/adminlte.min2167.js?v=3.2.0"></script>

    <script src="dist/js/demo.js"></script>
    <!-- JavaScript for filtering teachers and students  -->
    <script>
        function filterTeachers(inputId, listId) {
            var input = document.getElementById(inputId);
            var filter = input.value.toUpperCase();
            var teacherList = document.getElementById(listId);
            var teacherItems = teacherList.getElementsByClassName('teacher-item');

            for (var i = 0; i < teacherItems.length; i++) {
                var text = teacherItems[i].textContent || teacherItems[i].innerText;
                teacherItems[i].style.display = text.toUpperCase().indexOf(filter) > -1 ? "" : "none";
            }
        }

        function filterStudents(inputId, listId) {
            var input = document.getElementById(inputId);
            var filter = input.value.toUpperCase();
            var studentList = document.getElementById(listId);
            var studentItems = studentList.getElementsByClassName('student-item');

            for (var i = 0; i < studentItems.length; i++) {
                var text = studentItems[i].textContent || studentItems[i].innerText;
                studentItems[i].style.display = text.toUpperCase().indexOf(filter) > -1 ? "" : "none";
            }
        }
    </script>
    <!-- DataTables -->

    <script>
        $(function() {
            let table = $("#classroomTable").DataTable({
                dom: '<"row mb-3"<"col-md-6"B><"col-md-6 text-end"f>>' +
                    '<"row"<"col-sm-12"tr>>' +
                    '<"row mt-2"<"col-md-6"i><"col-md-6"p>>',

                language: {
                    search: '',
                    searchPlaceholder: "🔍 Search class..."
                }
            });

            // Styling search box
            $('#example1_filter input')
                .addClass('form-control form-control-sm rounded-pill')
                .css({
                    width: '200px',
                    display: 'inline-block'
                });

            // Move buttons to custom container (optional, can skip if using bootstrap layout)
            // table.buttons().container().appendTo('#datatable-buttons');
        });
    </script>


    <!-- SCRIPT: Enable/Disable guru & pelajar bila tukar status -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Ambil semua elemen status select dan checkbox untuk guru dan pelajar
            const statusSelects = document.querySelectorAll('.status-select');

            // Fungsi untuk toggle checkbox berdasarkan status
            function toggleCheckboxes(statusSelect) {
                const classroomId = statusSelect.getAttribute('id').replace('statusSelect',
                    ''); // Ambil ID kelas dari ID status select
                const teacherCheckboxes = document.querySelectorAll(`.teacher-checkbox${classroomId}`);
                const studentCheckboxes = document.querySelectorAll(`.student-checkbox${classroomId}`);

                const isActive = statusSelect.value === 'active'; // Semak jika status adalah active

                teacherCheckboxes.forEach(cb => {
                    cb.disabled = !isActive; // Sekat checkbox jika status 'inactive'
                    if (!isActive) cb.checked = false; // Nyahpilih checkbox jika status 'inactive'
                });

                studentCheckboxes.forEach(cb => {
                    cb.disabled = !isActive; // Sekat checkbox jika status 'inactive'
                    if (!isActive) cb.checked = false; // Nyahpilih checkbox jika status 'inactive'
                });
            }

            // Apply event listener untuk setiap status select
            statusSelects.forEach(statusSelect => {
                // Dengar perubahan status
                statusSelect.addEventListener('change', function() {
                    toggleCheckboxes(statusSelect);
                });

                // Set awal semasa buka modal
                toggleCheckboxes(statusSelect);
            });
        });
    </script>
@endsection
