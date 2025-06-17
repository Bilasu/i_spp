<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Notifications\StudentRegister;
use App\Notifications\StudentForgotPassword;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentController extends Controller
{
    public function index()
    {

        // $data['teachers'] = User::where('role', 'teacher', 'status')->latest()->get();
        // dd($data);

        $query = User::where('role', 'student', 'status')->latest('email');
        $users = $query->get();
        $data['user'] = $users;

        return view('admin.student.list', compact('users'));
    }

    public function store(Request $request)
    {
        // echo "ok";
        // dd($request->all());
        $request->validate(
            [

                'name' => 'required',
                'ic' => 'required|digits:12',
                'password' => 'required',
                'email' => 'required|email|ends_with:@moe-dl.edu.my', // 'email' => 'required|email|ends_with:@gmail.com',


            ],
            [
                'ic.digits' => 'IC number must be exactly 12 digits.',
                'email.ends_with' => 'Email must end with @moe-dl.edu.my', // 'email.ends_with' => 'Email must end with @gmail.com.',
            ]

        );
        $plainPassword = $request->password;
        $email = $request->email;

        // Check if the IC already exists
        if (User::where('email', $email)->exists()) {
            return redirect()->route('student.read')->with('error', 'User with this email already exists.');
        }


        // If not exists, proceed to create
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        // Ensure the 'ic' column exists in your users table and the User model has 'ic' as a fillable property if using mass assignment
        $user->ic = $request->ic; // Assuming IC is not required for students
        $user->password = Hash::make($plainPassword); // Admin sets an initial password
        $user->password_changed = false;
        $user->role = 'student';
        $user->status = 'active';
        $user->save();

        // // Isi nilai IC hanya jika role bukan student
        // if ($user->role !== 'student') {
        //     $user->ic = $request->ic;
        // }

        //🔔 Send notification email to teacher
        $user->notify(new StudentRegister($user, $plainPassword));

        return redirect()->route('student.read')->with('success', 'New Student Added Successfully');
    }

    public function update(Request $request, $ic)
    {
        $request->validate([
            'ic' => 'required|digits:12',
            'email' => 'required|email|ends_with:@moe-dl.edu.my',
        ], [
            'ic.digits' => 'IC number must be exactly 12 digits.',
            'email.ends_with' => 'Email must end with @moe-dl.edu.my.',
        ]);

        $user = User::find($ic);
        // Check if the provided IC is already taken by another user
        $existingUser = User::where('ic', $request->ic)->first();

        if ($existingUser && $existingUser->ic !== $user->ic) {
            return back()->with('error', 'This IC number already exists.');
        }

        // Check kalau user tak ubah apa-apa
        if (
            $request->name === $user->name &&
            $request->ic === $user->ic &&
            $request->email === $user->email &&

            $request->status === $user->status
        ) {
            return back()->with('error', 'Please update something before submitting.');
        }

        // If the email has changed, send a notification email
        $emailChanged = $request->email !== $user->email;

        // If there is a change, update data
        $user->name = $request->name;
        $user->ic = $request->ic;
        $user->email = $request->email;
        $user->status = $request->status;
        $user->update();

        // Check if password_changed is 0 (indicating first-time login or password not changed yet)
        if ($user->password_changed === 0 && $emailChanged) {
            // Send email to the user about the email change (can be the same email sent during registration)
            $plainPassword = 'This could be a default password or a specific message if needed';
            $user->notify(new StudentRegister($user, $plainPassword));  // Assuming this is the notification used to inform users
        }

        return redirect()->route('student.read')->with('success', 'Student Updated Successfully');
    }

    public function login()
    {
        return view('student.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'ic' => 'required|digits:8',
            'password' => 'required',
        ]);

        $students = User::where('ic', 'like', $request->ic . '%')->get();



        foreach ($students as $student) {
            $teacherICFirst8 = substr($student->ic, 0, 8);
            $teacherICLast4 = substr($student->ic, -4);

            if (
                $teacherICFirst8 === $request->ic &&
                Hash::check($request->password, $student->password) &&
                $student->status === 'active' &&
                $student->role === 'student'
            ) {
                Auth::guard('student')->login($student);

                // ⛔ Check kalau cikgu belum tukar password
                if (!$student->password_changed) {
                    return redirect()->route('student.change_password');
                }

                return redirect()->route('student.dashboard');
            }
        }

        return redirect()->route('student.login')->with('error', 'Invalid IC or password.');
    }

    public function logout()
    {
        Auth::guard('student')->logout();
        return redirect()->route('student.login')->with('success', 'Logout successfully.');
    }

    public function dashboard()
    {
        return view('student.dashboard');
    }

    public function changePassword()
    {
        return view('student.change_password');
    }

    public function updatePassword(Request $request)
    {
        // Validate input from form
        $request->validate([
            'old_password' => 'required', // Old password is required
            'password' => 'required|confirmed|min:6', // New password is required and must be confirmed
            // Confirm password is required
        ], [
            'password_confirmed' => 'The new password and confirmation password do not match.',
        ]);

        // Get the logged-in teacher's user model
        $student = User::find(Auth::guard('student')->id());

        // Check if the old password matches the stored password
        if (!Hash::check($request->old_password, $student->password)) {
            return back()->with('error', 'The old password is incorrect.'); // If old password is wrong, show error
        }
        // Check if the new password is the same as the old password
        if (Hash::check($request->password, $student->password)) {
            return back()->with('error', 'The new password cannot be the same as the old password.'); // If new password is same as old, show error
        }


        try {
            // Update password if old password is correct
            $student->password = Hash::make($request->password); // Hash the new password
            $student->password_changed = true; // Mark that the password has been updated
            $student->save(); // Save the changes
        } catch (\Exception $e) {
            // Return with error message if something goes wrong during the save process
            return back()->with('error', 'Unable to update the password. Please try again later.');
        }

        // Redirect with success message
        return redirect()->route('student.dashboard')->with('success', 'Password updated successfully.');
    }



    //Forgot Passwoord Section
    public function showForgotPasswordForm()
    {
        return view('student.forgetpassword');
    }

    public function sendResetLinkEmail(Request $request)
    {
        // 1. Validate IC input
        $request->validate([
            'ic' => 'required|digits:12|exists:users,ic', // Ensure IC exists in the users table
        ]);

        try {
            // 2. Retrieve user by IC
            $user = User::where('ic', $request->ic)->first();

            if (!$user) {
                return back()->with('error', 'Invalid or non-registered IC.');
            }

            // // 3. Generate a new plain password (optional: use random)
            // $plainPassword = Str::random(10);

            // // 4. Save the new hashed password to user
            // $user->password = bcrypt($plainPassword);
            // $user->save();

            // 5. Generate token for password reset link
            $token = Str::random(64);

            // 6. Save or update token in password_reset_tokens table
            DB::table('password_reset_tokens')->updateOrInsert(
                ['ic' => $user->ic],  // Use IC instead of email
                [
                    'token' => $token, // Simpan token tanpa di-hash
                    'created_at' => Carbon::now()
                ]
            );

            // 7. Send custom notification with temporary password + token
            $user->notify(new StudentForgotPassword($user, $token));

            // 8. Return success message
            return back()->with('success', 'We have emailed your account credentials!');
        } catch (\Exception $e) {
            // 9. Log and return error
            // \Log::error('Reset Email Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to send password reset email. Please try again.');
        }
    }

    public function showResetForm($token, Request $request)
    {
        // Find the user by their IC (which is passed via the query string or request)
        $user = User::where('ic', $request->ic)->first();

        if (!$user) {
            return redirect()->route('admin.forgot-password')->with('error', 'Invalid IC.');
        }

        return view('student.resetpassword', [
            'token' => $token,
            'ic' => $request->ic, // Pass IC instead of email to the view
        ]);
    }

    public function passwordchange(Request $request)
    {
        // Validate input
        $request->validate([
            'ic' => 'required|digits:12|exists:users,ic',
            'password' => 'required|confirmed|min:8',
            'token' => 'required',
        ], [
            'password.confirmed' => 'The password is not same.',
        ]);

        // Check if token exists and match
        $record = DB::table('password_reset_tokens')
            ->where('ic', $request->ic)
            ->first();

        // Gantikan Hash::check dengan perbandingan langsung
        if (!$record || $request->token !== $record->token) {
            return back()->withErrors(['token' => 'Invalid or expired token.']);
        }

        // Get the user
        $user = User::where('ic', $request->ic)->first();

        // Safely hash new password and save
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the token after reset
        DB::table('password_reset_tokens')->where('ic', $request->ic)->delete();

        // Redirect to login
        return redirect()->route('student.login')->with('success', 'Password has been reset!');
    }
}
