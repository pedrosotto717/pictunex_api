
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        // Validate client ID
        if ($request->header('Client-Id') !== config('app.client_id')) {
            return response()->json(['error' => 'Invalid client ID'], 401);
        }

        // Decode user data
        $userData = json_decode(base64_decode($request->user), true);

        $validator = Validator::make($userData, [
            'firstName' => 'required|string|max:30',
            'lastName' => 'required|string|max:30',
            'userName' => 'required|string|max:30|unique:users,username',
            'passWord' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Check if user already exists
        if (User::where('username', $userData['userName'])->exists()) {
            return response()->json(['error' => 'User already exists'], 401);
        }

        $user = User::create([
            'first_name' => $userData['firstName'],
            'last_name' => $userData['lastName'],
            'username' => $userData['userName'],
            'password' => Hash::make($userData['passWord']),
            'user_id' => hash('sha256', $userData['userName'] . time())
        ]);

        return response()->json(['statusCode' => 201], 201);
    }

    /**
     * Get a JWT via given credentials
     */
    public function token(Request $request)
    {
        $credentials = base64_decode($request->header('Authentication'));
        list($username, $password) = explode(':', $credentials);

        $user = User::where('username', $username)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $token = JWTAuth::fromUser($user);

        return response()->json(['access_token' => $token]);
    }

    /**
     * Verify token
     */
    public function verify(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            
            if ($request->input('user') == true) {
                $userData = $user->toArray();
                $userData['code'] = 200;
                return response()->json($userData);
            }
            
            return response()->json(['code' => 200]);
        } catch (\Exception $e) {
            return response()->json(['code' => -1], 401);
        }
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $userData = json_decode(base64_decode($request->user), true);

            $updateData = [
                'first_name' => $userData['firstName'],
                'last_name' => $userData['lastName']
            ];

            // Handle password update
            if (isset($userData['passWord']) && $userData['passWord'] !== '0' && 
                isset($userData['newPassword']) && $userData['newPassword'] !== '0') {
                
                if (!Hash::check($userData['passWord'], $user->password)) {
                    return response()->json(['error' => 'Invalid current password'], 401);
                }
                
                $updateData['password'] = Hash::make($userData['newPassword']);
            }

            // Handle image upload
            if ($request->hasFile('ico')) {
                $file = $request->file('ico');
                $filename = $user->user_id . '.jpg';
                $path = $file->storeAs('public/users', $filename);
                $updateData['ico'] = '/storage/users/' . $filename;
            }

            $user->update($updateData);

            return response()->json(['message' => 'User updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Bad request'], 400);
        }
    }
}
