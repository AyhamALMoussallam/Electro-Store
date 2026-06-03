<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class UserController extends Controller
{
    // Sign Up
    public function signup(Request $request)
    {
        $googleAccount = User::where('email', $request->email)
            ->whereNotNull('google_id')
            ->first();

        if ($googleAccount) {
            return response()->json([
                'message' => 'This email is already registered with Google. Please use Sign In with Google.',
            ], 422);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:50',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|digits:10',
                'password' => 'required|string|min:6|max:35|confirmed',
            ],
            [
                'name.required' => 'Name is required.',
                'email.required' => 'Email is required.',
                'email.email' => 'Please enter a valid email address.',
                'email.unique' => 'This email is already registered.',
                'phone.required' => 'Phone number is required.',
                'phone.digits' => 'Phone number must be exactly 10 digits (e.g. 09XXXXXXXX).',
                'password.required' => 'Password is required.',
                'password.min' => 'Password must be at least 6 characters.',
                'password.confirmed' => 'Password and confirmation do not match.',
            ]
        );

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => 0,
            ]);
            $user->sendEmailVerificationNotification();


            return $this->created([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $user->role,
                    'created_at' => $user->created_at,
                ],
            ], 'Registration successful! A verification email has been sent to your inbox.');
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while registering the user.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // LogIn
    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required|string',
            ],
            [
                'email.required' => 'Email is required.',
                'email.email' => 'Please enter a valid email address.',
                'password.required' => 'Password is required.',
            ]
        );

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'No account found with this email.',
            ], 401);
        }

        if ($user->google_id) {
            return response()->json([
                'message' => 'This email is already registered with Google. Please use Sign In with Google.',
            ], 401);
        }

        if (! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Password is incorrect.',
            ], 401);
        }

        if (! $user->canSignInWithPassword()) {
            $user->sendEmailVerificationNotification();

            return response()->json([
                'message' => 'Your email is not verified yet. We have sent a verification link to your inbox. Please verify your account, then sign in again.',
            ], 403);
        }

        // Create token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'created_at' => $user->created_at,
            ],
        ], 200);
    }

    // LogOut
    public function logout(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'message' => 'User not found'
                ], 401);
            }

            // Revoke all tokens
            $user->tokens()->delete();

            return response()->json([
                'message' => 'Logged out successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while trying to logout',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Get current user profile
    public function getCurrentUser(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]
        ]);
    }

    // Change password (requires current password)
    public function changePassword(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make(
            $request->all(),
            [
                'current_password' => 'required|string',
                'password' => 'required|string|min:6|confirmed',
            ],
            [
                'current_password.required' => 'Please enter your current password.',
                'password.required' => 'Please enter a new password.',
                'password.min' => 'New password must be at least 6 characters.',
                'password.confirmed' => 'New password and confirmation do not match.',
            ]
        );

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect.',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Password updated successfully.',
        ], 200);
    }

    // Update user profile
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
                'phone' => 'nullable|digits:10',
            ],
            [
                'phone.digits' => 'Phone number must be exactly 10 digits (e.g. 09XXXXXXXX).',
                'email.unique' => 'This email is already in use.',
            ]
        );

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $updateData = [];

        if ($request->has('name')) {
            $updateData['name'] = $request->name;
        }

        if ($request->has('email')) {
            $updateData['email'] = $request->email;
        }

        if ($request->has('phone')) {
            $updateData['phone'] = $request->filled('phone')
                ? $request->phone
                : null;
        }

        if (! empty($updateData)) {
            $user->update($updateData);
        }

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
        ], 200);
    }

    // Delete user account
    public function deleteAccount(Request $request)
    {
        $user = $request->user();
        
        // Revoke all tokens first
        $user->tokens()->delete();
        
        // Delete user
        $user->delete();

        return response()->json([
            'message' => 'Account deleted successfully',
        ], 200);
    }
}