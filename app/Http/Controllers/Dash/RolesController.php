<?php

namespace App\Http\Controllers\Dash;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //daftar roles
        $roles = Role::all();

        // Loop untuk menghitung jumlah user untuk setiap role
        $rolesWithUserCount = $roles->map(function ($role) {
            return [
                'id'            => $role->id,
                'name'          => $role->name,
                'guard_name'    => $role->guard_name,
                'user_count'    => $role->users() ? $role->users()->count() : 0, // Menghitung jumlah user
                'permissions'   => $role->permissions->map(function ($permission) {
                    return $permission->name;
                }), // Mengambil daftar permissions
            ];
        });

        return response()->json($rolesWithUserCount);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'permissions' => 'required|array',
        ]);

        //simpan role
        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);
        $role->syncPermissions($request->permissions);

        return response()->json($role);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $role = Role::findByName($id, 'web');

        return response()->json($role);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $request->validate([
            'name' => 'required',
            'permissions' => 'required|array',
        ]);

        //jika id = 'admin'
        if ($id == 'admin') {
            return response()->json(['message' => 'Tidak dapat ubah role admin']);
        }

        $role = Role::findByName($id, 'web');
        $role->name = $request->name;
        $role->syncPermissions($request->permissions);
        $role->save();

        return response()->json($role);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        //jika id = 'admin'
        if ($id == 'admin') {
            return response()->json(['message' => 'Tidak dapat menghapus role admin']);
        }

        //remove role by name
        $role = Role::findByName($id, 'web');
        $role->delete();
    }
}
