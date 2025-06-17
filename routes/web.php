<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Teacher\TeacherDashboardController;
use App\Http\Controllers\Student\StudentDashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\NotetypesController;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuizCategoryController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\SubmissionsController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\TeacherExaminationController;
use App\Http\Controllers\SubjectiveQuestionController;
use App\Http\Controllers\SubjectiveAnswerController;
use App\Http\Controllers\EssayAnswerController;
use App\Http\Controllers\EssayQuestionController;


Route::get('/', function () {
    return view('welcome');
});
//AdminController
Route::group(['prefix' => 'admin'], function () {
    Route::group(['middleware' => 'admin.guest'], function () {
        Route::get('login', [AdminController::class, 'index'])->name('admin.login');
        Route::get('register', [AdminController::class, 'register'])->name('admin.register');
        Route::post('login', [AdminController::class, 'authenticate'])->name('admin.authenticate');
        //ForgotPassword
        Route::get('show-forgot-password', [AdminController::class, 'showForgotPasswordForm'])->name('admin.forgotpassword');
        Route::post('sent-reset-link-email', [AdminController::class, 'sendResetLinkEmail'])->name('admin.emailforgot');
        Route::get('password/reset/{token}', [AdminController::class, 'showResetForm'])->name('password.reset');
        Route::post('passwordchange', [AdminController::class, 'passwordchange'])->name('admin.passwordchange');
    });
    Route::group(['middleware' => 'admin.auth'], function () {



        Route::get('admin/logout', [AdminController::class, 'logout'])->name('admin.logout');
        Route::get('admin/dashboard/chart/grades', [DashboardController::class, 'graphBarGred'])->name('admin.dashboard.chart.grades');
        Route::get('admin/dashboard/chart/average-class', [DashboardController::class, 'graphPurataKelas'])->name('admin.dashboard.chart.average_class');
        Route::get('admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('form', [AdminController::class, 'form'])->name('admin.form');
        Route::get('data', [AdminController::class, 'data'])->name('admin.data');

        // //ForgotPassword
        // Route::get('admin/show-forgot-password', [AdminController::class, 'showForgotPasswordForm'])->name('admin.forgotpassword');
        // Route::post('admin/sent-reset-link-email', [AdminController::class, 'sendResetLinkEmail'])->name('admin.emailforgot');
        // Route::get('admin/password-reset', [AdminController::class, 'resetpassword'])->name('admin.resetpassword');
        // Route::post('admin/passwordchange', [AdminController::class, 'passwordchange'])->name('admin.passwordchange');


        // Teacher Management
        Route::get('teacher/read', [TeacherController::class, 'index'])->name('teacher.read');
        Route::post('teacher/store', [TeacherController::class, 'store'])->name('teacher.store');
        Route::get('teacher/create', [TeacherController::class, 'create'])->name('teacher.create');
        Route::get('teacher/edit/{ic}', [TeacherController::class, 'edit'])->name('teacher.edit');
        Route::post('teacher/update/{ic}', [TeacherController::class, 'update'])->name('teacher.update');

        //Student Management
        Route::get('student/read', [StudentController::class, 'index'])->name('student.read');
        Route::post('student/store', [StudentController::class, 'store'])->name('student.store');
        Route::get('student/create', [StudentController::class, 'create'])->name('student.create');
        Route::get('student/edit/{ic}', [StudentController::class, 'edit'])->name('student.edit');
        Route::post('student/update/{ic}', [StudentController::class, 'update'])->name('student.update');


        //NotetypesController
        Route::get('notetypes/read', [NotetypesController::class, 'index'])->name('notetypes.read');
        Route::post('notetypes/store', [NotetypesController::class, 'store'])->name('notetypes.store');
        Route::get('notetypes/create', [NotetypesController::class, 'create'])->name('notetypes.create');
        Route::get('notetypes/edit/{id}', [NotetypesController::class, 'edit'])->name('notetypes.edit');
        Route::post('notetypes/update/{id}', [NotetypesController::class, 'update'])->name('notetypes.update');
        Route::get('notetypes/delete/{id}', [NotetypesController::class, 'delete'])->name('notetypes.delete');

        //NoteController
        Route::get('notes/read', [NotesController::class, 'index'])->name('admin.notes.read');
        Route::post('notes/store', [NotesController::class, 'store'])->name('admin.notes.store');
        Route::get('notes/download/{file}', [NotesController::class, 'download'])->name('admin.notes.download');
        Route::get('notes/view/{file}', [NotesController::class, 'view'])->name('admin.notes.view');
        Route::get('notes/create', [NotesController::class, 'create'])->name('admin.notes.create');
        Route::get('notes/edit/{id}', [NotesController::class, 'edit'])->name('admin.notes.edit');
        Route::post('notes/update/{id}', [NotesController::class, 'update'])->name('admin.notes.update');
        Route::get('notes/delete/{id}', [NotesController::class, 'delete'])->name('admin.notes.delete');

        //Quiz Controller
        Route::get('quiz/read/{quiz_category_id}', [QuestionController::class, 'index'])->name('admin.quiz.read');
        Route::post('quiz/store', [QuestionController::class, 'store'])->name('admin.quiz.store');
        Route::post('quiz/update/{id}', [QuestionController::class, 'update'])->name('admin.quiz.update');
        Route::get('quiz/delete/{id}', [QuestionController::class, 'delete'])->name('admin.quiz.delete');

        Route::get('quiz/{quiz_id}/results', [QuestionController::class, 'showResults'])->name('admin.quiz.results');


        //Subjective Controller
        Route::get('subjective/read/{quiz_category_id}', [SubjectiveQuestionController::class, 'index'])->name('admin.subjective.read');
        Route::post('subjective/store', [SubjectiveQuestionController::class, 'store'])->name('admin.subjective.store');
        Route::post('subjective/update/{id}', [SubjectiveQuestionController::class, 'update'])->name('admin.subjective.update');
        Route::get('subjective/delete/{id}', [SubjectiveQuestionController::class, 'delete'])->name('admin.subjective.delete');
        Route::get('review-student-answers/{id}', [SubjectiveAnswerController::class, 'reviewAnswersAdmin'])->name('admin.review.answers');

        // Cikgu yang cipta kategori: semak semua jawapan pelajar dan beri markah/komen
        Route::get('subjective/subshow/{quiz_category_id}/{question_id}', [SubjectiveQuestionController::class, 'adminSubshow'])->name('admin.quiz.subshow');

        // Cikgu lain (bukan pencipta): hanya tengok jawapan student dan komen oleh cikgu asal
        Route::get('subjective/review/{id}', [SubjectiveAnswerController::class, 'review'])->name('admin.answer.review');
        Route::post('/admin/subjective/mark/{answer_id}', [SubjectiveAnswerController::class, 'adminmark'])->name('admin.answer.mark');


        //Essay Question
        Route::get('essay/read/{quiz_category_id}', [EssayQuestionController::class, 'index'])->name('admin.essay.read');
        Route::post('essay/store', [EssayQuestionController::class, 'store'])->name('admin.essay.store');
        Route::post('essay/update/{id}', [EssayQuestionController::class, 'update'])->name('admin.essay.update');
        Route::get('essay/delete/{id}', [EssayQuestionController::class, 'delete'])->name('admin.essay.delete');
        Route::get('review-student-answers/{id}', [EssayAnswerController::class, 'reviewAnswersAdmin'])->name('admin.review.answers');

        // Cikgu yang cipta kategori: semak semua jawapan pelajar dan beri markah/komen
        Route::get('essay/subshow/{quiz_category_id}/{question_id}', [EssayQuestionController::class, 'adminSubshow'])->name('admin.quiz.essayshow');

        // Cikgu lain (bukan pencipta): hanya tengok jawapan student dan komen oleh cikgu asal
        // Route::get('essay/review/{id}', [EssayAnswerController::class, 'review'])->name('admin.answer.review');
        Route::post('/admin/essay/mark/{answer_id}', [EssayAnswerController::class, 'adminmark'])->name('admin.answer.essmark');



        //QuizCategory
        Route::get('quizcategory/read', [QuizCategoryController::class, 'index'])->name('admin.quizcategory.read');
        Route::post('quizcategory/store/', [QuizCategoryController::class, 'store'])->name('admin.quizcategory.store');
        Route::get('quizcategory/create', [QuizCategoryController::class, 'create'])->name('admin.quizcategory.create');
        Route::get('quizcategory/edit/{id}', [QuizCategoryController::class, 'edit'])->name('admin.quizcategory.edit');
        Route::post('quizcategory/update/{id}', [QuizCategoryController::class, 'update'])->name('admin.quizcategory.update');
        Route::get('quizcategory/delete/{id}', [QuizCategoryController::class, 'delete'])->name('admin.quizcategory.delete');

        //ClassController
        Route::get('classrooms/read', [ClassroomController::class, 'index'])->name('admin.classrooms.read');

        Route::post('classrooms/store', [ClassroomController::class, 'store'])->name('admin.classrooms.store');

        Route::post('classrooms/{id}', [ClassroomController::class, 'update'])->name('admin.classrooms.update');


        // Senarai peperiksaan
        Route::get('exams/read', [ExamController::class, 'index'])->name('admin.exams.read');


        // Simpan peperiksaan baru
        Route::post('exams/store', [ExamController::class, 'store'])->name('admin.exams.store');


        // Kemas kini peperiksaan
        Route::post('exams/update/{exam}', [ExamController::class, 'update'])->name('admin.exams.update');

        // Padam peperiksaan
        Route::get('exams/{exam}', [ExamController::class, 'delete'])->name('admin.exams.delete');

        Route::get('/exams/{exam_id}/classroom/{classroom_id}/marks', [ExamController::class, 'viewMarks'])
            ->name('admin.exams.viewMarks');



        // Route::get('assignments/create', [AssignmentController::class, 'index'])->name('admin.assignments.create');
        // // Route::get('exam', [AdminController::class, 'exam'])->name('assign_classes.form');
        // Route::get('myclass', [AdminController::class, 'myclass'])->name('admin.myclass');
    });
});

//TeacherController
Route::group(['prefix' => 'teacher'], function () {
    Route::group(['middleware' => 'teacher.guest'], function () {
        Route::get('login', [TeacherController::class, 'login'])->name('teacher.login');
        Route::post('login', [TeacherController::class, 'authenticate'])->name('teacher.authenticate');

        //ForgotPassword
        Route::get('show-forgot-password', [TeacherController::class, 'showForgotPasswordForm'])->name('teacher.forgotpassword');
        Route::post('sent-reset-link-email', [TeacherController::class, 'sendResetLinkEmail'])->name('teacher.emailforgot');
        Route::get('password/reset/{token}', [TeacherController::class, 'showResetForm'])->name('teacher.password.reset');
        Route::post('passwordchange', [TeacherController::class, 'passwordchange'])->name('teacher.passwordchange');
    });

    Route::group(['middleware' => 'teacher.auth'], function () {


        Route::get('change_password', [TeacherController::class, 'changePassword'])->name('teacher.change_password');
        Route::post('update_password', [TeacherController::class, 'updatePassword'])->name('teacher.updatePassword');
        Route::get('teacher/dashboard', [TeacherDashboardController::class, 'index'])->name('teacher.dashboard');
        Route::get('myclass', [TeacherController::class, 'myclass'])->name('teacher.myclass');
        Route::get('logout', [TeacherController::class, 'logout'])->name('teacher.logout');

        //NoteController
        Route::get('notes/read', [NotesController::class, 'index'])->name('teacher.notes.read');
        Route::post('notes/store', [NotesController::class, 'store'])->name('teacher.notes.store');
        Route::get('notes/download/{file}', [NotesController::class, 'download'])->name('teacher.notes.download');
        Route::get('notes/view/{file}', [NotesController::class, 'view'])->name('teacher.notes.view');
        Route::get('notes/create', [NotesController::class, 'create'])->name('teacher.notes.create');
        Route::get('notes/edit/{id}', [NotesController::class, 'edit'])->name('teacher.notes.edit');
        Route::post('notes/update/{id}', [NotesController::class, 'update'])->name('teacher.notes.update');
        Route::get('notes/delete/{id}', [NotesController::class, 'delete'])->name('teacher.notes.delete');

        //Quiz Controller
        Route::get('quiz/read/{quiz_category_id}', [QuestionController::class, 'index'])->name('teacher.quiz.read');
        Route::post('quiz/store', [QuestionController::class, 'store'])->name('teacher.quiz.store');
        Route::post('quiz/update/{id}', [QuestionController::class, 'update'])->name('teacher.quiz.update');
        Route::get('quiz/delete/{id}', [QuestionController::class, 'delete'])->name('teacher.quiz.delete');

        Route::get('quiz/{quiz_id}/results', [QuestionController::class, 'showResults'])->name('teacher.quiz.results');

        //Subjective Controller
        Route::get('subjective/read/{quiz_category_id}', [SubjectiveQuestionController::class, 'index'])->name('teacher.subjective.read');
        Route::post('subjective/store', [SubjectiveQuestionController::class, 'store'])->name('teacher.subjective.store');
        Route::post('subjective/update/{id}', [SubjectiveQuestionController::class, 'update'])->name('teacher.subjective.update');
        Route::get('subjective/delete/{id}', [SubjectiveQuestionController::class, 'delete'])->name('teacher.subjective.delete');
        // Cikgu yang cipta kategori: semak semua jawapan pelajar dan beri markah/komen
        Route::get('subjective/subshow/{quiz_category_id}/{question_id}', [SubjectiveQuestionController::class, 'subshow'])->name('teacher.quiz.subshow');

        // Cikgu lain (bukan pencipta): hanya tengok jawapan student dan komen oleh cikgu asal
        Route::get('subjective/review/{id}', [SubjectiveAnswerController::class, 'review'])->name('teacher.answer.review');
        Route::post('/teacher/subjective/mark/{answer_id}', [SubjectiveAnswerController::class, 'mark'])->name('teacher.answer.essmark');

        //Essay Question
        //Essay Controller
        Route::get('essay/read/{quiz_category_id}', [EssayQuestionController::class, 'index'])->name('teacher.essay.read');
        Route::post('essay/store', [EssayQuestionController::class, 'store'])->name('teacher.essay.store');
        Route::post('essay/update/{id}', [EssayQuestionController::class, 'update'])->name('teacher.essay.update');
        Route::get('essay/delete/{id}', [EssayQuestionController::class, 'delete'])->name('teacher.essay.delete');
        // Cikgu yang cipta kategori: semak semua jawapan pelajar dan beri markah/komen
        Route::get('essay/subshow/{quiz_category_id}/{question_id}', [EssayQuestionController::class, 'subshow'])->name('teacher.quiz.essayshow');

        // Cikgu lain (bukan pencipta): hanya tengok jawapan student dan komen oleh cikgu asal
        Route::get('essay/review/{id}', [EssayAnswerController::class, 'review'])->name('teacher.essay.review');
        Route::post('/teacher/essay/mark/{answer_id}', [EssayAnswerController::class, 'mark'])->name('teacher.essay.mark');





        //QuizCategory
        Route::get('quizcategory/read', [QuizCategoryController::class, 'index'])->name('teacher.quizcategory.read');
        Route::post('quizcategory/store', [QuizCategoryController::class, 'store'])->name('teacher.quizcategory.store');
        Route::get('quizcategory/create', [QuizCategoryController::class, 'create'])->name('teacher.quizcategory.create');
        Route::get('quizcategory/edit/{id}', [QuizCategoryController::class, 'edit'])->name('teacher.quizcategory.edit');
        Route::post('quizcategory/update/{id}', [QuizCategoryController::class, 'update'])->name('teacher.quizcategory.update');
        Route::get('quizcategory/delete/{id}', [QuizCategoryController::class, 'delete'])->name('teacher.quizcategory.delete');

        //ClassroomController
        Route::get('classrooms', [ClassroomController::class, 'index'])->name('teacher.classrooms.index');
        Route::get('classrooms/{id}/edit', [ClassroomController::class, 'edit'])->name('teacher.classrooms.edit');
        Route::post('classrooms/{id}', [ClassroomController::class, 'updateByTeacher'])->name('teacher.classrooms.update');

        //AsssignmnetController
        // Papar semua tugasan untuk kelas tertentu (classroom_id sebagai param)
        Route::get('assignment/read/{classroom}', [AssignmentController::class, 'index'])->name('teacher.assignment.index');

        // Create & Store assignment for that classroom
        //Route::get('assignment/create/{classroom}', [AssignmentController::class, 'create'])->name('teacher.assignment.create');
        Route::post('assignment/store/{classroom}', [AssignmentController::class, 'store'])->name('teacher.assignment.store');

        // Edit, update, delete tugasan
        //Route::get('assignment/edit/{id}', [AssignmentController::class, 'edit'])->name('teacher.assignment.edit');
        Route::post('assignment/update/{id}', [AssignmentController::class, 'update'])->name('teacher.assignment.update');
        Route::get('assignment/delete/{id}', [AssignmentController::class, 'delete'])->name('teacher.assignment.delete');

        // Optional download / view file
        Route::get('assignment/download/{file}', [AssignmentController::class, 'download'])->name('teacher.assignment.download');
        Route::get('assignment/view/{file}', [AssignmentController::class, 'view'])->name('teacher.assignment.view');
        // View student submission
        Route::get('assignments/{assignment}/submissions', [AssignmentController::class, 'viewSubmissions'])->name('teacher.assignment.submissions');
        Route::get('submission/download/{file}', [SubmissionsController::class, 'download'])->name('teacher.submission.download');

        //Senarai peperiksaan dan kelas teacher
        Route::get('exams', [TeacherExaminationController::class, 'index'])->name('teacher.exams.index');

        // Form isi markah untuk exam + class tertentu
        Route::get('exams/{exam}/classrooms/{classroom}/marks', [TeacherExaminationController::class, 'fillMarks'])->name('teacher.exams.fillmarks');

        // Simpan markah selepas submit form
        Route::post('exams/{exam}/classrooms/{classroom}/marks', [TeacherExaminationController::class, 'storeMarks'])->name('teacher.exams.storemarks');


        Route::get('myclass', [TeacherController::class, 'myclass'])->name('teacher.myclass');
    });
});


Route::group(['prefix' => 'student'], function () {
    Route::group(['middleware' => 'teacher.guest'], function () {
        Route::get('login', [StudentController::class, 'login'])->name('student.login');
        Route::post('login', [StudentController::class, 'authenticate'])->name('student.authenticate');

        //ForgotPassword
        //ForgotPassword
        Route::get('show-forgot-password', [StudentController::class, 'showForgotPasswordForm'])->name('student.forgotpassword');
        Route::post('sent-reset-link-email', [StudentController::class, 'sendResetLinkEmail'])->name('student.emailforgot');
        Route::get('password/reset/{token}', [StudentController::class, 'showResetForm'])->name('student.password.reset');
        Route::post('passwordchange', [StudentController::class, 'passwordchange'])->name('student.passwordchange');
    });

    Route::group(['middleware' => 'student.auth'], function () {


        Route::get('change_password', [StudentController::class, 'changePassword'])->name('student.change_password');
        Route::post('update_password', [StudentController::class, 'updatePassword'])->name('student.updatePassword');
        Route::get('student/dashboard', [StudentDashboardController::class, 'index'])->name('student.dashboard');
        // Route::get('myclass', [StudentController::class, 'myclass'])->name('student.myclass');
        Route::get('logout', [StudentController::class, 'logout'])->name('student.logout');
        //Acess Quiz Category
        Route::get('quizcategory/read', [QuizCategoryController::class, 'index'])->name('student.quizcategory.read');
        //Quiz
        Route::get('quiz/start/{quiz_category_id}', [QuestionController::class, 'startquiz'])->name('student.quiz.startquiz');
        Route::post('quiz/submitans', [QuestionController::class, 'submitans'])->name('student.quiz.submit');
        //Resut all quiz by ca
        Route::get('quiz/results/all', [QuestionController::class, 'allResults'])->name('student.quiz.allResults');

        //Subjective Question

        Route::get('subjective/{category_id}', [SubjectiveAnswerController::class, 'showQuestionForm'])->name('student.subjective.form');
        Route::post('subjective/submit/{question_id}', [SubjectiveAnswerController::class, 'submitAnswer'])->name('student.subjective.submit');
        Route::get('subjective/answers/{category_id}', [SubjectiveAnswerController::class, 'showStudentAnswers'])->name('student.subjective.viewAnswer');


        //Essay Question
        Route::get('essay/{category_id}', [EssayAnswerController::class, 'showQuestionForm'])->name('student.essay.form');
        Route::post('essay/submit', [EssayAnswerController::class, 'submitAnswer'])->name('student.essay.submit');
        Route::get('essay/answers/{category_id}', [EssayAnswerController::class, 'showStudentAnswers'])->name('student.essay.viewAnswer');

        //Classrooms
        Route::get('classrooms', [ClassroomController::class, 'index'])->name('student.classrooms.index');
        //        //AssignmentController
        Route::get('/assignment/download/{file}', [AssignmentController::class, 'download'])->name('student.assignment.download');
        //Submission
        Route::get('submissions/{assignment}', [SubmissionsController::class, 'index'])->name('student.submission.index');
        Route::post('submissions/{assignment}/submit', [SubmissionsController::class, 'submit'])->name('student.submission.submit');
        Route::post('submissions/{assignment}/update', [SubmissionsController::class, 'update'])->name('student.submission.update');
        Route::get('submissions/download/{file}', [SubmissionsController::class, 'download'])->name('student.submission.download');
    });
});

// //Acess Quiz Category
// Route::get('quizcategory/read', [QuizCategoryController::class, 'index'])->name('quizcategory.read');
// Class





//Route::get('class/read', [ClassroomController::class, 'index'])->name('admin.class.read');
//Route::post('class/store', [ClassroomController::class, 'store'])->name('admin.class.store');
// Route::get('class/download/{file}', [ClassroomController::class, 'download'])->name('admin.class.download');
// Route::get('class/view/{file}', [ClassroomController::class, 'view'])->name('admin.class.view');
// Route::get('class/create', [ClassroomController::class, 'create'])->name('admin.class.create');
// Route::get('class/edit/{id}', [ClassroomController::class, 'edit'])->name('admin.class.edit');
// Route::post('class/update/{id}', [ClassroomController::class, 'update'])->name('admin.class.update');
// Route::get('class/delete/{id}', [ClassroomController::class, 'delete'])->name('admin.class.delete');
