<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users=User::all();
        return response()->json([
            "users"=>new UserResource($users),
        ], 200);
    }



    public function store(Request $request)
    {
        // this user will be active and vertified
        $data=$request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'user_name' => 'required|string|unique:users,user_name',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);
        $data['password'] = Hash::make($data['password']);
        $data['is_active']=1;
        $data['email_verified_at'] = Carbon::now()->format('Y-m-d H:i:s');
        $user=User::create($data);
        return response()->json([
            "success"=>"user added successfull",
            "user"=>new UserResource($user),
        ], 200);
    }



    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'user_name' => 'required|string|unique:users,user_name,' . $user->id,
            'email' => 'required|string|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
        ]);
        $data['is_active'] = $request->is_active;
        $data['password'] =isset($request->password)? Hash::make($request->password):$user->password;
        // dd($user->password);
        $user->update($data);
        return response()->json([
            "success"=>"user updated successfull",
            "user"=>new UserResource($user),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json([
            "success"=>"user deleted successfull",
        ], 200);
    }
}
