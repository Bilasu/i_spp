<?php

namespace App\Http\Controllers;

use App\Models\EssayAnswer;
use App\Models\EssayQuestion;
use App\Models\QuizCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EssayAnswerController extends Controller
{
    // Papar borang jawapan pelajar
    // public function showQuestionForm($category_id)
    // {
    //     $questions = EssayQuestion::where('quiz_category_id', $category_id)->get();
    //     return view('student.essay.form', compact('questions', 'category_id'));
    // }

    // // Simpan jawapan pelajar
    // public function submitAnswer(Request $request)
    // {
    //     foreach ($request->answers as $question_id => $answer) {
    //         essayAnswer::create([
    //             's' => $question_id,
    //             'user_ic' => Auth::guard('student')->user()->ic,
    //             'answer' => $answer
    //         ]);
    //     }
    //     if (empty($request->answers)) {
    //         return back()->with(['errors' => 'Tiada jawapan dihantar.']);
    //     }

    //     return back()->with('success', 'Your answers have been submitted!');
    // }
    public function mark(Request $request, $answer_id)
    {
        $request->validate([
            'mark_obtained' => 'required|numeric|min:0',
            'comments' => 'nullable|string',
        ]);

        $answer = EssayAnswer::findOrFail($answer_id);
        $total = $answer->question->mark_total; // Dapatkan dari table essay_questions

        if ($request->mark_obtained > $total) {
            return back()->withInput()->withErrors([
                'mark_obtained' => 'Obtained mark cannot exceed total mark.'
            ]);
        }

        $answer->mark = $request->mark_obtained; // ✅ Simpan hanya nilai markah pelajar (integer)
        $answer->comment = $request->comments;
        $answer->save();

        return redirect()->back()->with('success', 'Mark and comments have been saved.');
    }


    public function adminmark(Request $request, $answer_id)
    {
        $request->validate([
            'mark_obtained' => 'required|numeric|min:0',
            'comments' => 'nullable|string',
        ]);

        $answer = EssayAnswer::findOrFail($answer_id);
        $total = $answer->question->mark_total; // Dapatkan dari table essay_questions

        if ($request->mark_obtained > $total) {
            return back()->withInput()->withErrors([
                'mark_obtained' => 'Obatined mark cannot exceed total mark.'
            ]);
        }

        $answer->mark = $request->mark_obtained; // ✅ Simpan hanya nilai markah pelajar (integer)
        $answer->comment = $request->comments;
        $answer->save();

        return redirect()->back()->with('success', 'Mark and comments have been saved.');
    }

    // Papar semua jawapan (untuk admin/cikgu semak)
    public function reviewAnswers()
    {
        $answers = EssayAnswer::with('question', 'student')->get();
        return view('teacher.essay.review', compact('answers'));
    }






    // Simpan markah & komen
    public function gradeAnswer(Request $request, $answer_id)
    {
        $answer = EssayAnswer::findOrFail($answer_id);
        $answer->mark = $request->mark;
        $answer->comment = $request->comment;
        $answer->save();

        return back()->with('success', 'Mark ');
    }


    public function showQuestionForm($category_id)
    {
        $userIc = Auth::guard('student')->user()->ic;

        // Dapatkan semua soalan untuk kategori ini
        $category = QuizCategory::with('EssayQuestions')->findOrFail($category_id);

        // Ambil semua jawapan pelajar untuk kategori ini
        $answers = EssayAnswer::where('user_ic', $userIc)
            ->where('quiz_category_id', $category_id)
            ->get()
            ->keyBy('essay_questions_id');

        $answers = $answers ?? collect(); // fallback jaminan // supaya mudah akses ikut question id

        return view('student.essay.form', compact('category', 'category_id', 'answers'));
    }


    // Simpan jawapan pelajar
    public function submitAnswer(Request $request)
    {
        $userIc = Auth::guard('student')->user()->ic;  // Get student IC
        $questionId = $request->input('question_id');
        $answerText = $request->input('answer');  // Get the answer text from the form

        // Make sure the answer isn't empty
        if ($answerText !== null && trim($answerText) !== '') {
            $question = EssayQuestion::find($questionId);

            if (!$question) {
                return back()->with('error', 'No question found with');
            }

            // Check if the student has already answered this question
            $exists = EssayAnswer::where('user_ic', $userIc)
                ->where('essay_questions_id', $questionId)
                ->exists();

            if (!$exists) {
                // Save the answer if it hasn't been answered yet
                EssayAnswer::create([
                    'essay_questions_id' => $questionId,
                    'user_ic' => $userIc,
                    'answer' => $answerText,
                    'quiz_category_id' => $question->quiz_category_id,
                ]);
            }
        }

        return redirect()->route('student.essay.viewAnswer', $request->category_id)
            ->with('success', 'Answer submitted successfully.');
    }

    // Papar semula jawapan pelajar
    public function showStudentAnswers($category_id)
    {
        $userIc = Auth::guard('student')->user()->ic;

        $category = QuizCategory::with([
            'EssayQuestions.answers' => function ($query) use ($userIc) {
                $query->where('user_ic', $userIc);
            }
        ])->findOrFail($category_id);

        return view('student.essay.view', compact('category'));
    }




    public function review($categoryId)
    {
        // Logic untuk review essay answers by category ID
        // Contoh fetch data untuk view
        $category = QuizCategory::findOrFail($categoryId);
        $answers = EssayAnswer::where('quiz_category_id', $categoryId)->get();

        return view('teacher.answer.review', compact('category', 'answers'));
    }
}
