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

        // Cari user yang IC bermula dengan 8 digit
        $admin = User::where('ic', 'like', $request->ic . '%')
            ->where('role', 'admin')
            ->where('status', 'active')
            ->first();

        if ($admin) {
            $adminICFirst8 = substr($admin->ic, 0, 8);

            if (
                $adminICFirst8 === $request->ic &&
                Hash::check($request->password, $admin->password)
            ) {
                Auth::guard('admin')->login($admin);
                return redirect()->route('admin.dashboard')->with('success', 'Login successfully.');
            }
        }

        // dd([
        //     'input_ic' => $request->ic,
        //     'admin_found' => $admin,
        //     'admin_ic_first8' => $admin ? substr($admin->ic, 0, 8) : null,
        //     'compare_ic' => $admin ? substr($admin->ic, 0, 8) === $request->ic : null,
        //     'password_input' => $request->password,
        //     'password_match' => $admin ? Hash::check($request->password, $admin->password) : null,
        // ]);


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
        $user = User::where('ic', $request->ic)->first();

        if (!$user) {
            return back()->with('error', 'Invalid or non-registered IC.');
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['ic' => $user->ic],
            [
                'token' => $token, // Simpan token tanpa di-hash
                'created_at' => Carbon::now()
            ]
        );

        $checkToken = DB::table('password_reset_tokens')->where('ic', $user->ic)->first();

        try {
            $user->notify(new AdminForgotPassword($user, $token));

            // ✅ DEBUG BLOCK - Uncomment if needed
            /*
        dd([
            'input_ic' => $request->ic,
            'user_found' => $user,
            'generated_token' => $token,
            'saved_token_row' => $checkToken,
            'notification_status' => 'Sent Successfully!',
        ]);
        */

            return back()->with('success', 'We have emailed your account credentials!');
        } catch (\Exception $e) {

            // ❌ DEBUG BLOCK - Uncomment if needed
            /*
        dd([
            'input_ic' => $request->ic,
            'user_found' => $user,
            'generated_token' => $token,
            'saved_token_row' => $checkToken,
            'notification_error' => $e->getMessage(),
        ]);
        */

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
