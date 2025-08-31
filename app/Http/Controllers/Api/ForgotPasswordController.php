<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Mail\SendOtpMail;

class ForgotPasswordController extends Controller
{
    // Send OTP to email
    public function sendOtp(Request $request)
    {
       // dd($request->all());
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $otp = rand(100000, 999999);

        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $otp, 'created_at' => Carbon::now()]
        );

        Mail::to($request->email)->send(new SendOtpMail($otp));

        return response()->json([
            'status' => 200,
            'message' => 'OTP has been sent to your email.',
        ]);
    }

    // Reset password using OTP
    public function resetPasswordWithOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);
    
        $reset = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();
        if (
            !$reset ||
            $reset->token !== $request->otp
        ) {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid or expired OTP.',
            ]);
        }
        User::where('email', $request->email)
            ->update([
                'password' => Hash::make($request->password)
            ]);

        DB::table('password_resets')
            ->where('email', $request->email)
            ->delete();
    
        return response()->json([
            'status' => 200,
            'message' => 'Password has been successfully reset.',
        ]);
    }
}
