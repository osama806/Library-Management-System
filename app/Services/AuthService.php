<?php

namespace App\Services;

use App\Models\User;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    use ResponseTrait;

    /**
     * Create a new user in storage.
     * @param array $data
     * @throws \Exception
     * @return bool[]
     */
    public function register(array $data)
    {
        try {
            User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'as_admin'  =>  $data['as_admin'] ?? 'no'
            ]);
            return ['status'    =>      true];
        } catch (Exception $e) {
            Log::error('Error register user: ' . $e->getMessage());
            return ['status'    =>  false, 'msg'    =>  'Unable to c. Please try again later.', 'code'  => 500];
        }
    }

    /**
     * Check if user authorize or unAuthorize
     * @param array $data
     * @throws \Tymon\JWTAuth\Exceptions\JWTException
     * @return array
     */
    public function login(array $data)
    {
        $credentials = [
            "email"         =>      $data['email'],
            "password"      =>      $data['password']
        ];
        $token = JWTAuth::attempt($credentials);
        if (!$token) {
            return ['status'    =>  false, 'msg'    =>  "username or password is incorrect", 'code' =>  401];
        }
        return ["status"    =>  true, "token"   =>      $token];
    }

    /**
     * Get user profile data
     * @return array
     */
    public function show()
    {
        $user = Auth::user();
        $data = [
            "name"          =>      $user->name,
            "email"         =>      $user->email,
            "as_admin"      =>      $user->as_admin == 'yes' ? 'Administrator' : 'Non-Administrator'
        ];
        return ['status'    =>      true, 'profile'     =>      $data];
    }

    /**
     * Update user profile in storage
     * @param array $data
     * @throws \Exception
     * @return bool[]
     */
    public function updateProfile(array $data)
    {
        $user = Auth::user();
        try {
            $user->name = $data['name'];
            $user->password = bcrypt($data['password']);
            $user->as_admin = $data['as_admin'] ?? $user->as_admin;
            $user->save();
            return ['status'    =>  true];
        } catch (Exception $e) {
            Log::error('Error update profile: ' . $e->getMessage());
            return ['status'    =>  false, 'msg'    =>  'Failed update profile for user. Try again', 'code' =>  500];
        }
    }

    /**
     * Delete user from storage.
     * @throws \Exception
     * @return bool[]
     */
    public function deleteUser()
    {
        $user = Auth::user();
        try {
            if (JWTAuth::parseToken()->check()) {
                JWTAuth::invalidate(JWTAuth::getToken());
            }
            $user->delete();
            return ['status'    =>  true];
        } catch (TokenInvalidException $e) {
            Log::error('Error Invalid token: ' . $e->getMessage());
            return ['status' => false, 'msg' => 'Invalid token.', 'code'   => 401];
        } catch (JWTException $e) {
            Log::error('Error invalidate token: ' . $e->getMessage());
            return ['status' => false, 'msg' => 'Failed to invalidate token, please try again.', 'code' => 500];
        } catch (Exception $e) {
            Log::error('Error delete user: ' . $e->getMessage());
            return ['status' => false, 'msg' => 'Failed to delete user, please try again.', 'code'  => 500];
        }
    }
}
