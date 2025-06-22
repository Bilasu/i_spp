@extends('admin.layout')
@section('customCss')
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="lugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@section('content')
    <div class="content-wrapper">

        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Student List</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Student List</li>
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

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif



                            <div class="card-header">
                                <h3 class="card-title">Student List</h3>
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
                                        data-target="#addStudentModal">
                                        <i class="fas fa-plus"></i> Add Student
                                    </button>

                                    <div class="col-sm-6">
                                        <div id="customButtons" class="btn-group float-sm-right"></div>
                                    </div>
                                </div>
                                <table id="example1" class="table table-bordered table-striped"
                                    style="table-layout: fixed; width: 100%;">
                                    <thead>
                                        <tr>

                                            <th>Name</th>
                                            <th>I/C Number</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Status</th>

                                            <th>Edit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $item)
                                            <tr>

                                                <td>{{ $item->name }}</td>
                                                <td>{{ $item->ic }}</td>
                                                <td>{{ $item->email }}</td>
                                                <td>{{ $item->role }} </td>

                                                <td>{{ $item->status }}</td>
                                                {{-- <td>{{ $item->mobno }}</td> --}}
                                                <td><a href="" class="btn btn-primary" data-toggle="modal"
                                                        data-target="#editStudentModal{{ $item->ic }}">Edit</a>
                                                </td>
                                                {{-- <td><a href="{{ route('class.delete', $item->id) }}"
                                                        onclick="return confirm('Are you sure?');"
                                                        class="btn btn-danger">Delete</a></td>
                                                </td> --}}

                                            </tr>

                                            <!-- Edit Modal -->
                                            <div class="modal fade" id="editStudentModal{{ $item->ic }}" tabindex="-1"
                                                aria-labelledby="editStudentModalLabel{{ $item->ic }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('student.update', $item->ic) }}"
                                                            method="POST">
                                                            @csrf
                                                            {{-- @method('PUT') --}}

                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="editStudentModalLabel{{ $item->ic }}">
                                                                    Edit Teacher</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>

                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label>Name</label>
                                                                    <input type="text" name="name"
                                                                        value="{{ $item->name }}" class="form-control"
                                                                        required>
                                                                </div>

                                                                <div class="form-group">
                                                                    <label>IC Number</label>
                                                                    <input type="text" name="ic"
                                                                        value="{{ $item->ic }}" class="form-control"
                                                                        required maxlength="12">
                                                                </div>

                                                                <div class="form-group">
                                                                    <label>Email Address</label>
                                                                    <input type="email" name="email"
                                                                        value="{{ $item->email }}" class="form-control"
                                                                        required placeholder="Enter your Gmail email"
                                                                        pattern="[a-zA-Z0-9._%+-]+@gmail\.com">
                                                                </div>

                                                                {{-- <div class="form-group col-md-6">
                                                                    <label>Select Role</label>
                                                                    <select name="role" class="form-control">
                                                                        <option value="" disabled selected>Change
                                                                            Role</option>
                                                                        <option value="teacher"
                                                                            {{ $item->role == 'admin' ? 'selected' : '' }}>
                                                                            admin</option>
                                                                        <option value="teacher"
                                                                            {{ $item->role == 'teacher' ? 'selected' : '' }}>
                                                                            teacher</option>
                                                                    </select>
                                                                    {{-- @error('status')
                                                            <p class="text-danger">{{ $message }}</p>
                                                        @enderror --}}
                                                                {{-- </div> --}}
                                                                <div class="form-group col-md-6">
                                                                    <label>Select Status</label>
                                                                    <select name="status" class="form-control">
                                                                        <option value="" disabled selected>Select
                                                                            Status</option>
                                                                        <option value="active"
                                                                            {{ $item->status == 'active' ? 'selected' : '' }}>
                                                                            active</option>
                                                                        <option value="inactive"
                                                                            {{ $item->status == 'inactive' ? 'selected' : '' }}>
                                                                            inactive</option>
                                                                    </select>
                                                                    {{-- @error('status')
                                                            <p class="text-danger">{{ $message }}</p>
                                                        @enderror --}}
                                                                </div>
                                                            </div>

                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary">Save
                                                                    Changes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>

                                            <th>Name</th>
                                            <th>I/C Number</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th>Edit</th>
                                        </tr>
                                    </tfoot>
                                </table>





                            </div>

                        </div>

                    </div>

                </div>

            </div>



            <!-- Add Modal -->
            <div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('student.store') }}" method="POST">
                            @csrf
                            <div class="modal-header">


                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <h5 class="modal-title" id="addTeacherModalLabel">Add New Student</h5>
                            </div>

                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label>IC Number</label>
                                    <input type="text" name="ic" class="form-control" required maxlength="12">
                                </div>

                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" required
                                        placeholder="Enter your Gmail email" pattern="[a-zA-Z0-9._%+-]+@moe-dl.edu.my">
                                </div>


                                <div class="form-group">
                                    <label>Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>

                                <input type="hidden" name="role" value="student">
                                <input type="hidden" name="status" value="active">
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Add Student</button>
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
                    searchPlaceholder: "🔍 Search student..."
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
