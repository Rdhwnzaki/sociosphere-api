<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class UserController extends Controller
{
    public function updateProfile(Request $request)
    {

        Log::info('Request Data:', $request->all());

        $user = Auth::user();

        if (!$user instanceof User) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $validatedData = $request->validate([
            'username' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images', 'public');
            $validatedData['image'] = $path;
        }

        $user->username = $validatedData['username'] ?? $user->username;
        $user->bio = $validatedData['bio'] ?? $user->bio;
        $user->image = $validatedData['image'] ?? $user->image;

        if ($user->save()) {
            return response()->json([
                'message' => 'Profile updated successfully',
                'user' => $user
            ]);
        } else {
            return response()->json(['message' => 'Failed to update profile'], 500);
        }
    }

    public function getProfile(Request $request)
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        return response()->json([
            'message' => 'Profile fetched successfully',
            'user' => $user
        ], 200);
    }
}
