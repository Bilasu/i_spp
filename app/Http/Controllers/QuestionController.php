<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\Question;
use App\Models\QuizCategory;
use App\Models\QuizResult; // Import the QuizResult model
use Symfony\Component\Console\Question\Question as QuestionQuestion;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{

    public function index(Request $request, $id)
    {
        $category = QuizCategory::findOrFail($id);
        $questions = $category->questions; // make sure method name in model is "questions"

        if (Auth::guard('teacher')->check()) {
            return view('teacher.quiz.list', compact('category', 'questions'));
        }

        return view('admin.quiz.list', compact('category', 'questions'));
        // Merge $data and compacted $notetypes
        // $data = array_merge($data, compact('notetypes'));

        // dd($data); // Dumping the merged data to see if it's passed correctly

        // if (Auth::guard('admin')->check()) {
        // return view('admin.quiz.list', $data);
        // } else {
        //     return view('teacher.quiz.list', $data);
        // }
    }
    // //
    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required',
            'opa' => 'required',
            'opb' => 'required',
            'opc' => 'required',
            'opd' => 'required',
            'ans' => 'required',
            'quiz_category_id' => 'required|exists:quiz_categories,id',
        ]);

        $q = new Question();
        $q->question = $request->question;
        $q->a = $request->opa;
        $q->b = $request->opb;
        $q->c = $request->opc;
        $q->d = $request->opd;
        $q->ans = $request->ans;
        $q->quiz_category_id = $request->quiz_category_id;
        $q->save();

        if (Auth::guard('teacher')->check()) {
            return redirect()->route('teacher.quiz.read', $request->quiz_category_id)
                ->with('success', 'Soalan berjaya ditambah.');
        }

        return redirect()->route('admin.quiz.read', $request->quiz_category_id)
            ->with('success', 'Soalan berjaya ditambah.');
    }

    // public function showByCategory($id)
    // {
    //     $category = QuizCategory::findOrFail($id);
    //     $questions = $category->questions; // ambil semua soalan kategori ini

    //     return view('admin.quiz.create', compact('category', 'questions'));
    // }

    // public function show()
    // {
    //     // Dapatkan ID soalan seterusnya dari session, mula dengan 1 kalau tiada
    //     $nextQ = Session::get('nextq', 1);

    //     // Cari satu soalan berdasarkan ID
    //     $question = Question::find($nextQ);

    //     // Jika soalan tiada (mungkin ID lebih dari jumlah soalan), boleh redirect atau tamat kuiz
    //     if (!$question) {
    //         return redirect('student.quiz.end'); // Halaman tamat kuiz
    //     }

    //     return view('admin.quiz.question')->with(['question' => $question]);
    // }

    public function update(Request $request, $id)
    {
        $request->validate([
            'question' => 'required',
            'opa' => 'required',
            'opb' => 'required',
            'opc' => 'required',
            'opd' => 'required',
            'ans' => 'required|in:a,b,c,d',
        ]);

        $question = Question::findOrFail($id);

        // Check kalau user tak ubah apa-apa
        if (
            $request->question === $question->question &&
            $request->opa === $question->a &&
            $request->opb === $question->b &&
            $request->opc === $question->c &&
            $request->opd === $question->d &&
            $request->ans === $question->ans
        ) {
            return back()->with('error', 'Please update something before submitting.');
        }

        // Update fields
        $question->question = $request->question;
        $question->a = $request->opa;
        $question->b = $request->opb;
        $question->c = $request->opc;
        $question->d = $request->opd;
        $question->ans = $request->ans;

        $question->save();

        // Redirect ikut role
        if (Auth::guard('teacher')->check()) {
            return redirect()->route('teacher.quiz.read', $question->quiz_category_id)
                ->with('success', 'Soalan berjaya dikemaskini.');
        }

        return redirect()->route('admin.quiz.read', $question->quiz_category_id)
            ->with('success', 'Question updated successfully.');
    }

    public function delete($id)
    {
        $data = Question::find($id);
        $categoryId = $data->quiz_category_id;
        $data->delete();

        // Redirect ikut role
        if (Auth::guard('teacher')->check()) {
            return redirect()->route('teacher.quiz.read', $categoryId)
                ->with('success', 'Soalan berjaya dipadam.');
        }

        return redirect()->route('admin.quiz.read', $categoryId)
            ->with('success', 'Question deleted successfully.');
    }



    public function startquiz($quiz_category_id)
    {
        // Store category ID in session
        Session::put('quiz_category_id', $quiz_category_id);
        Session::put('nextq', 0); // Reset to the first question
        Session::put('correctans', 0); // Initialize correct answer count
        Session::put('wrongans', 0); // Initialize wrong answer count

        // Retrieve the category and questions
        $category = QuizCategory::findOrFail($quiz_category_id);
        $questions = $category->questions->values();

        if ($questions->isEmpty()) {
            // Pass a variable to the view indicating that no questions are available
            return view('student.quiz.list', ['no_questions' => true]);
        }

        // Proceed to show the first question if available
        $currentIndex = 0;
        $question = $questions[$currentIndex];

        return view('student.quiz.list', [
            'quiz_category' => $category,
            'question' => $question,
            'currentIndex' => $currentIndex,
            'totalQuestions' => count($questions),
        ]);
    }

    public function submitans(Request $request)
    {
        // dd(Auth::guard('student')->user());

        // Retrieve session data
        $nextq = Session::get('nextq', 0); // Retrieve next question index from session
        $quiz_category_id = Session::get('quiz_category_id');
        $questions = QuizCategory::findOrFail($quiz_category_id)->questions;;
        // Validate the answer submission
        $request->validate([
            'ans' => 'required',
            'dbnans' => 'required',
        ]);

        // Update score based on the answer correctness
        if ($request->ans === $request->dbnans) {
            $correctans = Session::get('correctans', 0) + 1; // Increment correct answers
            Session::put('correctans', $correctans);
        } else {
            $wrongans = Session::get('wrongans', 0) + 1; // Increment wrong answers
            Session::put('wrongans', $wrongans);
        }

        // Increment question counter
        $nextq++;
        Session::put('nextq', $nextq);

        // Check if all questions are answered, if so, save results and return to results page
        if ($nextq >= $questions->count()) {
            $correct = Session::get('correctans');
            $wrong = Session::get('wrongans');

            // Save score to database
            $latestResult = QuizResult::create([
                'user_ic' => Auth::guard('student')->user()->ic,
                'quiz_category_id' => $quiz_category_id,
                'correct' => $correct,
                'wrong' => $wrong,
                'total' => $questions->count(),
                'taken_at' => now(),
            ]);

            // Retrieve previous results
            $previousResults = QuizResult::where('user_ic', Auth::guard('student')->user()->ic)
                ->where('quiz_category_id', $quiz_category_id)
                ->where('id', '!=', $latestResult->id)
                ->orderByDesc('taken_at')
                ->get();


            // Clear session for next quiz
            Session::forget(['nextq', 'correctans', 'wrongans', 'quiz_category_id']);

            // Return to the results page with latest and previous results
            return view('student.quiz.end', [
                'latestResult' => $latestResult,
                'previousResults' => $previousResults,
            ]);
        }

        // Get the next question if not finished
        $question = $questions[$nextq];

        // Return the next question view
        return view('student.quiz.list', compact('question'));
    }

    public function showResults($quiz_id)
    {
        $quizResults = QuizResult::with(['user.classrooms'])
            ->where('quiz_category_id', $quiz_id)
            ->get();

        // Check role via guard (assuming separate guards for admin & teacher)
        if (Auth::guard('admin')->check()) {
            return view('admin.quiz.quizresults', compact('quizResults'));
        } elseif (Auth::guard('teacher')->check()) {
            return view('teacher.quiz.quizresults', compact('quizResults'));
        } else {
            abort(403, 'Unauthorized');
        }
    }

    public function allResults()
    {
        $userIc = Auth::guard('student')->user()->ic;

        // Ambil semua keputusan pelajar dan group by kategori
        $resultsByCategory = DB::table('quiz_results')
            ->join('quiz_categories', 'quiz_results.quiz_category_id', '=', 'quiz_categories.id') // betulkan sini
            ->select(
                'quiz_categories.id as category_id',
                'quiz_categories.name as category_name',
                'quiz_results.correct',
                'quiz_results.wrong',
                'quiz_results.total',
                'quiz_results.taken_at'
            )
            ->where('quiz_results.user_ic', $userIc)
            ->orderBy('quiz_results.taken_at', 'desc')
            ->get()
            ->groupBy('category_name');

        return view('student.quiz.all_results', compact('resultsByCategory'));
    }
}
