<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubjectiveAnswer;
use App\Models\SubjectiveQuestion;
use App\Models\QuizCategory;
use Illuminate\Support\Facades\Auth;

class SubjectiveAnswerController extends Controller
{
    // Papar borang jawapan pelajar
    // public function showQuestionForm($category_id)
    // {
    //     $questions = SubjectiveQuestion::where('quiz_category_id', $category_id)->get();
    //     return view('student.subjective.form', compact('questions', 'category_id'));
    // }

    // // Simpan jawapan pelajar
    // public function submitAnswer(Request $request)
    // {
    //     foreach ($request->answers as $question_id => $answer) {
    //         SubjectiveAnswer::create([
    //             'subjective_question_id' => $question_id,
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

        $answer = SubjectiveAnswer::findOrFail($answer_id);
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

        $answer = SubjectiveAnswer::findOrFail($answer_id);
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
    // Papar semua jawapan (untuk admin/cikgu semak)
    public function reviewAnswers()
    {
        $answers = SubjectiveAnswer::with('question', 'student')->get();
        return view('teacher.subjective.review', compact('answers'));
    }






    // Simpan markah & komen
    public function gradeAnswer(Request $request, $answer_id)
    {
        $answer = SubjectiveAnswer::findOrFail($answer_id);
        $answer->mark = $request->mark;
        $answer->comment = $request->comment;
        $answer->save();

        return back()->with('success', 'Mark and comments have been saved.');
    }


    public function showQuestionForm($category_id)
    {
        $userIc = Auth::guard('student')->user()->ic;

        // Dapatkan semua soalan untuk kategori ini
        $category = QuizCategory::with('subjectiveQuestions')->findOrFail($category_id);

        // Ambil semua jawapan pelajar untuk kategori ini
        $answers = SubjectiveAnswer::where('user_ic', $userIc)
            ->where('quiz_category_id', $category_id)
            ->get()
            ->keyBy('subjective_question_id');

        $answers = $answers ?? collect(); // fallback jaminan // supaya mudah akses ikut question id

        return view('student.subjective.form', compact('category', 'category_id', 'answers'));
    }


    // Simpan jawapan pelajar
    public function submitAnswer(Request $request, $question_id)
    {
        $userIc = Auth::guard('student')->user()->ic;
        $answerText = $request->input('answer');

        if ($answerText !== null && trim($answerText) !== '') {
            $question = SubjectiveQuestion::find($question_id);

            if ($question) {
                $exists = SubjectiveAnswer::where('user_ic', $userIc)
                    ->where('subjective_question_id', $question_id)
                    ->exists();

                if (!$exists) {
                    SubjectiveAnswer::create([
                        'subjective_question_id' => $question_id,
                        'user_ic' => $userIc,
                        'answer' => $answerText,
                        'quiz_category_id' => $question->quiz_category_id,
                    ]);
                }
            }
        }

        return redirect()->route('student.subjective.viewAnswer', $request->category_id)
            ->with('success', 'Answer submitted successfully.');
    }


    // Papar semula jawapan pelajar
    public function showStudentAnswers($category_id)
    {
        $userIc = Auth::guard('student')->user()->ic;

        $category = QuizCategory::with([
            'subjectiveQuestions.answers' => function ($query) use ($userIc) {
                $query->where('user_ic', $userIc);
            }
        ])->findOrFail($category_id);

        return view('student.subjective.view', compact('category'));
    }

    public function review($categoryId)
    {
        // Logic untuk review subjective answers by category ID
        // Contoh fetch data untuk view
        $category = QuizCategory::findOrFail($categoryId);
        $answers = SubjectiveAnswer::where('quiz_category_id', $categoryId)->get();

        return view('teacher.answer.review', compact('category', 'answers'));
    }
}
