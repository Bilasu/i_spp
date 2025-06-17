<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
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
            'file' => 'required|file|max:10240', // max 10MB
            'comment' => 'nullable|string|max:500',
        ]);

        $student_ic = Auth::guard('student')->user()?->ic;

        // Cegah double submission
        if (Submission::where('assignment_id', $assignmentId)->where('student_ic', $student_ic)->exists()) {
            return redirect()->back()->with('error', 'Anda telah menghantar tugasan ini.');
        }

        // Simpan fail
        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalFilename = $file->getClientOriginalName();
            $file->move(public_path('uploads'), $originalFilename);
            $filePath = $originalFilename;
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
            ->with('success', 'Tugasan berjaya dihantar!');
    }

    public function update(Request $request, $assignmentId)
    {
        $request->validate([
            'file' => 'nullable|file|max:10240', // Tak wajib kalau cuma nak tukar komen
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
            // Padam fail lama jika ada
            if ($submission->file_path && Storage::disk('public')->exists($submission->file_path)) {
                Storage::disk('public')->delete($submission->file_path);
            }

            // Simpan fail baru
            $filePath = $request->file('file')->store('uploads', 'public');
            $submission->file_path = $filePath;
        }

        // Update komen dan masa serahan
        $submission->comment = $request->comment;
        $submission->submitted_at = now();
        $submission->save();

        return redirect()
            ->route('student.classroom.index', $assignmentId)
            ->with('success', $late
                ? 'Tugasan berjaya dikemas kini. <strong>Serahan anda akan dikira sebagai lewat.</strong>'
                : 'Tugasan berjaya dikemas kini.');
    }

    public function download($file)
    {

        return response()->download(public_path('uploads/' . $file));
    }
}
