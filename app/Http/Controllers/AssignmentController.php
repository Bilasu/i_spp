<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Classroom;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AssignmentController extends Controller
{
    // Papar semua tugasan milik guru yang log masuk
    public function index($classroomId)
    {
        $teacherIC = Auth::guard('teacher')->user()->ic;

        // Pastikan kelas itu memang ada cikgu login ini
        $classroom = Classroom::where('id', $classroomId)
            ->whereHas('teachers', fn($q) => $q->where('user_ic', $teacherIC))
            ->firstOrFail();

        // Tugasan kelas tu
        $assignments = Assignment::where('classroom_id', $classroomId)->get();

        // Pelajar kelas tu (untuk kira berapa submit)
        $students = $classroom->students;

        return view('teacher.assignment.index', compact('classroom', 'assignments', 'students'));
    }


    // Papar borang cipta tugasan baru
    // public function create($classroomId)
    // {
    //     $teacherIC = Auth::guard('teacher')->user()->ic;

    //     $classroom = Classroom::where('id', $classroomId)
    //         ->whereHas('teachers', fn($q) => $q->where('user_ic', $teacherIC))
    //         ->firstOrFail();
    //     return view('teacher.assignment.create', compact('classroom'));
    // }

    // Simpan tugasan baru
    public function store(Request $request, $classroomId)
    {
        $teacherIC = Auth::guard('teacher')->user()->ic;

        $classroom = Classroom::where('id', $classroomId)
            ->whereHas('teachers', fn($q) => $q->where('user_ic', $teacherIC))
            ->firstOrFail();

        if ($request->isMethod('POST')) {
            // Handle form submission (store the new assignment)
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'due_date' => 'nullable|date|after_or_equal:today',
                'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:1048576',
            ]);



            $assignment = new Assignment();
            $assignment->classroom_id = $classroomId;
            $assignment->title = $request->title;
            $assignment->description = $request->description;
            $assignment->due_date = $request->due_date;

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $originalFilename = $file->getClientOriginalName();

                // Buat folder jika belum ada
                if (!Storage::disk('public')->exists('uploads')) {
                    Storage::disk('public')->makeDirectory('uploads');
                }

                // Bersihkan nama fail (buang space, tanda pelik, dan buat lowercase)
                $cleanFilename = Str::slug(pathinfo($originalFilename, PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

                // Simpan fail
                Storage::disk('public')->putFileAs('uploads', $file, $cleanFilename);

                // Simpan nama fail dalam database
                $assignment->file_path = $cleanFilename;
            }

            $assignment->save();

            return redirect()->route('teacher.assignment.index', ['classroom' => $classroomId])
                ->with('success', 'Assignment added successfully.');
        }
    }


    // Papar borang edit
    // public function edit($id)
    // {
    //     // dd($id); // Periksa sama ada ID memang dihantar

    //     $assignment = Assignment::findOrFail($id);
    //     // dd($assignment);
    //     $classroom = Classroom::findOrFail($assignment->classroom_id);

    //     return view('teacher.assignment.edit', compact('assignment', 'classroom'));
    // }

    // Kemaskini tugasan
    public function update(Request $request, $id)
    {
        $assignment = Assignment::findOrFail($id);
        $classroom = Classroom::findOrFail($assignment->classroom_id);

        if ($request->isMethod('POST')) {
            // Validate the request data
            $request->validate([
                'classroom_id' => 'required|exists:classrooms,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'due_date' => 'nullable|date',
                'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:1048576',
            ]);

            try {
                $hasChange = false;

                // Check if classroom_id changed
                if ($request->classroom_id != $assignment->classroom_id) {
                    $hasChange = true;
                }

                // Check if title changed
                if ($request->title != $assignment->title) {
                    $hasChange = true;
                }

                // Check if description changed
                if ($request->description != $assignment->description) {
                    $hasChange = true;
                }

                // Check if due_date changed (handle nulls too)
                if ($request->due_date != $assignment->due_date) {
                    $hasChange = true;
                }

                // Check if file uploaded (always counted as change)
                if ($request->hasFile('file')) {
                    $hasChange = true;
                }

                // Kalau tiada perubahan langsung
                if (!$hasChange) {
                    return back()->with('error', 'Please update something before updating/submitting.');
                }

                // Handle the file upload if a new file is uploaded
                if ($request->hasFile('file')) {
                    $file = $request->file('file');
                    $originalFilename = $file->getClientOriginalName();

                    // Buat folder jika belum ada
                    if (!Storage::disk('public')->exists('uploads')) {
                        Storage::disk('public')->makeDirectory('uploads');
                    }

                    // Bersihkan nama fail (buang space, tanda pelik, dan buat lowercase)
                    $cleanFilename = Str::slug(pathinfo($originalFilename, PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

                    // Simpan fail
                    Storage::disk('public')->putFileAs('uploads', $file, $cleanFilename);

                    // Simpan nama fail dalam database
                    $assignment->file_path = $cleanFilename;
                }

                // Update assignment data
                $assignment->update([
                    'classroom_id' => $request->classroom_id,
                    'title' => $request->title,
                    'description' => $request->description,
                    'due_date' => $request->due_date,
                    'file_path' => $assignment->file_path,
                ]);

                return redirect()->route('teacher.assignment.index', ['classroom' => $request->classroom_id])
                    ->with('success', 'Tugasan berjaya dikemas kini.');
            } catch (\Exception $e) {
                return back()->with('error', 'Error went updating assignment: ' . $e->getMessage());
            }
        }
    }


    // Padam tugasan
    public function delete($id)
    {
        $assignment = Assignment::findOrFail($id);

        try {
            if ($assignment->file_path && Storage::disk('public')->exists($assignment->file_path)) {
                Storage::disk('public')->delete($assignment->file_path);
            }

            $assignment->delete();

            return redirect()->route('teacher.assignment.index', ['classroom' => $assignment->classroom_id])
                ->with('success', 'Assignment deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error went deleting assignment: ' . $e->getMessage());
        }
    }

    // Fungsi muat turun fail tugasan
    public function download($file)
    {

        return response()->download(public_path('uploads/' . $file));
    }

    public function viewSubmissions($assignmentId)
    {
        $teacherIC = Auth::guard('teacher')->user()->ic;

        $assignment = Assignment::with('classroom.students', 'submissions')
            ->where('id', $assignmentId)
            ->whereHas('classroom.teachers', fn($q) => $q->where('user_ic', $teacherIC))
            ->firstOrFail();

        $students = $assignment->classroom->students;

        return view('teacher.assignment.submissions', compact('assignment', 'students'));
    }

    // Add this method inside your AssignmentController


}
