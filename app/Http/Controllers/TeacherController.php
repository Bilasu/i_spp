<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Notifications\TeacherRegister;
use App\Notifications\TeacherForgotPassword;
use Carbon\Carbon;

class TeacherController extends Controller
{
    public function index()
    {

        // $data['teachers'] = User::where('role', 'teacher', 'status')->latest()->get();
        // dd($data);

        $query = User::where('role', 'teacher', 'status')->latest('ic');
        $users = $query->get();
        $data['user'] = $users;

        return view('admin.teacher.list', compact('users'));
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
                'email' => 'required|email|ends_with:@gmail.com',


            ],
            [
                'ic.digits' => 'IC number must be exactly 12 digits.',
                'email.ends_with' => 'Email must end with @gmail.com.',
            ]
        );
        $plainPassword = $request->password;
        $ic = $request->ic;

        // Check if the IC already exists
        if (User::where('ic', $ic)->exists()) {
            return redirect()->route('teacher.read')->with('error', 'User with this IC already exists.');
        }


        // If not exists, proceed to create
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->ic = $ic;
        $user->password = Hash::make($plainPassword); // Admin sets an initial password
        $user->password_changed = false;
        $user->role = 'teacher';
        $user->status = 'active';
        $user->save();

        // 🔔 Send notification email to teacher
        $user->notify(new TeacherRegister($user, $plainPassword));

        return redirect()->route('teacher.read')->with('success', 'New Teacher Added Successfully');
    }




    // public function read()
    // {
    //     $data['teachers'] = User::where('role', 'teacher', 'status')->latest()->get();
    //     dd($data);

    //     // $query = User::where('role', 'teacher', 'status')->latest('id');
    //     // $user = $query->get();
    //     // $data['user'] = $user;

    //     // return view('admin.teacher.list', $data);
    // }

    // public function edit($ic)
    // {

    //     $data['teachers'] = User::find($ic);
    //     return view('admin.teacher.edit_teacher', $data);
    // }

    public function update(Request $request, $ic)
    {
        $request->validate([
            'ic' => 'required|digits:12',
            'email' => 'required|email|ends_with:gmail.com',
        ], [
            'ic.digits' => 'IC number must be exactly 12 digits.',
            'email.ends_with' => 'Email must end with @gmail.com.',
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
            $request->role === $user->role &&
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

        // Check if password_changes is 0 (indicating first-time login or password not changed yet)
        if ($user->password_changed === 0 && $emailChanged) {
            // Send email to the user about the email change (can be the same email sent during registration)
            $plainPassword = 'This could be a default password or a specific message if needed';
            $user->notify(new TeacherRegister($user, $plainPassword));  // Assuming this is the notification used to inform users
        }

        return redirect()->route('teacher.read')->with('success', 'Teacher Updated Successfully');
    }

    public function login()
    {
        return view('teacher.login');
    }


    public function authenticate(Request $request)
    {
        $request->validate([
            'ic' => 'required|digits:8',
            'password' => 'required',
        ]);

        $teachers = User::where('ic', 'like', $request->ic . '%')->get();

        foreach ($teachers as $teacher) {
            $teacherICFirst8 = substr($teacher->ic, 0, 8);
            $teacherICLast4 = substr($teacher->ic, -4);

            if (
                $teacherICFirst8 === $request->ic &&
                Hash::check($request->password, $teacher->password) &&
                $teacher->status === 'active' &&
                $teacher->role === 'teacher'
            ) {
                Auth::guard('teacher')->login($teacher);

                // ⛔ Check kalau cikgu belum tukar password
                if (!$teacher->password_changed) {
                    return redirect()->route('teacher.change_password');
                }

                return redirect()->route('teacher.dashboard');
            }
        }

        return redirect()->route('teacher.login')->with('error', 'Invalid IC or password.');
    }

    public function logout()
    {
        Auth::guard('teacher')->logout();
        return redirect()->route('teacher.login')->with('success', 'Logout successfully.');
    }

    public function dashboard()
    {
        return view('teacher.dashboard');
    }

    public function changePassword()
    {
        return view('teacher.change_password');
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
        $teacher = User::find(Auth::guard('teacher')->id());

        // Check if the old password matches the stored password
        if (!Hash::check($request->old_password, $teacher->password)) {
            return back()->with('error', 'The old password is incorrect.'); // If old password is wrong, show error
        }
        // Check if the new password is the same as the old password
        if (Hash::check($request->password, $teacher->password)) {
            return back()->with('error', 'The new password cannot be the same as the old password.'); // If new password is same as old, show error
        }


        try {
            // Update password if old password is correct
            $teacher->password = Hash::make($request->password); // Hash the new password
            $teacher->password_changed = true; // Mark that the password has been updated
            $teacher->save(); // Save the changes
        } catch (\Exception $e) {
            // Return with error message if something goes wrong during the save process
            return back()->with('error', 'Unable to update the password. Please try again later.');
        }

        // Redirect with success message
        return redirect()->route('teacher.dashboard')->with('success', 'Password updated successfully.');
    }

    //Forgot Passwoord Section
    public function showForgotPasswordForm()
    {
        return view('teacher.forgetpassword');
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
            $user->notify(new TeacherForgotPassword($user, $token));

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

        return view('teacher.resetpassword', [
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


        // dd([
        //     'input_ic' => $request->ic,
        //     'input_token' => $request->token,
        //     'db_record' => $record,
        //     'record_token' => $record ? $record->token : null,
        //     'token_match' => $record ? $request->token === $record->token : null,
        // ]);
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
        return redirect()->route('teacher.login')->with('success', 'Password has been reset!');
    }
}
