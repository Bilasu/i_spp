<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Assignment;
use App\Models\Submission;

class SubmissionsController extends Controller
{
    public function index($assignmentId)
    {
        $assignment = Assignment::findOrFail($assignmentId);

        $student_ic = Auth::guard('student')->user()?->ic;
        // dd('Student guard user:', $student_ic);
        $studentSubmission = Submission::where('assignment_id', $assignmentId)
            ->where('student_ic', $student_ic)
            ->first();

        return view('student.assignment.index', compact('assignment', 'studentSubmission'));
    }

    public function submit(Request $request, $assignmentId)
    {
        $request->validate([
            'file' => 'required|file|max:1048576', // max 10MB
            'comment' => 'nullable|string|max:500',
        ]);

        $student_ic = Auth::guard('student')->user()?->ic;

        // Cegah double submission
        if (Submission::where('assignment_id', $assignmentId)->where('student_ic', $student_ic)->exists()) {
            return redirect()->back()->with('error', 'You have already submitted this assignment.');
        }

        // Simpan fail
        $filePath = null;
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
            $filePath = 'uploads/' . $cleanFilename;
        }

        // Cipta submission baru
        Submission::create([
            'assignment_id' => $assignmentId,
            'student_ic' => $student_ic,
            'file_path' => $filePath,
            'comment' => $request->comment,

        ]);

        return redirect()
            ->route('student.submission.index', $assignmentId)
            ->with('success', 'Assignment submitted successfully.');
    }

    public function update(Request $request, $assignmentId)
    {
        $request->validate([
            'file' => 'nullable|file|max:1048576', // Tak wajib kalau cuma nak tukar komen
            'comment' => 'nullable|string|max:500',
        ]);

        $student_ic = Auth::user()->ic;
        $assignment = Assignment::findOrFail($assignmentId);

        $submission = Submission::where('assignment_id', $assignmentId)
            ->where('student_ic', $student_ic)
            ->firstOrFail();

        $late = now()->gt($assignment->due_date); // Check jika lewat

        // Kalau pelajar upload fail baru
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
            $submission->file_path = 'uploads/' . $cleanFilename;
        }

        // Update komen dan masa serahan
        $submission->comment = $request->comment;
        $submission->submitted_at = now();
        $submission->save();

        return redirect()
            ->route('student.classroom.index', $assignmentId)
            ->with('success', $late
                ? 'Assignment submitted successfully. <strong>Your submission is stated as being late</strong>'
                : 'Assignment submitted successfully.');
    }

    public function download($file)
    {

        return response()->download(public_path('uploads/' . $file));
    }
}
