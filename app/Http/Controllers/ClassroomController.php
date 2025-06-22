<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Classroom;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Log;

class ClassroomController extends Controller
{
    public function index()
    {
        if (Auth::guard('admin')->check()) {
            // Fetch all classrooms with their teachers and students
            $classrooms = Classroom::with(['teachers', 'students'])->get();

            // Fetch all teachers
            $teachers = User::where('role', 'teacher')->get();
            $students = User::where('role', 'student')->get();
            // Fetch all students who are NOT assigned to any class
            $studentsWithoutClass = User::where('role', 'student')
                ->whereDoesntHave('classrooms') // Only students without any class
                ->get();
            // ✅ Dapatkan class_name yang sudah digunakan
            $usedClassNames = Classroom::pluck('class_name')->toArray();
            // dd($usedClassNames);
            return view('admin.classrooms.list', compact('classrooms', 'teachers', 'studentsWithoutClass', 'students', 'usedClassNames'));
        }
        // Handle Teacher view
        elseif (Auth::guard('teacher')->check()) {
            $teacher = Auth::guard('teacher')->user();
            $students = User::where('role', 'student')->get();

            // Only get active classrooms
            $classrooms = $teacher->classrooms()
                ->where('status', 'active') // Add condition for active status
                ->with(['teachers', 'students'])
                ->get();

            return view('teacher.classrooms.index', compact('classrooms', 'students'));
        }

        // Handle Student view
        elseif (Auth::guard('student')->check()) {
            $student = Auth::guard('student')->user();
            //dd(Auth::guard('student')->check());
            // Only get active classrooms
            $classrooms = $student->classrooms()
                ->where('status', 'active') // Add condition for active status
                ->with(['teachers', 'students'])
                ->get();

            // if ($classrooms->isEmpty()) {
            //     // If no active classrooms are found, redirect with a message
            //     return redirect()->route('student.classrooms.index')->with('error', 'You do not have access to any active classrooms.');
            // }
            // dd($classrooms); // Debug if classrooms are being fetched correctly

            return view('student.classrooms.index', compact('classrooms'));
        }
    }






    // $user = Auth::user();

    // if ($user->role === 'admin') {
    //     // Admin boleh lihat semua kelas
    //     $classrooms = Classroom::with(['teachers', 'students'])->get();
    //     return view('admin.classrooms.index', compact('classrooms'));
    // } else {
    //     // Teacher & Student hanya lihat kelas yang mereka diassign
    //     $classrooms = Classroom::with(['teachers', 'students'])->get();
    //     return view('teacher.classrooms.index', compact('classrooms'));
    // }
    // public function create()
    // {
    //     // Fetch all students who are NOT assigned to any class
    //     $students = User::where('role', 'student')
    //         ->whereDoesntHave('classrooms') // Students without any assigned class
    //         ->get();

    //     // Fetch all teachers
    //     $teachers = User::where('role', 'teacher')->get();

    //     // Pass the filtered students to the view
    //     return view('admin.classrooms.list', compact('students', 'teachers'));
    // }


    public function store(Request $request)
    {
        // Ambil senarai guru
        $teachers = User::where('role', 'teacher')->get();

        // Ambil pelajar yang belum berada dalam mana-mana kelas (1 pelajar = 1 kelas sahaja)
        $students = User::where('role', 'student')
            ->whereDoesntHave('classrooms') // pastikan tiada kelas langsung
            ->get();

        $validClassNames = [
            '4 Acalaypha',
            '4 Alyssium',
            '4 Andalas',
            '4 Aster',
            '4 Amarilis',
            '4 Allium',
            '4 Azalea',
            '5 Acalyapha',
            '5 Alyssium',
            '5 Andalas',
            '5 Aster',
            '5 Amarilis',
            '5 Allium',
            '5 Azalea',
        ];

        // Validasi input
        $request->validate([
            'class_name' => 'required|in:' . implode(',', $validClassNames),
            'teacher_ic' => 'required|exists:users,ic',
            'students' => 'nullable|array',
            'students.*' => 'exists:users,ic',
        ]);

        // ✅ Normalize class_name (trim + title case)
        $normalizedClassName = ucwords(strtolower(trim($request->class_name)));

        // ✅ Cek kalau class_name dah wujud (case insensitive)
        $existing = Classroom::whereRaw('LOWER(TRIM(class_name)) = ?', [strtolower(trim($request->class_name))])->first();

        if ($existing) {
            return back()->with('error', 'Class name already exists.');
        }

        try {
            // Cipta kelas
            $classroom = Classroom::create([
                'class_name' => $normalizedClassName,
            ]);

            // Sambungkan guru
            $classroom->users()->attach($request->teacher_ic, ['role' => 'teacher']);

            // Sambungkan pelajar jika ada
            if ($request->filled('students')) {
                foreach ($request->students as $ic) {
                    $classroom->users()->attach($ic, ['role' => 'student']);
                }
            }

            // Jika berjaya
            return redirect()->route('admin.classrooms.read')->with('success', 'Class added successfully.');
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->with('error', 'Error adding class: ' . $ex->getMessage());
        }
    }


    public function update(Request $request, $id)
    {
        $classroom = Classroom::findOrFail($id);

        $validClassNames = [
            '4 Acalyapha',
            '4 Alyssium',
            '4 Andalas',
            '4 Aster',
            '4 Amarilis',
            '4 Allium',
            '4 Azalea',
            '5 Acalyapha',
            '5 Alyssium',
            '5 Andalas',
            '5 Aster',
            '5 Amarilis',
            '5 Allium',
            '5 Azalea',
        ];
        // Validation
        $request->validate([
            'class_name' => 'required|in:' . implode(',', $validClassNames),
            'teacher_ic' => 'required|exists:users,ic',
            'student_ics' => 'nullable|array',
            'student_ics.*' => 'exists:users,ic',
            'status' => 'required|in:active,inactive',
        ]);

        // Check if class name already exists for other classes
        $existing = Classroom::where('class_name', $request->class_name)
            ->where('id', '!=', $id)
            ->first();

        if ($existing) {
            return back()->with('error', 'This class already exists');
        }

        // Get old data
        $oldTeacher = $classroom->users()->wherePivot('role', 'teacher')->pluck('ic')->first();
        $oldStudents = $classroom->users()->wherePivot('role', 'student')->pluck('ic')->sort()->values()->toArray();

        // New data from request
        $newTeacher = $request->teacher_ic;
        $newStudents = $request->student_ics ? collect($request->student_ics)->sort()->values()->toArray() : [];
        $newStatus = $request->status;

        // Check if data changed
        $teacherChanged = $oldTeacher !== $newTeacher;
        $studentsChanged = $oldStudents !== $newStudents;
        $statusChanged = $classroom->status !== $newStatus;
        $classNameChanged = $classroom->class_name !== $request->class_name;

        // If no changes made
        if (!$teacherChanged && !$studentsChanged && !$statusChanged && !$classNameChanged) {
            return back()->with('error', 'Please update something before submitting.');
        }

        // Update classroom basic info
        $classroom->update([
            'class_name' => $request->class_name,
            'status' => $newStatus,
        ]);

        // Update teacher if changed
        if ($teacherChanged) {
            $classroom->users()->wherePivot('role', 'teacher')->detach();
            $classroom->users()->attach($newTeacher, ['role' => 'teacher']);
        }

        // Update students if changed
        if ($studentsChanged) {
            $classroom->users()->wherePivot('role', 'student')->detach();
            foreach ($newStudents as $ic) {
                $classroom->users()->attach($ic, ['role' => 'student']);
            }
        }

        return redirect()->route('admin.classrooms.read')->with('success', 'Class details updated successfully.');
    }

    public function updateByTeacher(Request $request, $id)
    {
        // Pastikan teacher log masuk
        if (!Auth::guard('teacher')->check()) {
            abort(403, 'Unauthorized');
        }

        $classroom = Classroom::findOrFail($id);

        // Validasi
        $request->validate([
            'students' => 'nullable|array',
            'students.*' => 'exists:users,ic',
        ]);

        $oldStudents = $classroom->users()->wherePivot('role', 'student')->pluck('ic')->sort()->values()->toArray();
        $newStudents = $request->students ? collect($request->students)->sort()->values()->toArray() : [];

        // Tiada perubahan
        if ($oldStudents == $newStudents) {
            return back()->with('error', 'Please update something before submitting.');
        }

        // Detach semua pelajar lama
        $classroom->users()->wherePivot('role', 'student')->detach();

        // Attach semula pelajar baru
        foreach ($newStudents as $ic) {
            $classroom->users()->attach($ic, ['role' => 'student']);
        }

        return redirect()->route('teacher.classrooms.index')->with('success', 'Student details updated successfully.');
    }
}
