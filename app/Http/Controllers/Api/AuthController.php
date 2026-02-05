<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Officer;
use App\Models\Mentor;
use App\Models\Supervisor;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $username = $request->username;
        $password = $request->password;

        // 1. Try Officer
        $user = Officer::where('username', $username)->first();
        if ($user && Hash::check($password, $user->password)) {
            $token = $user->createToken('auth_token', ['officer'])->plainTextToken;
            return $this->respondWithToken($token, $user, 'officer');
        }

        // 2. Try Mentor
        $user = Mentor::where('username', $username)->first();
        if ($user && Hash::check($password, $user->password)) {
            $token = $user->createToken('auth_token', ['mentor'])->plainTextToken;
            return $this->respondWithToken($token, $user, 'mentor');
        }

        // 3. Try Supervisor
        $user = Supervisor::where('username', $username)->first();
        if ($user && Hash::check($password, $user->password)) {
            $token = $user->createToken('auth_token', ['supervisor'])->plainTextToken;
            return $this->respondWithToken($token, $user, 'supervisor');
        }

        // 4. Try Student (username = student_id, password = national_id)
        $user = Student::where('student_id', $username)->first();
        if ($user && $user->national_id === $password) {
            // Check password expiration
            if ($user->password_expires_at && now()->greaterThan($user->password_expires_at)) {
                return response()->json([
                    'message' => 'Your password/access has expired. Please contact the officer.'
                ], 401);
            }

            $token = $user->createToken('auth_token', ['student'])->plainTextToken;
            return $this->respondWithToken($token, $user, 'student');
        }

        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }

    private function respondWithToken($token, $user, $role)
    {
        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'role' => $role
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
