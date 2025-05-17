<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //get all users
        $users = User::paginate(20);
        $users->withPath('/users');

        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|min:3',
            'email'     => 'required|email|unique:users,email',
            'status'    => 'required',
            'password'  => ['required', 'confirmed', Rules\Password::defaults()],
            'role'      => 'required|min:2',
        ]);

        //buat user
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'status'    => $request->status,
            'password'  => bcrypt($request->password),
        ]);
        $user->assignRole($request->role);

        return response()->json($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $user = User::find($id);
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $request->validate([
            'name'      => 'required|min:3',
            'email'     => 'required|email',
            'status'    => 'required',
            // 'password'  => ['required', 'confirmed', Rules\Password::defaults()],
            'role'      => 'required|min:2',
        ]);

        $user = User::find($id);
        $user->update([
            'name'      => $request->name,
            'email'     => $request->email,
            'status'    => $request->status,
            // 'password'  => bcrypt($request->password),
        ]);

        //hapus role lama
        if ($user->user_roles) {
            foreach ($user->user_roles as $role) {
                $user->removeRole($role);
            }
        }

        $user->assignRole($request->role);

        return response()->json($user);
    }

    /**
     * Update password user.
     */
    public function updatePassword(Request $request, string $id)
    {
        //
        $request->validate([
            'password'  => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::find($id);

        $user->update([
            'password'  => bcrypt($request->password),
        ]);

        return response()->json($user);
    }


    /**
     * Update avatar user.
     */
    public function updateAvatar(Request $request, string $id)
    {
        //
        $request->validate([
            'image'    => 'required|image|mimes:jpeg,png,jpg,webp,svg|max:1048',
        ]);

        $user = User::find($id);

        if ($request->hasFile('image')) {

            //hapus avatar lama
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $file = $request->file('image');
            $path = $file->store('avatar', 'public');
            $user->update([
                'avatar' => $path,
            ]);

            return response()->json($user);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //hapus user
        $user = User::find($id);
        $user->delete();
    }
}
