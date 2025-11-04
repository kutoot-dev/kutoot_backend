<?php

namespace App\Http\Controllers\WEB\Seller\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\AdminForgetPassword;
use App\Helpers\MailHelper;
use App\Models\Vendor;
use App\Models\User;
use App\Models\EmailTemplate;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class SellerForgotPasswordController extends Controller
{
    public function forgetPassword()
    {
        $setting = Setting::first();
        return view('seller.auth.forget', compact('setting')); // seller view instead of admin
    }

public function sendForgetPassword(Request $request)
{
    $request->validate([
        'email' => 'required|email'
    ]);

    $vendor = Vendor::whereHas('user', function($q) use ($request) {
        $q->where('email', $request->email);
    })->first();

    if (!$vendor) {
        return back()->with('error', 'Email not found in our records');
    }

    // Generate token
    $token = Str::random(64);
    $vendor->forget_password_token = $token;
    $vendor->save();

    // Build reset link
    $resetLink = route('seller.reset.password', $token);

    // Send email (you can create a Mailable instead of raw Mail::send)
    Mail::raw("Click here to reset your password: $resetLink", function ($message) use ($request) {
        $message->to($request->email)
                ->subject('Reset Your Password');
    });

    return back()->with('success', 'Password reset link has been sent to your email');
}

public function resetPassword($token)
{
    $vendor = Vendor::where('forget_password_token', $token)->first();
    if ($vendor) {
        $setting = Setting::first();
        return view('seller.auth.reset', compact('vendor', 'token', 'setting'));
    } else {
        return redirect()->route('seller.forgot-password')->with('error', 'Invalid token');
    }
}


public function storeResetData(Request $request, $token)
{
    $rules = [
        'password' => 'required|confirmed|min:4'
    ];

    $customMessages = [
        'password.required' => trans('admin_validation.Password is required'),
        'password.confirmed' => trans('admin_validation.Password does not match'),
        'password.min' => trans('admin_validation.Password must be at least 4 characters'),
    ];

    $this->validate($request, $rules, $customMessages);

    $vendor = Vendor::where('forget_password_token', $token)->first();

    if ($vendor) {
        $vendor->user->password = Hash::make($request->password);
        $vendor->user->save();

        $vendor->forget_password_token = null;
        $vendor->save();

        return redirect()->route('seller.login')
            ->with('success', trans('admin_validation.Password Reset Successfully'));
    }

    return redirect()->back()
        ->withErrors(['token' => trans('admin_validation.Invalid or expired token')]);
}

}
