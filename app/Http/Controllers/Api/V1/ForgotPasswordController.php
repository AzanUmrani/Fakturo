<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'language' => 'required|string|in:en,sk,cz,ua',
        ]);

        // Generate a 6 digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Generate a reset ID
        $resetId = Str::uuid();

        // Store the reset code
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $code,
                'reset_id' => $resetId,
                'created_at' => Carbon::now()
            ]
        );

        // Send the email with specified language
        Mail::to($request->email)->send(new ResetPasswordCode($code, $request->language));

        return response()->json([
            'message' => 'Reset code sent successfully',
            'reset_id' => $resetId
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'reset_id' => 'required|string',
            'code' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed'
        ]);

        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('reset_id', $request->reset_id)
            ->where('token', $request->code)
            ->where('created_at', '>', Carbon::now()->subHours(1))
            ->first();

        if (!$reset) {
            return response()->json([
                'message' => 'Invalid reset code or expired',
            ], 400);
        }

        // Update the user's password
        $user = User::where('email', $request->email)->first();
        $user->password = bcrypt($request->password);
        $user->save();

        // Delete the used token
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return response()->json([
            'message' => 'Password has been reset successfully'
        ]);
    }
}

