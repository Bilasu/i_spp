<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Notifications\AdminForgotPassword;

class AdminController extends Controller
{
    //
    public function index()
    {
        return view('admin.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'ic' => 'required|digits:8',
            'password' => 'required',
        ]);

        $admins = User::where('ic', 'like', $request->ic . '%')->get();

        foreach ($admins as $admin) {
            $adminICFirst8 = substr($admin->ic, 0, 8);
            $adminICLast4 = substr($admin->ic, -4);

            if (
                $adminICFirst8 === $request->ic &&
                Hash::check($request->password, $admin->password) &&
                $admin->status === 'active' &&
                $admin->role === 'admin'
            ) {
                Auth::guard('admin')->login($admin); // ✅ Only after verification
                return redirect()->route('admin.dashboard')->with('success', 'Login successfully.');
            }
        }

        return redirect()->route('admin.login')->with('error', 'Invalid IC or password.');
    }


    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login')->with('success', 'Logout successfully.');
    }

    public function register()
    {

        $ic = '021230071645'; // Replace with dynamic $request->ic if needed

        // Check if IC already exists
        if (User::where('ic', $ic)->exists()) {
            return redirect()->route('admin.login')->with('error', 'User with this IC already exists.');
        }

        $user = new User();
        $user->name = 'FAIZAH BINTI SAMINGON';
        $user->ic = $ic;
        $user->email = 'nurulnabilahsuhud@gmail.com';
        $user->role = 'admin';
        $user->password = Hash::make('faizahadmin');
        $user->status = 'active';
        $user->save();

        return redirect()->route('admin.login')->with('success', 'User created successfully.');
    }

    public function showForgotPasswordForm()
    {
        return view('admin.forgetpassword');
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
                    'token' => bcrypt($token),
                    'created_at' => Carbon::now()
                ]
            );

            // 7. Send custom notification with temporary password + token
            $user->notify(new AdminForgotPassword($user, $token));

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

        return view('admin.resetpassword', [
            'token' => $token,
            'ic' => $request->ic, // Pass IC instead of email to the view
        ]);
    }

    public function passwordchange(Request $request)
    {
        $request->validate([
            'ic' => 'required|digits:12|exists:users,ic',
            'password' => 'required|confirmed|min:8',
            'token' => 'required',
        ], [
            'password.confirmed' => 'The password is not same.',
        ]);

        // Semak token wujud dalam table dan IC match
        $record = DB::table('password_reset_tokens')
            ->where('ic', $request->ic)
            ->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['token' => 'Invalid or expired token.']);
        }

        // Tukar password user
        $user = User::where('ic', $request->ic)->first();
        $user->password = $request->password;
        $user->save();

        // Debug untuk sahkan perubahan
        // dd([
        //     'IC' => $user->ic,
        //     'Saved Hash' => $user->password,
        //     'Valid Hash?' => Hash::check($request->password, $user->password),
        // ]);

        // Buang token selepas reset
        DB::table('password_reset_tokens')->where('ic', $request->ic)->delete();

        return redirect()->route('admin.login')->with('success', 'Password has been reset!');
    }


    // public function dashboard()
    // {
    //     return view('admin.dashboard');
    // }

    public function form()
    {
        return view('admin.form');
    }

    public function table()
    {
        return view('admin.table');
    }
}
