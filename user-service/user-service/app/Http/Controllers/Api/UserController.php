<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // Register User (POST /api/users)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:creator,buyer,admin',
            'bio' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'bio' => $request->bio,
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user->toApiArray()
        ], 201);
    }

    // Get User Profile (GET /api/users/{id})
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user->toApiArray());
    }

    // Update User Profile (PUT /api/users/{id})
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|max:255|unique:users,username,' . $id,
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
            'role' => 'sometimes|in:creator,buyer,admin',
            'bio' => 'nullable|string',
            'is_verified' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update allowed fields
        $updateData = $request->only(['name', 'username', 'email', 'role', 'bio']);
        
        // Handle is_verified specifically
        if ($request->has('is_verified')) {
            $updateData['is_verified'] = $request->boolean('is_verified');
        }

        $user->update($updateData);

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user->toApiArray()
        ]);
    }

    // Get All Users (for admin/internal use)
    public function index()
    {
        try {
            $users = User::all();
            
            return response()->json($users->map(function ($user) {
                return $user->toApiArray();
            }));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error loading users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete User
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}