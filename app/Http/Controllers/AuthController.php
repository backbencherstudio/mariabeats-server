<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Traits\CommonTrait;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetOTP;

class AuthController extends Controller
{
    use CommonTrait;

    public function getUser(Request $request)
    {
        $user = User::find(auth()->user()->id);
        return $this->sendResponse($user);
    }

    public function register(Request $request)
    {
        try {
            $request->validate([
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users',
                    'password' => 'required|string|min:8', 
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken($request->email)->plainTextToken;

            return $this->sendResponse([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                    'email' => 'required|string|email',
                    'password' => 'required|string',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return $this->sendError('Invalid credentials', 401);
            }

            $token = $user->createToken($request->email)->plainTextToken;

            return $this->sendResponse([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'message' => 'Login Successfully!'
            ]);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function updateUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::find(auth()->user()->id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return $this->sendResponse(['message' => 'User updated successfully']);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return $this->sendResponse(['message' => 'Logged out']);
    }

    public function forgotPassword(Request $request)
    {
        try {
            $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->sendError('User not found');
        }

        // Generate a 6-digit OTP
        $otp = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        
        // Store OTP in the database
        $user->update([
            'password_reset_otp' => $otp,
            'password_reset_otp_expiry' => now()->addMinutes(10) // OTP valid for 10 minutes
        ]);

        // Send OTP via email
        Mail::to($user->email)->send(new PasswordResetOTP($otp));

            return $this->sendResponse([
                'message' => 'Password reset OTP has been sent to your email'
            ]);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function otpVerify(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'otp' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || $user->password_reset_otp !== $request->otp) {
            return $this->sendError('Invalid OTP');
        }

            return $this->sendResponse([
                'message' => 'OTP verified successfully'
            ]);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string|min:8',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return $this->sendError('User not found');
            }

            $user->update([
                'password' => Hash::make($request->password),
                'password_reset_otp' => null,
                'password_reset_otp_expiry' => null
            ]);

            return $this->sendResponse([
                'message' => 'Password reset successful'
            ]);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }
    
}
