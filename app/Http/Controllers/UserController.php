<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Http\Requests\UserReq;
class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function add(UserReq $request)
    {
        $request->validated();
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->salary = $request->salary;
        $user->save();
        return response()->json([
            "id"=>$user->id,
        ]);
    }

    public function edit(UserReq $request)
    {
        $user = User::find($request->id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->salary = $request->salary;
        $user->save();
    }

    public function delete(Request $request)
    {
        User::find($request->id)->delete();
    }
}
